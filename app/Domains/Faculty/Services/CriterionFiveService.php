<?php

namespace App\Domains\Faculty\Services;

use App\Domains\Academic\Models\Program;
use App\Domains\Academic\Models\AcademicYear;
use App\Domains\Academic\Models\AcademicIntake;
use App\Domains\Faculty\Models\FacultyProfile;
use App\Domains\Faculty\Models\FacultyAcademicYear;
use App\Domains\Faculty\Models\VisitingFaculty;
use Carbon\Carbon;

class CriterionFiveService
{
    /**
     * Compute full Criterion 5 breakdown (5.1 to 5.5, max 100 marks)
     */
    public function calculateFullCriterion(Program $program, ?AcademicYear $cay = null): array
    {
        $years = $this->getAssessmentYears();

        $c51 = $this->calculateSfr($program, $years);
        $c52 = $this->calculateFqi($program, $years, $c51);
        $c53 = $this->calculateCadre($program, $years, $c51);
        $c54 = $this->calculateVisitingFaculty($program, $years);
        $c55 = $this->calculateRetention($program, $years, $c51);

        $totalMarks = round(
            $c51['claimed_marks'] +
            $c52['claimed_marks'] +
            $c53['claimed_marks'] +
            $c54['claimed_marks'] +
            $c55['claimed_marks'],
            2
        );

        return [
            'program_id' => $program->id,
            'program_name' => $program->name,
            'department_name' => $program->department->name ?? 'Department of Electronics and Communication Engineering',
            'assessment_years' => $years->map(fn($y) => [
                'id' => $y->id,
                'name' => $y->name,
                'type' => $y->type,
            ]),
            'total_max_marks' => 100.0,
            'total_claimed_marks' => min(100.0, $totalMarks),
            'percentage' => round((min(100.0, $totalMarks) / 100.0) * 100, 1),
            'sub_criteria' => [
                'c51' => $c51,
                'c52' => $c52,
                'c53' => $c53,
                'c54' => $c54,
                'c55' => $c55,
                '5.1' => $c51,
                '5.2' => $c52,
                '5.3' => $c53,
                '5.4' => $c54,
                '5.5' => $c55,
            ],
        ];
    }

    /**
     * Get CAY, CAYm1, CAYm2 academic years
     */
    public function getAssessmentYears()
    {
        $years = AcademicYear::whereIn('type', ['CAY', 'CAYm1', 'CAYm2'])
            ->orderBy('start_year', 'desc')
            ->get();

        if ($years->count() < 3) {
            $years = AcademicYear::orderBy('start_year', 'desc')->take(3)->get();
        }

        return $years;
    }

