<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AssessmentBatch;
use App\Models\AssessmentQuestion;
use App\Models\StudentMark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class StudentMarksCsvController extends Controller
{
    /**
     * Upload a CSV or XLSX file containing student marks.
     *
     * Expected format:
     * student_id (or Roll No), Q1, Q2, Q3...
     */
    public function upload(Request $request, AssessmentBatch $assessmentBatch): JsonResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:15360',
            ],
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['csv', 'txt', 'xlsx', 'xls'])) {
            return response()->json([
                'success' => false,
                'message' => 'Uploaded file must be a CSV or Excel spreadsheet (.csv, .xlsx, .xls).',
            ], 422);
        }

        // Parse rows from either CSV or XLSX
        if ($ext === 'xlsx') {
            $rawRows = $this->parseXlsxRows($file->getRealPath());
        } else {
            $rawRows = $this->parseCsvRows($file->getRealPath());
        }

        if (empty($rawRows) || count($rawRows) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'The uploaded file must contain a header row and at least one student data row.',
            ], 422);
        }

        $header = array_shift($rawRows);

        // Normalize header
        $header = array_map(function ($value) {
            $v = strtolower(trim((string) $value));
            $v = preg_replace('/\(.*?\)/', '', $v);
            $v = trim($v);
            if (in_array($v, ['roll no', 'roll no.', 'roll_no', 'student id', 'student_id', 'roll', 's. no.', 's.no.'])) {
                return 'student_id';
            }
            return $v;
        }, $header);

        // Ensure first column is student_id
        if (empty($header[0]) || $header[0] !== 'student_id') {
            return response()->json([
                'success' => false,
                'message' => 'First column must be student_id or Roll No.',
            ], 422);
        }

        $questions = $assessmentBatch->questions()
            ->orderBy('question_number')
            ->get();

        if ($questions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No questions have been configured for this assessment.',
            ], 422);
        }

        $questionMap = [];
        foreach ($questions as $question) {
            $questionMap['q' . $question->question_number] = $question;
        }

        $uploadedQuestions = array_slice($header, 1);

        foreach ($uploadedQuestions as $qCode) {
            if (!empty($qCode) && !isset($questionMap[$qCode])) {
                return response()->json([
                    'success' => false,
                    'message' => "Question '{$qCode}' is not configured for this assessment batch.",
                ], 422);
            }
        }

        $rows = [];
        $errors = [];
        $rowNumber = 1;

        foreach ($rawRows as $data) {
            $rowNumber++;

            if (empty($data) || (count($data) === 1 && trim((string)$data[0]) === '')) {
                continue;
            }

            $studentId = trim((string)($data[0] ?? ''));
            if ($studentId === '') {
                continue;
            }

            foreach ($uploadedQuestions as $index => $qCode) {
                if (empty($qCode) || !isset($questionMap[$qCode])) {
                    continue;
                }

                $value = isset($data[$index + 1]) ? trim((string)$data[$index + 1]) : '';
                $question = $questionMap[$qCode];

                // If blank or NA, skip or store null
                if ($value === '' || strtoupper($value) === 'NA') {
                    continue;
                }

                if (!is_numeric($value)) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'student_id' => $studentId,
                        'question' => strtoupper($qCode),
                        'message' => 'Marks must be numeric.',
                    ];
                    continue;
                }

                $marks = (float)$value;

                if ($marks < 0 || $marks > $question->max_marks) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'student_id' => $studentId,
                        'question' => strtoupper($qCode),
                        'message' => "Marks must be between 0 and {$question->max_marks}.",
                    ];
                    continue;
                }

                $rows[] = [
                    'assessment_question_id' => $question->id,
                    'student_identifier' => $studentId,
                    'marks_obtained' => $marks,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'File validation failed. No marks were imported.',
                'errors' => array_slice($errors, 0, 15),
            ], 422);
        }

        if (empty($rows)) {
            return response()->json([
                'success' => false,
                'message' => 'File contains no valid student marks.',
            ], 422);
        }

        DB::transaction(function () use ($rows) {
            foreach (array_chunk($rows, 500) as $chunk) {
                StudentMark::upsert(
                    $chunk,
                    [
                        'assessment_question_id',
                        'student_identifier',
                    ],
                    [
                        'marks_obtained',
                        'updated_at',
                    ]
                );
            }
        });

        $studentCount = collect($rows)
            ->pluck('student_identifier')
            ->unique()
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Student marks imported successfully.',
            'data' => [
                'students_imported' => $studentCount,
                'marks_imported' => count($rows),
                'assessment_batch_id' => $assessmentBatch->id,
            ],
        ]);
    }

    /**
     * Parse standard CSV file into rows array.
     */
    protected function parseCsvRows(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return [];
        }

        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = $data;
        }
        fclose($handle);

        return $rows;
    }

    /**
     * Parse XLSX file into rows array using ZipArchive.
     */
    protected function parseXlsxRows(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

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

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            $zip->close();
            return [];
        }

        $xml = simplexml_load_string($sheetXml);
        $zip->close();

        $rowsIndexed = [];
        foreach ($xml->sheetData->row as $row) {
            $rIdx = (int)$row['r'];
            $cols = [];
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
                $cols[$col] = $val;
            }
            $rowsIndexed[$rIdx] = $cols;
        }

        // Convert column letters into ordered 0-indexed array
        $output = [];
        foreach ($rowsIndexed as $cols) {
            $maxCol = 'A';
            foreach (array_keys($cols) as $colKey) {
                if (strlen($colKey) > strlen($maxCol) || (strlen($colKey) === strlen($maxCol) && $colKey > $maxCol)) {
                    $maxCol = $colKey;
                }
            }
            $rowArray = [];
            // Generate columns up to maxCol
            $curr = 'A';
            while (true) {
                $rowArray[] = $cols[$curr] ?? '';
                if ($curr === $maxCol) break;
                $curr++;
            }
            $output[] = $rowArray;
        }

        return $output;
    }
}
