<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Domains\Curriculum\Services\CloWorkflowService;
use App\Domains\Academic\Models\Program;
use App\Domains\Curriculum\Models\Course;
use App\Domains\Curriculum\Models\CourseOutcome;
use App\Domains\Curriculum\Models\CoPoMapping;
use App\Domains\Curriculum\Models\ProgramOutcome;
use App\Domains\Curriculum\Models\ProgramSpecificOutcome;
use App\Domains\Attainment\Models\StudentAssessment;
use App\Domains\Attainment\Models\AttainmentResult;
use App\Domains\Academic\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use ZipArchive;
use Exception;

class CloWorkflowApiController extends Controller
{
    protected CloWorkflowService $service;

    public function __construct(CloWorkflowService $service)
    {
        $this->service = $service;
    }

    /**
     * Parse uploaded performance file or load sample file.
     */
    public function parsePerformance(Request $request): JsonResponse
    {
        try {
            $filePath = null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = $file->getRealPath();
            } elseif ($request->boolean('use_sample')) {
                $samplePath = base_path('Ongoing Performance.xlsx');
                if (!file_exists($samplePath)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sample file Ongoing Performance.xlsx not found in project directory.',
                    ], 404);
                }
                $filePath = $samplePath;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded and sample file not requested.',
                ], 422);
            }

            $result = $this->service->parsePerformanceFile($filePath);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error parsing performance file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Compute the 3 deliverables: CLO sheet, CLO-PO mapping, and Articulation Matrix.
     */
    public function calculate(Request $request): JsonResponse
    {
        try {
            $programId = (int)$request->input('program_id', 1);
            $program = Program::find($programId) ?? Program::first();
            if (!$program) {
                return response()->json(['success' => false, 'message' => 'Program not found'], 404);
            }

            $courseCode = trim((string)$request->input('course_code', 'UEC519'));
            $courseName = trim((string)$request->input('course_name', 'Analog and Digital Communication Systems'));

            // 1. Ensure course exists in DB
            $course = $this->service->ensureCourseExists($courseCode, $courseName, $program->id);

            // 2. Parse or receive student data & CO mappings
            $students = $request->input('students', []);
            $coMappings = $request->input('co_mappings', []);
            $coPoRows = $request->input('co_po_rows', []);

            // Fallback to sample performance if empty
            if (empty($students)) {
                $parsed = $this->service->parsePerformanceFile(base_path('Ongoing Performance.xlsx'));
                $students = $parsed['students'];
            }

            if (empty($coMappings)) {
                $coMappings = $this->service->getDefaultUec519Mappings();
            }

            // Fallback CO-PO mapping rows if not provided
            if (empty($coPoRows)) {
                $pos = ProgramOutcome::where('program_id', $program->id)->orderBy('po_code')->get();
                $psos = ProgramSpecificOutcome::where('program_id', $program->id)->orderBy('pso_code')->get();

                // Standard UEC519 NBA mappings from PO PSO CALCULATION ENC 2024-25.xlsx
                $sampleMappings = [
                    'CO1' => ['po' => ['PO1' => 3, 'PO2' => 3, 'PO3' => 3], 'pso' => ['PSO1' => 1, 'PSO2' => 1]],
                    'CO2' => ['po' => ['PO1' => 3, 'PO2' => 2, 'PO3' => 1], 'pso' => ['PSO1' => 2, 'PSO2' => 2]],
                    'CO3' => ['po' => ['PO1' => 3, 'PO2' => 3, 'PO3' => 2], 'pso' => ['PSO1' => 2, 'PSO2' => 2]],
                    'CO4' => ['po' => ['PO1' => 3, 'PO2' => 3, 'PO3' => 1], 'pso' => ['PSO1' => 2, 'PSO2' => 2]],
                    'CO5' => ['po' => ['PO1' => 3, 'PO2' => 3, 'PO3' => 2], 'pso' => ['PSO1' => 2, 'PSO2' => 2]],
                    'CO6' => ['po' => ['PO1' => 2, 'PO2' => 2, 'PO3' => 1], 'pso' => ['PSO1' => 2, 'PSO2' => 2]],
                ];

                $coPoRows = [];
                foreach ($course->courseOutcomes as $co) {
                    $def = $sampleMappings[$co->co_code] ?? ['po' => ['PO1' => 3, 'PO2' => 2], 'pso' => ['PSO1' => 2, 'PSO2' => 2]];
                    $row = [
                        'co_id' => $co->id,
                        'co_code' => $co->co_code,
                        'description' => $co->description,
                        'bloom_level' => $co->bloom_level,
                        'po' => [],
                        'pso' => [],
                    ];
                    foreach ($pos as $po) {
                        $row['po'][$po->po_code] = $def['po'][$po->po_code] ?? null;
                    }
                    foreach ($psos as $pso) {
                        $row['pso'][$pso->pso_code] = $def['pso'][$pso->pso_code] ?? null;
                    }
                    $coPoRows[] = $row;
                }
            }

            // DELIVERABLE 1: Calculate CLO Sheet
            $cloSResults = $this->service->calculateCloAttainment($students, $coMappings);

            // DELIVERABLE 2: Compute CLO-PO Mapping
            $cloPoResult = $this->service->computeCloPoMapping($coPoRows, $program);

            // DELIVERABLE 3: Compute Master Program Articulation Matrix
            $masterMatrix = $this->service->getMasterArticulationMatrix(
                $program,
                $courseCode,
                $cloPoResult['course_averages']
            );

            // Persist CO-PO mappings & attainment results to database
            $this->persistResults($course, $program, $cloSResults, $coPoRows);

            return response()->json([
                'success' => true,
                'data' => [
                    'course' => [
                        'id' => $course->id,
                        'course_code' => $course->course_code,
                        'course_name' => $course->course_name,
                        'semester' => $course->semester,
                    ],
                    'clo_sheet' => $cloSResults,
                    'clo_po_mapping' => $cloPoResult,
                    'articulation_matrix' => $masterMatrix,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Calculation error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Persist computed attainment & correlation mappings to DB.
     */
    protected function persistResults(Course $course, Program $program, array $cloSResults, array $coPoRows): void
    {
        $year = AcademicYear::where('type', 'CAY')->first() ?? AcademicYear::first();
        $yearId = $year ? $year->id : 1;

        $pos = ProgramOutcome::where('program_id', $program->id)->get()->keyBy('po_code');
        $psos = ProgramSpecificOutcome::where('program_id', $program->id)->get()->keyBy('pso_code');

        // Save CO-PO Mappings
        foreach ($coPoRows as $row) {
            $co = CourseOutcome::where('course_id', $course->id)->where('co_code', $row['co_code'])->first();
            if (!$co) continue;

            if (isset($row['po']) && is_array($row['po'])) {
                foreach ($row['po'] as $poCode => $level) {
                    if (isset($pos[$poCode])) {
                        CoPoMapping::updateOrCreate(
                            [
                                'course_outcome_id' => $co->id,
                                'target_type' => 'PO',
                                'target_id' => $pos[$poCode]->id,
                            ],
                            ['correlation_level' => (int)($level ?? 0)]
                        );
                    }
                }
            }

            if (isset($row['pso']) && is_array($row['pso'])) {
                foreach ($row['pso'] as $psoCode => $level) {
                    if (isset($psos[$psoCode])) {
                        CoPoMapping::updateOrCreate(
                            [
                                'course_outcome_id' => $co->id,
                                'target_type' => 'PSO',
                                'target_id' => $psos[$psoCode]->id,
                            ],
                            ['correlation_level' => (int)($level ?? 0)]
                        );
                    }
                }
            }
        }

        // Save Attainment Results
        foreach ($cloSResults['metrics'] as $metric) {
            $co = CourseOutcome::where('course_id', $course->id)->where('co_code', $metric['co_code'])->first();
            if (!$co) continue;

            StudentAssessment::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'course_outcome_id' => $co->id,
                    'academic_year_id' => $yearId,
                    'assessment_type' => $metric['tool_type'] === 'E' ? 'SEE' : 'CIE',
                ],
                [
                    'total_students_attempted' => $metric['students_considered'],
                    'students_meeting_target' => $metric['students_above_target'],
                    'percentage_achieved' => $metric['percentage_achieved'],
                    'attainment_level' => $metric['attainment_level'],
                ]
            );

            AttainmentResult::updateOrCreate(
                [
                    'course_outcome_id' => $co->id,
                    'academic_year_id' => $yearId,
                ],
                [
                    'direct_cie_score' => $metric['attainment_level'],
                    'direct_see_score' => $metric['attainment_level'],
                    'direct_attainment' => $metric['attainment_level'],
                    'indirect_attainment' => $metric['indirect_score_3'],
                    'final_score' => $metric['final_score'],
                ]
            );
        }
    }

    /**
     * Download / Export CLO Sheet as XLSX using the institutional template.
     */
    public function exportExcel(Request $request)
    {
        $templatePath = base_path('CLO_UEC519_ADCS.xlsx');
        if (!file_exists($templatePath)) {
            return response()->json(['success' => false, 'message' => 'Excel template not found'], 404);
        }

        $filename = 'CLO_Evaluation_' . date('Ymd_His') . '.xlsx';
        return response()->download($templatePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