    /**
     * 5.1 Student-Faculty Ratio (30 Marks)
     */
    public function calculateSfr(Program $program, $years): array
    {
        // 1. Table 5.1.1 Intake & Admission Details (UG1, UG2, UG3, PG1, PG2)
        $table511 = [
            'programs' => [
                [
                    'name' => 'B.E. (Electronics & Communication Engineering)',
                    'code' => 'UG1',
                    'years' => [
                        ['year' => '2025-26', 'sa' => 240, 'n1' => 240, 'unfilled' => 0, 'l' => 0, 'st' => 240],
                        ['year' => '2024-25', 'sa' => 240, 'n1' => 240, 'unfilled' => 0, 'l' => 4, 'st' => 244],
                        ['year' => '2023-24', 'sa' => 240, 'n1' => 238, 'unfilled' => 0, 'l' => 3, 'st' => 243],
                        ['year' => '2022-23', 'sa' => 276, 'n1' => 263, 'unfilled' => 0, 'l' => 4, 'st' => 280],
                        ['year' => '2021-22', 'sa' => 240, 'n1' => 240, 'unfilled' => 0, 'l' => 4, 'st' => 244],
                        ['year' => '2020-21', 'sa' => 210, 'n1' => 210, 'unfilled' => 0, 'l' => 0, 'st' => 210],
                    ],
                ],
                [
                    'name' => 'B.E. (Electronics and Computer Engineering)',
                    'code' => 'UG2',
                    'years' => [
                        ['year' => '2025-26', 'sa' => 360, 'n1' => 360, 'unfilled' => 0, 'l' => 0, 'st' => 360],
                        ['year' => '2024-25', 'sa' => 360, 'n1' => 349, 'unfilled' => 0, 'l' => 1, 'st' => 361],
                        ['year' => '2023-24', 'sa' => 276, 'n1' => 276, 'unfilled' => 0, 'l' => 2, 'st' => 278],
                        ['year' => '2022-23', 'sa' => 276, 'n1' => 276, 'unfilled' => 0, 'l' => 4, 'st' => 280],
                        ['year' => '2021-22', 'sa' => 240, 'n1' => 240, 'unfilled' => 0, 'l' => 0, 'st' => 240],
                        ['year' => '2020-21', 'sa' => 180, 'n1' => 180, 'unfilled' => 0, 'l' => 0, 'st' => 180],
                    ],
                ],
                [
                    'name' => 'B.E. - Electronics Engineering (VLSI Design and Technology)',
                    'code' => 'UG3',
                    'years' => [
                        ['year' => '2025-26', 'sa' => 90, 'n1' => 90, 'unfilled' => 0, 'l' => 0, 'st' => 90],
                        ['year' => '2024-25', 'sa' => 90, 'n1' => 90, 'unfilled' => 0, 'l' => 2, 'st' => 92],
                        ['year' => '2023-24', 'sa' => 60, 'n1' => 60, 'unfilled' => 0, 'l' => 0, 'st' => 60],
                    ],
                ],
            ],
        ];

        // 2. Table 5.1.2 Student-Faculty Ratio
        // CAY: 2025-26, CAYm1: 2024-25, CAYm2: 2023-24
        $table512 = [
            'CAY' => [
                'label' => 'CAY (2025-26)',
                'year_name' => '2025-26',
                'ug1_b' => 244, 'ug1_c' => 243, 'ug1_d' => 280, 'ug1_total' => 767,
                'ug2_b' => 361, 'ug2_c' => 278, 'ug2_d' => 280, 'ug2_total' => 919,
                'ug3_b' => 92,  'ug3_c' => 60,  'ug3_d' => 0,   'ug3_total' => 152,
                'pg1_a' => 30,  'pg1_b' => 30,  'pg1_total' => 60,
                'pg2_a' => 0,   'pg2_b' => 0,   'pg2_total' => 0,
                'ds' => 1898,
                'as' => 0,
                's' => 1898,
                'df' => 96,
                'af' => 0,
                'f' => 96,
                'ff' => 0,
                'tf' => 96,
                'sfr' => 19.77,
            ],
            'CAYm1' => [
                'label' => 'CAYm1 (2024-25)',
                'year_name' => '2024-25',
                'ug1_b' => 243, 'ug1_c' => 280, 'ug1_d' => 244, 'ug1_total' => 767,
                'ug2_b' => 278, 'ug2_c' => 280, 'ug2_d' => 240, 'ug2_total' => 798,
                'ug3_b' => 60,  'ug3_c' => 0,   'ug3_d' => 0,   'ug3_total' => 60,
                'pg1_a' => 30,  'pg1_b' => 30,  'pg1_total' => 60,
                'pg2_a' => 0,   'pg2_b' => 18,  'pg2_total' => 18,
                'ds' => 1703,
                'as' => 0,
                's' => 1703,
                'df' => 88,
                'af' => 0,
                'f' => 88,
                'ff' => 0,
                'tf' => 88,
                'sfr' => 19.35,
            ],
            'CAYm2' => [
                'label' => 'CAYm2 (2023-24)',
                'year_name' => '2023-24',
                'ug1_b' => 280, 'ug1_c' => 244, 'ug1_d' => 210, 'ug1_total' => 734,
                'ug2_b' => 280, 'ug2_c' => 240, 'ug2_d' => 180, 'ug2_total' => 700,
                'ug3_b' => 0,   'ug3_c' => 0,   'ug3_d' => 0,   'ug3_total' => 0,
                'pg1_a' => 30,  'pg1_b' => 30,  'pg1_total' => 60,
                'pg2_a' => 18,  'pg2_b' => 18,  'pg2_total' => 36,
                'ds' => 1530,
                'as' => 0,
                's' => 1530,
                'df' => 85,
                'af' => 0,
                'f' => 85,
                'ff' => 0,
                'tf' => 85,
                'sfr' => 18.00,
            ],
        ];

        // Department faculty counts from NBA SAR assessment: CAY=96, CAYm1=88, CAYm2=85
        $sfr1 = $table512['CAY']['sfr'];
        $sfr2 = $table512['CAYm1']['sfr'];
        $sfr3 = $table512['CAYm2']['sfr'];
        $avgSfr = round(($sfr1 + $sfr2 + $sfr3) / 3, 2);

        // Scoring rules:
        // SFR < 15: 30 marks
        // SFR < 17: 26 marks
        // SFR < 19: 22 marks
        // SFR < 21: 18 marks
        // SFR < 23: 14 marks
        // SFR < 25: 10 marks
        // SFR >= 25: 0 marks
        $claimedMarks = 0.0;
        $statusTier = 'Zero Marks (>= 25:1)';

        if ($avgSfr < 15.0) {
            $claimedMarks = 30.0;
            $statusTier = 'Tier 1: < 15:1 (30 Marks)';
        } elseif ($avgSfr < 17.0) {
            $claimedMarks = 26.0;
            $statusTier = 'Tier 2: < 17:1 (26 Marks)';
        } elseif ($avgSfr < 19.0) {
            $claimedMarks = 22.0;
            $statusTier = 'Tier 3: < 19:1 (22 Marks)';
        } elseif ($avgSfr < 21.0) {
            $claimedMarks = 18.0;
            $statusTier = 'Tier 4: < 21:1 (18 Marks)';
        } elseif ($avgSfr < 23.0) {
            $claimedMarks = 14.0;
            $statusTier = 'Tier 5: < 23:1 (14 Marks)';
        } elseif ($avgSfr < 25.0) {
            $claimedMarks = 10.0;
            $statusTier = 'Tier 6: < 25:1 (10 Marks)';
        } else {
            $claimedMarks = 0.0;
            $statusTier = 'Deficient: >= 25:1 (0 Marks)';
        }

        return [
            'sub_criterion' => '5.1',
            'title' => 'Student-Faculty Ratio (SFR)',
            'max_marks' => 30.0,
            'claimed_marks' => $claimedMarks,
            'avg_sfr' => $avgSfr,
            'status_tier' => $statusTier,
            'table_5_1_1' => $table511,
            'table_5_1_2' => $table512,
        ];
    }

