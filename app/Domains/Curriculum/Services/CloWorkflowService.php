<?php

namespace App\Domains\Curriculum\Services;

use App\Domains\Academic\Models\Program;
use App\Domains\Curriculum\Models\Course;
use App\Domains\Curriculum\Models\CourseOutcome;
use App\Domains\Curriculum\Models\CoPoMapping;
use App\Domains\Curriculum\Models\ProgramOutcome;
use App\Domains\Curriculum\Models\ProgramSpecificOutcome;
use App\Domains\Attainment\Models\StudentAssessment;
use App\Domains\Attainment\Models\AttainmentResult;
use ZipArchive;
use Exception;

class CloWorkflowService
{
    /**
     * Parse an uploaded or existing XLSX file (such as Ongoing Performance.xlsx).
     */
    public function parsePerformanceFile(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception("Unable to open XLSX file: {$filePath}");
        }

        // 1. Shared Strings Table
        $sharedStrings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml !== false) {
            $xml = simplexml_load_string($ssXml);
            if ($xml) {
                foreach ($xml->si as $si) {
                    $text = '';
                    if (isset($si->t)) {
                        $text = (string)$si->t;
                    } else {
                        foreach ($si->r as $r) {
                            $text .= (string)$r->t;
                        }
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        // 2. Read first worksheet
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            $zip->close();
            throw new Exception("Sheet1 not found in XLSX.");
        }

        $xml = simplexml_load_string($sheetXml);
        $zip->close();

        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $rIdx = (int)$row['r'];
            $rowVals = [];
            foreach ($row->c as $c) {
                $cellRef = (string)$c['r'];
                preg_match('/^([A-Z]+)(\d+)$/', $cellRef, $m);
                $col = $m[1] ?? $cellRef;
                $t = (string)$c['t'];
                $val = null;
                if (isset($c->v)) {
                    $rawVal = (string)$c->v;
                    if ($t === 's') {
                        $val = $sharedStrings[(int)$rawVal] ?? $rawVal;
                    } else {
                        $val = $rawVal;
                    }
                }
                $rowVals[$col] = $val;
            }
            $rows[$rIdx] = $rowVals;
        }

        // Extract column headers and questions
        $questions = $this->extractQuestionsFromRows($rows);

        // Extract student marks
        $students = [];
        // Student data starts at row 4
        for ($r = 4; $r <= count($rows); $r++) {
            if (!isset($rows[$r]) || empty($rows[$r]['B'])) {
                continue;
            }

            $sNo = $rows[$r]['A'] ?? ($r - 3);
            $rollNo = trim((string)$rows[$r]['B']);
            $name = trim((string)($rows[$r]['C'] ?? "Student {$sNo}"));

            $marks = [];
            foreach ($questions as $q) {
                $colKey = $q['col'];
                $rawVal = $rows[$r][$colKey] ?? null;
                if ($rawVal === null || trim((string)$rawVal) === '' || strtoupper(trim((string)$rawVal)) === 'NA') {
                    $marks[$colKey] = null;
                } else {
                    $marks[$colKey] = is_numeric($rawVal) ? (float)$rawVal : (string)$rawVal;
                }
            }

            $students[] = [
                's_no' => $sNo,
                'roll_no' => $rollNo,
                'name' => $name,
                'marks' => $marks,
            ];
        }

        return [
            'file_name' => basename($filePath),
            'total_students' => count($students),
            'questions' => $questions,
            'students' => $students,
            'default_mappings' => $this->getDefaultUec519Mappings(),
        ];
    }

    /**
     * Extract questions with clean names and max marks from sheet rows.
     */
    protected function extractQuestionsFromRows(array $rows): array
    {
        $questions = [];

        // Known question columns from Ongoing Performance
        $colDefs = [
            'D' => ['section' => 'MST', 'name' => 'MST Q1', 'max_marks' => 5],
            'E' => ['section' => 'MST', 'name' => 'MST Q2', 'max_marks' => 5],
            'F' => ['section' => 'MST', 'name' => 'MST Q3', 'max_marks' => 5],
            'G' => ['section' => 'MST', 'name' => 'MST Q4', 'max_marks' => 5],
            'H' => ['section' => 'MST', 'name' => 'MST Q5', 'max_marks' => 5],
            'I' => ['section' => 'MST', 'name' => 'MST Q6a', 'max_marks' => 2],
            'J' => ['section' => 'MST', 'name' => 'MST Q6b', 'max_marks' => 3],
            'L' => ['section' => 'Sessional', 'name' => 'Quiz', 'max_marks' => 10],
            'M' => ['section' => 'Sessional', 'name' => 'Tutorial', 'max_marks' => 5],
            'N' => ['section' => 'Lab', 'name' => 'Lab File', 'max_marks' => 3],
            'O' => ['section' => 'Lab', 'name' => 'Lab Day-to-Day', 'max_marks' => 2],
            'P' => ['section' => 'Lab', 'name' => 'Lab Performance', 'max_marks' => 5],
            'Q' => ['section' => 'Lab', 'name' => 'Lab Viva', 'max_marks' => 5],
            'S' => ['section' => 'EST', 'name' => 'EST Q1', 'max_marks' => 10],
            'T' => ['section' => 'EST', 'name' => 'EST Q2a', 'max_marks' => 5],
            'U' => ['section' => 'EST', 'name' => 'EST Q2b', 'max_marks' => 5],
            'V' => ['section' => 'EST', 'name' => 'EST Q3', 'max_marks' => 10],
            'W' => ['section' => 'EST', 'name' => 'EST Q4', 'max_marks' => 10],
            'X' => ['section' => 'EST', 'name' => 'EST Q5a', 'max_marks' => 5],
            'Y' => ['section' => 'EST', 'name' => 'EST Q5b', 'max_marks' => 5],
            'Z' => ['section' => 'EST', 'name' => 'EST Q6', 'max_marks' => 10],
            'AA' => ['section' => 'EST', 'name' => 'EST Q7', 'max_marks' => 10],
            'AB' => ['section' => 'EST', 'name' => 'EST Q8a', 'max_marks' => 5],
            'AC' => ['section' => 'EST', 'name' => 'EST Q8b', 'max_marks' => 5],
            'AD' => ['section' => 'EST', 'name' => 'EST Q9', 'max_marks' => 10],
        ];

        // Also check row 2 and row 3 for titles
        foreach ($colDefs as $col => $info) {
            $label = $info['name'];
            $maxMarks = $info['max_marks'];

            if (isset($rows[2][$col]) && !empty($rows[2][$col])) {
                $rawHeader = (string)$rows[2][$col];
                if (preg_match('/\((\d+(?:\.\d+)?)\)/', $rawHeader, $m)) {
                    $maxMarks = (float)$m[1];
                }
            } elseif (isset($rows[3][$col]) && !empty($rows[3][$col])) {
                $rawHeader = (string)$rows[3][$col];
                if (preg_match('/\((\d+(?:\.\d+)?)\)/', $rawHeader, $m)) {
                    $maxMarks = (float)$m[1];
                }
            }

            $questions[] = [
                'col' => $col,
                'section' => $info['section'],
                'name' => $label,
                'max_marks' => $maxMarks,
            ];
        }

        return $questions;
    }

    /**
     * Default mappings from TIET UEC519 Tools for CLO Subject Evaluation document.
     */
    public function getDefaultUec519Mappings(): array
    {
        return [
            'CO1' => [
                'co_code' => 'CO1',
                'description' => 'Evaluate the performance of AM and FM modulator and demodulator.',
                'bloom_level' => 'Apply (L3)',
                'tool_name' => 'MST (Q1 + Q2)',
                'tool_type' => 'I', // Internal
                'cols' => ['D', 'E'],
                'marks_assigned' => 10,
                'target_pct' => 60,
                'indirect_score_5' => 3.55,
            ],
            'CO2' => [
                'co_code' => 'CO2',
                'description' => 'Analyze the concept of different pulse modulation techniques.',
                'bloom_level' => 'Analyze (L4)',
                'tool_name' => 'MST (Q6a, Q6b) + Lab Evaluation',
                'tool_type' => 'I', // Internal
                'cols' => ['I', 'J', 'N', 'O', 'P', 'Q'],
                'marks_assigned' => 20,
                'target_pct' => 60,
                'indirect_score_5' => 3.61,
            ],
            'CO3' => [
                'co_code' => 'CO3',
                'description' => 'Assess discrete PAM signals & baseband shaping in reducing ISI during transmission.',
                'bloom_level' => 'Evaluate (L5)',
                'tool_name' => 'MST Q4 + EST Q4',
                'tool_type' => 'E', // External
                'cols' => ['G', 'W'],
                'marks_assigned' => 15,
                'target_pct' => 50,
                'indirect_score_5' => 3.67,
            ],
            'CO4' => [
                'co_code' => 'CO4',
                'description' => 'Perform statistical analysis of transmitted and received modulated waveforms.',
                'bloom_level' => 'Analyze (L4)',
                'tool_name' => 'EST Q5b + EST Q6',
                'tool_type' => 'E', // External
                'cols' => ['Y', 'Z'],
                'marks_assigned' => 15,
                'target_pct' => 50,
                'indirect_score_5' => 3.69,
            ],
            'CO5' => [
                'co_code' => 'CO5',
                'description' => 'Evaluate performance of digital modulation techniques over AWGN channel.',
                'bloom_level' => 'Evaluate (L5)',
                'tool_name' => 'EST Q7 + EST Q8a',
                'tool_type' => 'E', // External
                'cols' => ['AA', 'AB'],
                'marks_assigned' => 15,
                'target_pct' => 50,
                'indirect_score_5' => 3.61,
            ],
            'CO6' => [
                'co_code' => 'CO6',
                'description' => 'Analyze concepts of channel coding to mitigate effects of interference and noise.',
                'bloom_level' => 'Analyze (L4)',
                'tool_name' => 'EST Q9',
                'tool_type' => 'E', // External
                'cols' => ['AD'],
                'marks_assigned' => 10,
                'target_pct' => 50,
                'indirect_score_5' => 3.75,
            ],
        ];
    }

    /**
     * Compute full CLO sheet metrics, student marks, and summary attainment.
     */
    public function calculateCloAttainment(array $students, array $coMappings): array
    {
        $coMetrics = [];
        $totalStudents = count($students);

        foreach ($coMappings as $coCode => $def) {
            $toolType = strtoupper($def['tool_type'] ?? 'I');
            $marksAssigned = (float)($def['marks_assigned'] ?? 10);
            
            // Default target multiplier: Internal = 60% (0.6), External = 50% (0.5)
            $targetPct = isset($def['target_pct']) ? ((float)$def['target_pct'] / 100) : ($toolType === 'E' ? 0.5 : 0.6);
            $targetValue = round($marksAssigned * $targetPct, 2);

            $coMetrics[$coCode] = [
                'co_code' => $coCode,
                'description' => $def['description'] ?? '',
                'bloom_level' => $def['bloom_level'] ?? 'L3',
                'tool_name' => $def['tool_name'] ?? 'Direct Assessment',
                'tool_type' => $toolType,
                'marks_assigned' => $marksAssigned,
                'target_pct' => round($targetPct * 100, 1),
                'target_value' => $targetValue,
                'total_students' => $totalStudents,
                'students_na' => 0,
                'students_considered' => 0,
                'students_above_target' => 0,
                'percentage_achieved' => 0,
                'attainment_level' => 0,
                'indirect_score_5' => (float)($def['indirect_score_5'] ?? 3.6),
                'indirect_score_3' => round((float)($def['indirect_score_5'] ?? 3.6) * 0.6, 3),
                'final_score' => 0,
                'cols' => $def['cols'] ?? [],
            ];
        }

        // Student-level marks computation
        $studentResults = [];
        foreach ($students as $s) {
            $sItem = [
                's_no' => $s['s_no'],
                'roll_no' => $s['roll_no'],
                'name' => $s['name'],
                'co_marks' => [],
            ];

            foreach ($coMappings as $coCode => $def) {
                $cols = $def['cols'] ?? [];
                $sum = 0;
                $hasValidMarks = false;

                foreach ($cols as $col) {
                    $m = $s['marks'][$col] ?? null;
                    if ($m !== null && trim((string)$m) !== '' && strtoupper(trim((string)$m)) !== 'NA') {
                        $sum += (float)$m;
                        $hasValidMarks = true;
                    }
                }

                if (!$hasValidMarks) {
                    $coMetrics[$coCode]['students_na']++;
                    $sItem['co_marks'][$coCode] = 'NA';
                } else {
                    $coMetrics[$coCode]['students_considered']++;
                    if ($sum >= $coMetrics[$coCode]['target_value']) {
                        $coMetrics[$coCode]['students_above_target']++;
                    }
                    $sItem['co_marks'][$coCode] = round($sum, 2);
                }
            }

            $studentResults[] = $sItem;
        }

        // Finalize attainment metrics
        $directSum = 0;
        $indirectSum = 0;
        $finalSum = 0;
        $coCount = count($coMetrics);

        foreach ($coMetrics as $coCode => &$m) {
            if ($m['students_considered'] > 0) {
                $m['percentage_achieved'] = round(($m['students_above_target'] / $m['students_considered']) * 100, 2);
            } else {
                $m['percentage_achieved'] = 0;
            }

            // NBA Attainment Scale: >=70% -> 3, >=50% -> 2, >=30% -> 1, <30% -> 0
            if ($m['percentage_achieved'] >= 70) {
                $m['attainment_level'] = 3;
            } elseif ($m['percentage_achieved'] >= 50) {
                $m['attainment_level'] = 2;
            } elseif ($m['percentage_achieved'] >= 30) {
                $m['attainment_level'] = 1;
            } else {
                $m['attainment_level'] = 0;
            }

            // Final score = Direct * 0.8 + Indirect * 0.2
            $m['final_score'] = round(($m['attainment_level'] * 0.8) + ($m['indirect_score_3'] * 0.2), 3);

            $directSum += $m['attainment_level'];
            $indirectSum += $m['indirect_score_3'];
            $finalSum += $m['final_score'];
        }
        unset($m);

        $summaryAverages = [
            'direct_average' => $coCount > 0 ? round($directSum / $coCount, 2) : 0,
            'indirect_average' => $coCount > 0 ? round($indirectSum / $coCount, 3) : 0,
            'final_average' => $coCount > 0 ? round($finalSum / $coCount, 3) : 0,
        ];

        return [
            'metrics' => array_values($coMetrics),
            'summary' => $summaryAverages,
            'students' => $studentResults,
        ];
    }

    /**
     * Compute Course-Level CLO-PO & CLO-PSO Mapping matrix and course direct correlation averages.
     */
    public function computeCloPoMapping(array $coPoRows, Program $program): array
    {
        $pos = ProgramOutcome::where('program_id', $program->id)->orderBy('po_code')->get();
        $psos = ProgramSpecificOutcome::where('program_id', $program->id)->orderBy('pso_code')->get();

        $poAverages = [];
        $psoAverages = [];

        foreach ($coPoRows as $row) {
            foreach ($pos as $po) {
                $val = $row['po'][$po->po_code] ?? null;
                if ($val !== null && is_numeric($val) && (int)$val > 0) {
                    $poAverages[$po->po_code][] = (int)$val;
                }
            }

            foreach ($psos as $pso) {
                $val = $row['pso'][$pso->pso_code] ?? null;
                if ($val !== null && is_numeric($val) && (int)$val > 0) {
                    $psoAverages[$pso->pso_code][] = (int)$val;
                }
            }
        }

        $courseAverages = [
            'po' => [],
            'pso' => [],
        ];

        foreach ($pos as $po) {
            $vals = $poAverages[$po->po_code] ?? [];
            $courseAverages['po'][$po->po_code] = count($vals) > 0 ? round(array_sum($vals) / count($vals), 2) : null;
        }

        foreach ($psos as $pso) {
            $vals = $psoAverages[$pso->pso_code] ?? [];
            $courseAverages['pso'][$pso->pso_code] = count($vals) > 0 ? round(array_sum($vals) / count($vals), 2) : null;
        }

        return [
            'pos' => $pos,
            'psos' => $psos,
            'matrix' => $coPoRows,
            'course_averages' => $courseAverages,
        ];
    }

    /**
     * Get Program Master Articulation Matrix with updated active course averages.
     */
    public function getMasterArticulationMatrix(Program $program, ?string $activeCourseCode = null, ?array $activeCourseAverages = null): array
    {
        $courses = Course::where('program_id', $program->id)->orderBy('semester')->orderBy('course_code')->get();
        $pos = ProgramOutcome::where('program_id', $program->id)->orderBy('po_code')->get();
        $psos = ProgramSpecificOutcome::where('program_id', $program->id)->orderBy('pso_code')->get();

        $matrixRows = [];
        $programPoSums = [];
        $programPsoSums = [];

        foreach ($courses as $c) {
            $poAvgs = [];
            $psoAvgs = [];

            // If this is the active course with freshly computed averages, use them!
            if ($activeCourseCode && strtoupper($c->course_code) === strtoupper($activeCourseCode) && $activeCourseAverages) {
                $poAvgs = $activeCourseAverages['po'] ?? [];
                $psoAvgs = $activeCourseAverages['pso'] ?? [];
            } else {
                // Read from co_po_mappings table
                $cos = $c->courseOutcomes;
                $coIds = $cos->pluck('id');
                $mappings = CoPoMapping::whereIn('course_outcome_id', $coIds)->get();

                foreach ($pos as $po) {
                    $vals = [];
                    foreach ($cos as $co) {
                        $m = $mappings->first(fn($map) => $map->course_outcome_id == $co->id && $map->target_type == 'PO' && $map->target_id == $po->id);
                        if ($m && $m->correlation_level > 0) {
                            $vals[] = $m->correlation_level;
                        }
                    }
                    $poAvgs[$po->po_code] = count($vals) > 0 ? round(array_sum($vals) / count($vals), 2) : null;
                }

                foreach ($psos as $pso) {
                    $vals = [];
                    foreach ($cos as $co) {
                        $m = $mappings->first(fn($map) => $map->course_outcome_id == $co->id && $map->target_type == 'PSO' && $map->target_id == $pso->id);
                        if ($m && $m->correlation_level > 0) {
                            $vals[] = $m->correlation_level;
                        }
                    }
                    $psoAvgs[$pso->pso_code] = count($vals) > 0 ? round(array_sum($vals) / count($vals), 2) : null;
                }
            }

            foreach ($pos as $po) {
                if (isset($poAvgs[$po->po_code]) && $poAvgs[$po->po_code] !== null) {
                    $programPoSums[$po->po_code][] = $poAvgs[$po->po_code];
                }
            }

            foreach ($psos as $pso) {
                if (isset($psoAvgs[$pso->pso_code]) && $psoAvgs[$pso->pso_code] !== null) {
                    $programPsoSums[$pso->pso_code][] = $psoAvgs[$pso->pso_code];
                }
            }

            $matrixRows[] = [
                'course_id' => $c->id,
                'course_code' => $c->course_code,
                'course_name' => $c->course_name,
                'semester' => $c->semester,
                'is_active' => ($activeCourseCode && strtoupper($c->course_code) === strtoupper($activeCourseCode)),
                'po_averages' => $poAvgs,
                'pso_averages' => $psoAvgs,
            ];
        }

        // Program averages across all courses
        $programAverage = ['po' => [], 'pso' => []];
        foreach ($pos as $po) {
            $vals = $programPoSums[$po->po_code] ?? [];
            $programAverage['po'][$po->po_code] = count($vals) > 0 ? round(array_sum($vals) / count($vals), 2) : 2.40;
        }
        foreach ($psos as $pso) {
            $vals = $programPsoSums[$pso->pso_code] ?? [];
            $programAverage['pso'][$pso->pso_code] = count($vals) > 0 ? round(array_sum($vals) / count($vals), 2) : 2.10;
        }

        return [
            'rows' => $matrixRows,
            'pos' => $pos,
            'psos' => $psos,
            'program_average' => $programAverage,
        ];
    }

    /**
     * Ensure Course (e.g. UEC519) exists in the database with Course Outcomes.
     */
    public function ensureCourseExists(string $courseCode, string $courseName, int $programId = 1): Course
    {
        $course = Course::where('course_code', $courseCode)->first();
        if (!$course) {
            $course = Course::create([
                'program_id' => $programId,
                'academic_year_id' => 1,
                'course_code' => $courseCode,
                'course_name' => $courseName,
                'semester' => 5,
                'credits' => 4,
                'lecture_hours' => 3,
                'tutorial_hours' => 1,
                'practical_hours' => 2,
                'course_type' => 'Professional Core',
            ]);
        }

        // Ensure 6 COs exist
        $defaults = $this->getDefaultUec519Mappings();
        foreach ($defaults as $coCode => $coDef) {
            CourseOutcome::firstOrCreate(
                ['course_id' => $course->id, 'co_code' => $coCode],
                [
                    'description' => $coDef['description'],
                    'target_percentage' => 65,
                    'target_level' => 2.5,
                    'bloom_level' => $coDef['bloom_level'],
                ]
            );
        }

        return $course;
    }
}
