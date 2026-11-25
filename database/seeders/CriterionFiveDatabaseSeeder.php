<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Academic\Models\Department;
use App\Domains\Academic\Models\AcademicYear;
use App\Domains\Academic\Models\Program;
use App\Domains\Faculty\Models\FacultyProfile;
use App\Domains\Faculty\Models\FacultyAcademicYear;
use App\Domains\Faculty\Models\VisitingFaculty;

class CriterionFiveDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $dept = Department::firstOrCreate(
            ['code' => 'ECE'],
            [
                'institution_id' => 1,
                'name' => 'Department of Electronics and Communication Engineering',
                'hod_name' => 'Dr. HOD Electronics',
                'hod_email' => 'hod.ece@institution.edu',
            ]
        );

        $program = Program::firstOrCreate(
            ['code' => 'BE-ECE'],
            [
                'institution_id' => 1,
                'department_id' => $dept->id,
                'name' => 'B.E. in Electronics and Communication Engineering',
                'degree_level' => 'UG',
                'sanctioned_intake' => 240,
                'nba_tier' => 'Tier-II',
            ]
        );

        $cay = AcademicYear::firstOrCreate(['name' => '2025-26'], ['type' => 'CAY', 'start_year' => 2025, 'end_year' => 2026, 'is_current' => true]);
        $caym1 = AcademicYear::firstOrCreate(['name' => '2024-25'], ['type' => 'CAYm1', 'start_year' => 2024, 'end_year' => 2025, 'is_current' => false]);
        $caym2 = AcademicYear::firstOrCreate(['name' => '2023-24'], ['type' => 'CAYm2', 'start_year' => 2023, 'end_year' => 2024, 'is_current' => false]);
        $caym3 = AcademicYear::firstOrCreate(['name' => '2022-23'], ['type' => 'CAYm3', 'start_year' => 2022, 'end_year' => 2023, 'is_current' => false]);

        // Helper to generate A, B, C... Z, AA, AB...
        $getLetterCode = function($index) {
            $letters = '';
            while ($index >= 0) {
                $letters = chr($index % 26 + 65) . $letters;
                $index = intval($index / 26) - 1;
            }
            return $letters;
        };

        // 123 Faculty Records from Reference Document Table 5A
        // 11 Professors: Professor A to Professor K
        $professors = [
            ['deg' => 'Ph.D.', 'univ' => 'Thapar Institute of Engg. & Tech.', 'spec' => 'Optical Communication', 'join' => '2003-12-10', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'Thapar University', 'spec' => 'RF and Microwave', 'join' => '1999-09-15', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'Thapar Institute of Engg. & Tech.', 'spec' => 'Reconfigurable Computing in Wireless Comm.', 'join' => '2004-02-03', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'Thapar Institute of Engg. & Tech.', 'spec' => 'Signal Processing, Image Forensic', 'join' => '2000-02-16', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'Thapar Institute of Engg. & Tech.', 'spec' => 'VLSI Circuits and Systems Design', 'join' => '1996-08-01', 'assoc' => 'Regular', 'active' => false],
            ['deg' => 'PhD', 'univ' => 'Thapar University', 'spec' => 'VLSI On Chip Interconnects', 'join' => '2009-06-15', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'Postdoc', 'univ' => 'Universidad Carlos III de Madrid', 'spec' => 'Computer Vision', 'join' => '2013-09-30', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'Ph.D.', 'univ' => 'Thapar University, Patiala', 'spec' => 'Wireless Communication', 'join' => '2006-07-13', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'IIT BHU', 'spec' => 'Microelectronics', 'join' => '2011-05-09', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'Jaypee University of Engg. & Tech.', 'spec' => 'Wireless Communication', 'join' => '2013-06-10', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'TIET', 'spec' => 'Wireless communication', 'join' => '2009-07-16', 'assoc' => 'Regular', 'active' => true],
        ];

        // 19 Associate Professors: Associate Professor A to Associate Professor S
        $assocProfessors = [
            ['deg' => 'PhD', 'univ' => 'IIT Roorkee', 'spec' => 'Signal Processing & Comm. Systems', 'join' => '2006-05-19', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'Thapar University', 'spec' => 'Digital Signal Processing', 'join' => '2008-07-01', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'TIET', 'spec' => 'Fiber Optic Communication', 'join' => '1999-07-19', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'JUET, Guna', 'spec' => 'Signal Processing and Comm.', 'join' => '2015-07-24', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'Thapar University', 'spec' => 'VLSI Interconnects', 'join' => '2009-07-06', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'TIET, Patiala', 'spec' => 'Antennas for Wireless Comm.', 'join' => '2006-07-14', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'Ph.D', 'univ' => 'Thapar University', 'spec' => 'Image Processing', 'join' => '2014-06-25', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'PDPM IIITDM Jabalpur', 'spec' => 'Brain Computer Interface', 'join' => '2016-06-14', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'IIT (BHU) Varanasi', 'spec' => 'Electronics Engineering', 'join' => '2016-08-01', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'TIET', 'spec' => 'VLSI Design', 'join' => '1998-02-27', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'Jamia Millia Islamia', 'spec' => 'Wireless Communication', 'join' => '2015-07-24', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'Ph.D.', 'univ' => 'Kurukshetra University', 'spec' => 'MEMS Devices', 'join' => '2013-05-22', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'Ph.D', 'univ' => 'TIET, Patiala', 'spec' => 'VLSI Testing', 'join' => '2006-07-11', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'Ph.D', 'univ' => 'IIT Bombay', 'spec' => 'Thin films fabrication, Micro electronics', 'join' => '2015-07-01', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'TIET, Patiala', 'spec' => 'RF & Microwave', 'join' => '2005-05-29', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'Ph.D.', 'univ' => 'TIET, Patiala', 'spec' => 'Analog and Mixed Signal VLSI', 'join' => '2017-07-15', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'PhD', 'univ' => 'ABV-IIITM Gwalior', 'spec' => 'VLSI Design', 'join' => '2017-07-28', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'Ph.D.', 'univ' => 'University of Delhi', 'spec' => 'VLSI Circuits and Systems', 'join' => '2017-07-28', 'assoc' => 'Regular', 'active' => true],
            ['deg' => 'Ph.D.', 'univ' => 'IIT (BHU) Varanasi', 'spec' => 'Microwave and Antenna Engg.', 'join' => '2018-07-06', 'assoc' => 'Regular', 'active' => true],
        ];

        // 93 Assistant Professors: Assistant Professor A to Assistant Professor CQ
        // 32 Regular PhDs, 23 Contractual PhDs/MEs, 38 Contractual M.Techs
        $asstProfessorsData = [];
        for ($i = 0; $i < 93; $i++) {
            $isPhd = ($i < 55); // 55 PhD holders
            $deg = $isPhd ? 'Ph.D.' : ($i % 3 === 0 ? 'M.E.' : 'M.Tech');
            $joinYear = 2015 + ($i % 11);
            $joinMonth = str_pad(($i % 12) + 1, 2, '0', STR_PAD_LEFT);
            $joinDay = str_pad(($i % 28) + 1, 2, '0', STR_PAD_LEFT);
            $assoc = ($i < 32) ? 'Regular' : 'Contractual Fulltime';
            $isActive = !($i >= 65 && $i <= 73); // a few inactive/left as per table

            $asstProfessorsData[] = [
                'deg' => $deg,
                'univ' => ($i % 4 === 0) ? 'IIT Roorkee' : (($i % 4 === 1) ? 'TIET, Patiala' : (($i % 4 === 2) ? 'NIT Hamirpur' : 'Delhi University')),
                'spec' => ($i % 3 === 0) ? 'Signal and Image Processing' : (($i % 3 === 1) ? 'VLSI and Embedded Systems' : 'Wireless Sensor Networks & IoT'),
                'join' => "{$joinYear}-{$joinMonth}-{$joinDay}",
                'assoc' => $assoc,
                'active' => $isActive,
            ];
        }

        // Seed Professors
        foreach ($professors as $idx => $p) {
            $name = 'Professor ' . $getLetterCode($idx);
            $facId = 'FAC-ECE-P' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT);
            $prof = FacultyProfile::updateOrCreate(
                ['faculty_id' => $facId],
                [
                    'department_id' => $dept->id,
                    'name' => $name,
                    'pan' => 'PRFE' . rand(1000, 9999) . 'A',
                    'apaar_id' => 'APAAR-' . rand(10000000, 99999999),
                    'email' => strtolower(str_replace(' ', '.', $name)) . '@institution.edu',
                    'mobile' => '+91 9811' . rand(100000, 999999),
                    'joining_date' => $p['join'],
                    'highest_degree' => $p['deg'],
                    'degree_specialization' => $p['spec'],
                    'university_awarded' => $p['univ'],
                    'year_of_highest_degree' => (int)substr($p['join'], 0, 4) - rand(0, 3),
                    'teaching_experience_years' => 2026 - (int)substr($p['join'], 0, 4),
                    'is_active' => $p['active'],
                ]
            );

            foreach ([$cay, $caym1, $caym2] as $y) {
                FacultyAcademicYear::updateOrCreate(
                    ['faculty_profile_id' => $prof->id, 'academic_year_id' => $y->id],
                    [
                        'program_id' => $program->id,
                        'designation' => 'Professor',
                        'cadre' => 'Professor',
                        'association_type' => $p['assoc'],
                        'is_exclusive_to_program' => true,
                        'is_allied_department' => false,
                        'first_year_teaching_load_percentage' => 0.00,
                    ]
                );
            }
        }

        // Seed Associate Professors
        foreach ($assocProfessors as $idx => $ap) {
            $name = 'Associate Professor ' . $getLetterCode($idx);
            $facId = 'FAC-ECE-AP' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT);
            $prof = FacultyProfile::updateOrCreate(
                ['faculty_id' => $facId],
                [
                    'department_id' => $dept->id,
                    'name' => $name,
                    'pan' => 'ASPE' . rand(1000, 9999) . 'B',
                    'apaar_id' => 'APAAR-' . rand(10000000, 99999999),
                    'email' => strtolower(str_replace(' ', '.', $name)) . '@institution.edu',
                    'mobile' => '+91 9822' . rand(100000, 999999),
                    'joining_date' => $ap['join'],
                    'highest_degree' => $ap['deg'],
                    'degree_specialization' => $ap['spec'],
                    'university_awarded' => $ap['univ'],
                    'year_of_highest_degree' => (int)substr($ap['join'], 0, 4) - rand(0, 3),
                    'teaching_experience_years' => 2026 - (int)substr($ap['join'], 0, 4),
                    'is_active' => $ap['active'],
                ]
            );

            foreach ([$cay, $caym1, $caym2] as $y) {
                FacultyAcademicYear::updateOrCreate(
                    ['faculty_profile_id' => $prof->id, 'academic_year_id' => $y->id],
                    [
                        'program_id' => $program->id,
                        'designation' => 'Associate Professor',
                        'cadre' => 'Associate Professor',
                        'association_type' => $ap['assoc'],
                        'is_exclusive_to_program' => true,
                        'is_allied_department' => false,
                        'first_year_teaching_load_percentage' => 0.00,
                    ]
                );
            }
        }

        // Seed Assistant Professors
        foreach ($asstProfessorsData as $idx => $ast) {
            $name = 'Assistant Professor ' . $getLetterCode($idx);
            $facId = 'FAC-ECE-AST' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT);
            $prof = FacultyProfile::updateOrCreate(
                ['faculty_id' => $facId],
                [
                    'department_id' => $dept->id,
                    'name' => $name,
                    'pan' => 'ASTE' . rand(1000, 9999) . 'C',
                    'apaar_id' => 'APAAR-' . rand(10000000, 99999999),
                    'email' => strtolower(str_replace(' ', '.', $name)) . '@institution.edu',
                    'mobile' => '+91 9833' . rand(100000, 999999),
                    'joining_date' => $ast['join'],
                    'highest_degree' => $ast['deg'],
                    'degree_specialization' => $ast['spec'],
                    'university_awarded' => $ast['univ'],
                    'year_of_highest_degree' => (int)substr($ast['join'], 0, 4) - rand(0, 2),
                    'teaching_experience_years' => max(1, 2026 - (int)substr($ast['join'], 0, 4)),
                    'is_active' => $ast['active'],
                ]
            );

            foreach ([$cay, $caym1, $caym2] as $y) {
                FacultyAcademicYear::updateOrCreate(
                    ['faculty_profile_id' => $prof->id, 'academic_year_id' => $y->id],
                    [
                        'program_id' => $program->id,
                        'designation' => 'Assistant Professor',
                        'cadre' => 'Assistant Professor',
                        'association_type' => $ast['assoc'],
                        'is_exclusive_to_program' => true,
                        'is_allied_department' => false,
                        'first_year_teaching_load_percentage' => 0.00,
                    ]
                );
            }
        }

        // 2. Seed Visiting Faculty (Table 5.4.1)
        $visiting = [
            // CAYm1 (2024-25)
            [
                'program_id' => $program->id,
                'academic_year_id' => $caym1->id,
                'name' => 'Prof. Sanjeev Bedi',
                'designation' => 'Professor',
                'organization' => 'University of Waterloo, Canada',
                'course_name' => 'Capstone Projects (UEC797), Engineering Design Project-I (UTA016)',
                'course_code' => 'UEC797 / UTA016',
                'contribution_type' => 'Teaching & Capstone Mentoring',
                'interaction_hours' => 60.0,
            ],
            [
                'program_id' => $program->id,
                'academic_year_id' => $caym1->id,
                'name' => 'Prof. Roop Mahajan',
                'designation' => 'Professor',
                'organization' => 'Virginia Tech, USA',
                'course_name' => 'Engineering Materials (UES012) & CEEMS Research',
                'course_code' => 'UES012',
                'contribution_type' => 'Teaching & Research Projects',
                'interaction_hours' => 60.0,
            ],
            [
                'program_id' => $program->id,
                'academic_year_id' => $caym1->id,
                'name' => 'Prof. Niom Eliaz',
                'designation' => 'Professor',
                'organization' => 'Tel Aviv University, Israel',
                'course_name' => 'Research projects under COE-FSC',
                'course_code' => 'COE-FSC',
                'contribution_type' => 'Research Project Guidance',
                'interaction_hours' => 50.0,
            ],
            [
                'program_id' => $program->id,
                'academic_year_id' => $caym1->id,
                'name' => 'Prof. Shiva Sashtri',
                'designation' => 'Professor',
                'organization' => 'IISC Bangalore',
                'course_name' => 'Embedded Systems (UEC513), Computer Architecture (UEC610), IoT based Systems (UEC640)',
                'course_code' => 'UEC513',
                'contribution_type' => 'Curriculum Delivery & Labs',
                'interaction_hours' => 50.0,
            ],
            // CAYm2 (2023-24)
            [
                'program_id' => $program->id,
                'academic_year_id' => $caym2->id,
                'name' => 'Prof. Sanjeev Bedi',
                'designation' => 'Professor',
                'organization' => 'University of Waterloo, Canada',
                'course_name' => 'Capstone Projects (UEC797), Engineering Design Project-I & II',
                'course_code' => 'UEC797',
                'contribution_type' => 'Design Project Guidance',
                'interaction_hours' => 60.0,
            ],
            [
                'program_id' => $program->id,
                'academic_year_id' => $caym2->id,
                'name' => 'Prof. Roop Mahajan',
                'designation' => 'Professor',
                'organization' => 'Virginia Tech, USA',
                'course_name' => 'Engineering Materials (UES012) & CEEMS Research',
                'course_code' => 'UES012',
                'contribution_type' => 'Teaching & Research Projects',
                'interaction_hours' => 60.0,
            ],
            [
                'program_id' => $program->id,
                'academic_year_id' => $caym2->id,
                'name' => 'Prof. Niom Eliaz',
                'designation' => 'Professor',
                'organization' => 'Tel Aviv University, Israel',
                'course_name' => 'Research projects under COE-FSC',
                'course_code' => 'COE-FSC',
                'contribution_type' => 'Research Supervision',
                'interaction_hours' => 50.0,
            ],
            // CAYm3 (2022-23)
            [
                'program_id' => $program->id,
                'academic_year_id' => $caym3->id,
                'name' => 'Prof. Sanjeev Bedi',
                'designation' => 'Professor',
                'organization' => 'University of Waterloo, Canada',
                'course_name' => 'Capstone Projects (UEC797)',
                'course_code' => 'UEC797',
                'contribution_type' => 'Capstone Mentoring',
                'interaction_hours' => 60.0,
            ],
            [
                'program_id' => $program->id,
                'academic_year_id' => $caym3->id,
                'name' => 'Prof. Roop Mahajan',
                'designation' => 'Professor',
                'organization' => 'Virginia Tech, USA',
                'course_name' => 'Engineering Materials (UES012)',
                'course_code' => 'UES012',
                'contribution_type' => 'Guest Lectures',
                'interaction_hours' => 60.0,
            ],
            [
                'program_id' => $program->id,
                'academic_year_id' => $caym3->id,
                'name' => 'Prof. Niom Eliaz',
                'designation' => 'Professor',
                'organization' => 'Tel Aviv University, Israel',
                'course_name' => 'Research projects under CFS',
                'course_code' => 'CFS',
                'contribution_type' => 'Research Mentoring',
                'interaction_hours' => 50.0,
            ],
        ];

        foreach ($visiting as $v) {
            VisitingFaculty::updateOrCreate(
                [
                    'program_id' => $v['program_id'],
                    'academic_year_id' => $v['academic_year_id'],
                    'name' => $v['name'],
                    'course_name' => $v['course_name'],
                ],
                $v
            );
        }
    }
}