    /**
     * 5.2 Faculty Qualification (25 Marks)
     * FQI = 2.5 * [(10X + 4Y) / RF]
     */
    public function calculateFqi(Program $program, $years, array $c51): array
    {
        $sData = $c51['table_5_1_2'];

        $rows = [
            'CAY' => [
                'year_name' => 'CAY (2025-26)',
                't' => $sData['CAY']['df'] ?? 96,
                'x' => 71, // PhD
                'y' => 25, // M.Tech / ME
                'rf' => 95, // S / 20 = 1898 / 20 ~ 95
                'fqi' => 21.32,
            ],
            'CAYm1' => [
                'year_name' => 'CAYm1 (2024-25)',
                't' => $sData['CAYm1']['df'] ?? 88,
                'x' => 65,
                'y' => 23,
                'rf' => 85, // S / 20 = 1703 / 20 ~ 85
                'fqi' => 21.24,
            ],
            'CAYm2' => [
                'year_name' => 'CAYm2 (2023-24)',
                't' => $sData['CAYm2']['df'] ?? 85,
                'x' => 61,
                'y' => 24,
                'rf' => 76, // S / 20 = 1530 / 20 ~ 76
                'fqi' => 22.89,
            ],
        ];

        // Dynamically compute FQI if real faculty data exists
        foreach ($rows as $key => &$row) {
            $rf = max(1, $row['rf']);
            $calculatedFqi = round(2.5 * ((10 * $row['x'] + 4 * $row['y']) / $rf), 2);
            $row['fqi'] = min(25.0, $calculatedFqi);
        }

        $avgAssessment = round(($rows['CAY']['fqi'] + $rows['CAYm1']['fqi'] + $rows['CAYm2']['fqi']) / 3, 2);

        return [
            'sub_criterion' => '5.2',
            'title' => 'Faculty Qualification (FQI)',
            'max_marks' => 25.0,
            'claimed_marks' => min(25.0, $avgAssessment),
            'avg_fqi' => $avgAssessment,
            'table_5_2_1' => $rows,
        ];
    }

    /**
     * 5.3 Faculty Cadre Proportion (25 Marks)
     * Cadre Ratio 1 : 2 : 6
     * Cadre Score = 12.5 * [(AF1/RF1) + 0.6*(AF2/RF2) + 0.4*(AF3/RF3)]
     */
    public function calculateCadre(Program $program, $years, array $c51): array
    {
        $rows = [
            'CAY' => [
                'year_name' => 'CAY (2025-26)',
                't' => 96,
                'rf1' => 10.5, 'af1' => 9,   // Professors
                'rf2' => 21.0, 'af2' => 19,  // Assoc Profs
                'rf3' => 63.0, 'af3' => 68,  // Asst Profs
            ],
            'CAYm1' => [
                'year_name' => 'CAYm1 (2024-25)',
                't' => 88,
                'rf1' => 9.4, 'af1' => 6,
                'rf2' => 19.0, 'af2' => 14,
                'rf3' => 57.0, 'af3' => 68,
            ],
            'CAYm2' => [
                'year_name' => 'CAYm2 (2023-24)',
                't' => 85,
                'rf1' => 8.5, 'af1' => 5,
                'rf2' => 17.0, 'af2' => 12,
                'rf3' => 51.0, 'af3' => 68,
            ],
        ];

        // 3-Year averages
        $avgRf1 = round((10.5 + 9.4 + 8.5) / 3, 2); // 9.47
        $avgAf1 = round((9 + 6 + 5) / 3, 2);        // 6.67
        $avgRf2 = round((21.0 + 19.0 + 17.0) / 3, 2); // 19.00
        $avgAf2 = round((19 + 14 + 12) / 3, 2);       // 15.00
        $avgRf3 = round((63.0 + 57.0 + 51.0) / 3, 2); // 57.00
        $avgAf3 = round((68 + 68 + 68) / 3, 2);       // 68.00

        // Zero rule: If AF1 and AF2 are both 0 => 0 marks
        if ($avgAf1 == 0 && $avgAf2 == 0) {
            $cadreMarks = 0.0;
        } else {
            $pRatio = $avgAf1 / max(0.1, $avgRf1);
            $aRatio = $avgAf2 / max(0.1, $avgRf2);
            $asRatio = $avgAf3 / max(0.1, $avgRf3);

            $rawMarks = round(($pRatio + 0.6 * $aRatio + 0.4 * $asRatio) * 12.5, 2);
            $cadreMarks = min(25.0, $rawMarks);
        }

        return [
            'sub_criterion' => '5.3',
            'title' => 'Faculty Cadre Proportion',
            'max_marks' => 25.0,
            'claimed_marks' => $cadreMarks,
            'formula' => 'Cadre Score = 12.5 * [(AF1/RF1) + 0.6(AF2/RF2) + 0.4(AF3/RF3)]',
            'table_5_3_1' => $rows,
            'averages' => [
                't' => 89.66,
                'rf1' => $avgRf1, 'af1' => $avgAf1,
                'rf2' => $avgRf2, 'af2' => $avgAf2,
                'rf3' => $avgRf3, 'af3' => $avgAf3,
            ],
            'breakdown_calculation' => [
                'prof_component' => round($avgAf1 / max(0.1, $avgRf1), 3),
                'assoc_component' => round(($avgAf2 / max(0.1, $avgRf2)) * 0.6, 3),
                'asst_component' => round(($avgAf3 / max(0.1, $avgRf3)) * 0.4, 3),
            ],
        ];
    }

    /**
     * 5.4 Visiting/Adjunct Faculty/Professor of Practice (10 Marks)
     * Provision = 1 mark
     * >= 50 hrs/yr = 3 marks/yr across 3 assessment years = 9 marks
     * Max = 10 marks
     */
    public function calculateVisitingFaculty(Program $program, $years): array
    {
        // Try fetching real records from database
        $dbRecords = VisitingFaculty::where('program_id', $program->id)->get();

        if ($dbRecords->isEmpty()) {
            // Reference seed data from document
            $visitingList = [
                'CAYm1' => [
                    'year_name' => 'CAYm1 (2024-2025)',
                    'total_hours' => 220.0,
                    'qualifies' => true,
                    'faculty' => [
                        [
                            'id' => 1,
                            'name' => 'Prof. Sanjeev Bedi',
                            'designation' => 'Professor',
                            'organization' => 'University of Waterloo, Canada',
                            'course_name' => 'Capstone Projects (UEC797), Engineering Design Project-I & II, ELC Activities',
                            'course_code' => 'UEC797 / UTA016',
                            'interaction_hours' => 60.0,
                        ],
                        [
                            'id' => 2,
                            'name' => 'Prof. Roop Mahajan',
                            'designation' => 'Professor',
                            'organization' => 'Virginia Tech, USA',
                            'course_name' => 'Engineering Materials (UES012) & Research projects under CEEMS',
                            'course_code' => 'UES012',
                            'interaction_hours' => 60.0,
                        ],
                        [
                            'id' => 3,
                            'name' => 'Prof. Niom Eliaz',
                            'designation' => 'Professor',
                            'organization' => 'Tel Aviv University, Israel',
                            'course_name' => 'Research projects under COE-FSC',
                            'course_code' => 'COE-FSC',
                            'interaction_hours' => 50.0,
                        ],
                        [
                            'id' => 4,
                            'name' => 'Prof. Shiva Sashtri',
                            'designation' => 'Professor',
                            'organization' => 'IISC Bangalore',
                            'course_name' => 'Embedded Systems (UEC513), Computer Architecture (UEC610), IoT Systems (UEC640)',
                            'course_code' => 'UEC513',
                            'interaction_hours' => 50.0,
                        ],
                    ],
                ],
                'CAYm2' => [
                    'year_name' => 'CAYm2 (2023-2024)',
                    'total_hours' => 170.0,
                    'qualifies' => true,
                    'faculty' => [
                        [
                            'id' => 5,
                            'name' => 'Prof. Sanjeev Bedi',
                            'designation' => 'Professor',
                            'organization' => 'University of Waterloo, Canada',
                            'course_name' => 'Capstone Projects (UEC797), Engineering Design Project-I & II',
                            'course_code' => 'UEC797',
                            'interaction_hours' => 60.0,
                        ],
                        [
                            'id' => 6,
                            'name' => 'Prof. Roop Mahajan',
                            'designation' => 'Professor',
                            'organization' => 'Virginia Tech, USA',
                            'course_name' => 'Engineering Materials (UES012) & CEEMS Research',
                            'course_code' => 'UES012',
                            'interaction_hours' => 60.0,
                        ],
                        [
                            'id' => 7,
                            'name' => 'Prof. Niom Eliaz',
                            'designation' => 'Professor',
                            'organization' => 'Tel Aviv University, Israel',
                            'course_name' => 'Research projects under COE-FSC',
                            'course_code' => 'COE-FSC',
                            'interaction_hours' => 50.0,
                        ],
                    ],
                ],
                'CAYm3' => [
                    'year_name' => 'CAYm3 (2022-2023)',
                    'total_hours' => 170.0,
                    'qualifies' => true,
                    'faculty' => [
                        [
                            'id' => 8,
                            'name' => 'Prof. Sanjeev Bedi',
                            'designation' => 'Professor',
                            'organization' => 'University of Waterloo, Canada',
                            'course_name' => 'Capstone Projects (UEC797), Engineering Design Project-I & II',
                            'course_code' => 'UEC797',
                            'interaction_hours' => 60.0,
                        ],
                        [
                            'id' => 9,
                            'name' => 'Prof. Roop Mahajan',
                            'designation' => 'Professor',
                            'organization' => 'Virginia Tech, USA',
                            'course_name' => 'Engineering Materials (UES012) & CEEMS Research',
                            'course_code' => 'UES012',
                            'interaction_hours' => 60.0,
                        ],
                        [
                            'id' => 10,
                            'name' => 'Prof. Niom Eliaz',
                            'designation' => 'Professor',
                            'organization' => 'Tel Aviv University, Israel',
                            'course_name' => 'Research projects under CFS',
                            'course_code' => 'CFS',
                            'interaction_hours' => 50.0,
                        ],
                    ],
                ],
            ];
        } else {
            // Group by academic year
            $visitingList = [];
            foreach ($dbRecords as $vf) {
                $yName = $vf->academicYear->name ?? 'CAY';
                if (!isset($visitingList[$yName])) {
                    $visitingList[$yName] = [
                        'year_name' => $yName,
                        'total_hours' => 0.0,
                        'qualifies' => false,
                        'faculty' => [],
                    ];
                }
                $visitingList[$yName]['faculty'][] = [
                    'id' => $vf->id,
                    'name' => $vf->name,
                    'designation' => $vf->designation,
                    'organization' => $vf->organization,
                    'course_name' => $vf->course_name,
                    'course_code' => $vf->course_code,
                    'interaction_hours' => (float)$vf->interaction_hours,
                ];
                $visitingList[$yName]['total_hours'] += (float)$vf->interaction_hours;
                $visitingList[$yName]['qualifies'] = $visitingList[$yName]['total_hours'] >= 50.0;
            }
        }

        $provisionMark = 1.0;
        $hoursMarks = 0.0;
        $summaryTable = [];

        foreach ($visitingList as $key => $yData) {
            $earned = ($yData['total_hours'] >= 50.0) ? 3.0 : 0.0;
            $hoursMarks += $earned;
            $summaryTable[$key] = [
                'year_name' => $yData['year_name'],
                'total_hours' => $yData['total_hours'],
                'min_hours_met' => $yData['total_hours'] >= 50.0,
                'score_awarded' => $earned,
            ];
        }

        $claimedMarks = min(10.0, $provisionMark + min(9.0, $hoursMarks));

        return [
            'sub_criterion' => '5.4',
            'title' => 'Visiting / Adjunct Faculty / Professor of Practice',
            'max_marks' => 10.0,
            'claimed_marks' => $claimedMarks,
            'provision_score' => $provisionMark,
            'interaction_hours_score' => min(9.0, $hoursMarks),
            'table_5_4_1' => $visitingList,
            'table_5_4_2' => $summaryTable,
        ];
    }

    /**
     * 5.5 Faculty Retention (10 Marks)
     * FR = [((A*0) + (B*1) + (C*2) + (D*3) + (E*4)) / RF] * 2.5
     */
    public function calculateRetention(Program $program, $years, array $c51): array
    {
        $rows = [
            'CAY' => [
                'year_name' => '2025-26 (CAY)',
                'rf' => 95,
                'af' => 96,
                'a' => 8,  // < 1 yr
                'b' => 12, // 1 to 2 yrs
                'c' => 8,  // 2 to 3 yrs
                'd' => 8,  // 3 to 4 yrs
                'e' => 60, // > 4 yrs
                'points' => 292,
                'fr' => 7.68,
            ],
            'CAYm1' => [
                'year_name' => '2024-25 (CAYm1)',
                'rf' => 85,
                'af' => 88,
                'a' => 6,
                'b' => 13,
                'c' => 8,
                'd' => 6,
                'e' => 55,
                'points' => 267,
                'fr' => 7.85,
            ],
            'CAYm2' => [
                'year_name' => '2023-24 (CAYm2)',
                'rf' => 76,
                'af' => 85,
                'a' => 13,
                'b' => 9,
                'c' => 8,
                'd' => 3,
                'e' => 52,
                'points' => 242,
                'fr' => 7.96,
            ],
        ];

        // Recalculate FR points & score
        foreach ($rows as $key => &$r) {
            $points = ($r['a'] * 0) + ($r['b'] * 1) + ($r['c'] * 2) + ($r['d'] * 3) + ($r['e'] * 4);
            $r['points'] = $points;
            $r['fr'] = round(($points / max(1, $r['rf'])) * 2.5, 2);
        }

        $avgFr = round(($rows['CAY']['fr'] + $rows['CAYm1']['fr'] + $rows['CAYm2']['fr']) / 3, 2);

        return [
            'sub_criterion' => '5.5',
            'title' => 'Faculty Retention',
            'max_marks' => 10.0,
            'claimed_marks' => min(10.0, $avgFr),
            'avg_retention' => $avgFr,
            'formula' => 'FR = [((A*0) + (B*1) + (C*2) + (D*3) + (E*4)) / RF] * 2.5',
            'table_5_5_1' => $rows,
        ];
    }
}
