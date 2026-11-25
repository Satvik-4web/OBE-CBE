<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Academic\Models\Institution;
use App\Domains\Academic\Models\Department;
use App\Domains\Academic\Models\AcademicYear;
use App\Domains\Academic\Models\Program;
use App\Domains\Academic\Models\AcademicIntake;
use App\Domains\Faculty\Models\FacultyProfile;
use App\Domains\Faculty\Models\FacultyAcademicYear;
use App\Domains\Faculty\Models\FacultyFdp;
use App\Domains\Faculty\Models\FacultyPublication;
use App\Domains\Curriculum\Models\Course;
use App\Domains\Curriculum\Models\ProgramOutcome;
use App\Domains\Curriculum\Models\ProgramSpecificOutcome;
use App\Domains\Curriculum\Models\CourseOutcome;
use App\Domains\Curriculum\Models\CoPoMapping;
use App\Domains\Attainment\Models\StudentAssessment;
use App\Domains\Attainment\Models\IndirectAssessment;
use App\Domains\Attainment\Models\AttainmentResult;
use App\Domains\Student\Models\StudentCohort;
use App\Domains\Student\Models\StudentPlacement;
use App\Domains\Finance\Models\FinancialBudget;
use App\Domains\Finance\Models\BudgetItem;
use App\Domains\SAR\Models\SarReport;
use App\Domains\SAR\Models\SarCriterion;
use App\Domains\SAR\Models\SarAuditLog;

class NbaSarDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Institution & Department
        $inst = Institution::firstOrCreate(
            ['code' => 'NIET-098'],
            [
                'name' => 'National Institute of Engineering & Technology',
                'head_of_institution' => 'Dr. R. K. Sharma (Principal / Director)',
                'address' => 'Knowledge Park Tech Zone, Phase II',
                'website' => 'https://niet-tech.edu.in',
            ]
        );

        $dept = Department::firstOrCreate(
            ['code' => 'ENC', 'institution_id' => $inst->id],
            [
                'name' => 'Department of Electronics and Computer Engineering',
                'hod_name' => 'Dr. Arvind Mehra, Ph.D.',
                'hod_email' => 'hod.enc@niet-tech.edu.in',
            ]
        );

        // Default System Users for Roles
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@nba-obe.edu'],
            [
                'name' => 'System Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('Admin@123'),
                'role' => \App\Models\User::ROLE_SUPER_ADMIN,
                'designation' => 'Chief Technology Officer & Admin',
                'phone' => '+91 9900112233',
                'is_active' => true,
                'remember_token' => \Illuminate\Support\Str::random(10),
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'hod.enc@niet-tech.edu.in'],
            [
                'name' => 'Dr. Arvind Mehra',
                'password' => \Illuminate\Support\Facades\Hash::make('Password@123'),
                'role' => \App\Models\User::ROLE_HOD,
                'department_id' => $dept->id,
                'designation' => 'Professor & Head of Department',
                'faculty_code' => 'FAC-ENC-001',
                'phone' => '+91 9811223344',
                'is_active' => true,
                'remember_token' => \Illuminate\Support\Str::random(10),
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'hod.cse@niet-tech.edu.in'],
            [
                'name' => 'Dr. Arvind Mehra',
                'password' => \Illuminate\Support\Facades\Hash::make('Password@123'),
                'role' => \App\Models\User::ROLE_HOD,
                'department_id' => $dept->id,
                'designation' => 'Professor & Head of Department',
                'faculty_code' => 'FAC-ENC-001',
                'phone' => '+91 9811223344',
                'is_active' => true,
                'remember_token' => \Illuminate\Support\Str::random(10),
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'faculty@niet-tech.edu.in'],
            [
                'name' => 'Prof. Rohit Kulkarni',
                'password' => \Illuminate\Support\Facades\Hash::make('Password@123'),
                'role' => \App\Models\User::ROLE_FACULTY,
                'department_id' => $dept->id,
                'designation' => 'Associate Professor & Course Coordinator',
                'faculty_code' => 'FAC-ENC-008',
                'phone' => '+91 9822334455',
                'is_active' => true,
                'remember_token' => \Illuminate\Support\Str::random(10),
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'iqac@niet-tech.edu.in'],
            [
                'name' => 'Dr. R. K. Sharma',
                'password' => \Illuminate\Support\Facades\Hash::make('Password@123'),
                'role' => \App\Models\User::ROLE_IQAC,
                'designation' => 'Principal & IQAC Chairman',
                'phone' => '+91 9833445566',
                'is_active' => true,
                'remember_token' => \Illuminate\Support\Str::random(10),
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'evaluator@nba-obe.edu'],
            [
                'name' => 'Prof. NBA Expert Committee',
                'password' => \Illuminate\Support\Facades\Hash::make('Password@123'),
                'role' => \App\Models\User::ROLE_EVALUATOR,
                'designation' => 'External Accreditation Peer Reviewer',
                'phone' => '+91 9844556677',
                'is_active' => true,
                'remember_token' => \Illuminate\Support\Str::random(10),
            ]
        );

        // 2. Academic Years (CAY, CAYm1, CAYm2, CAYm3)
        $cay = AcademicYear::firstOrCreate(['name' => '2024-25'], ['type' => 'CAY', 'start_year' => 2024, 'end_year' => 2025, 'is_current' => true]);
        $caym1 = AcademicYear::firstOrCreate(['name' => '2023-24'], ['type' => 'CAYm1', 'start_year' => 2023, 'end_year' => 2024, 'is_current' => false]);
        $caym2 = AcademicYear::firstOrCreate(['name' => '2022-23'], ['type' => 'CAYm2', 'start_year' => 2022, 'end_year' => 2023, 'is_current' => false]);
        $caym3 = AcademicYear::firstOrCreate(['name' => '2021-22'], ['type' => 'CAYm3', 'start_year' => 2021, 'end_year' => 2022, 'is_current' => false]);

        // 3. Program
        $prog = Program::firstOrCreate(
            ['code' => 'BTECH-ENC'],
            [
                'institution_id' => $inst->id,
                'department_id' => $dept->id,
                'name' => 'B.Tech in Electronics and Computer Engineering',
                'degree_level' => 'UG',
                'sanctioned_intake' => 120,
                'year_of_start' => 2008,
                'nba_tier' => 'Tier-II',
                'coordinator_name' => 'Dr. S. Narayan (NBA Coordinator)',
            ]
        );

        // 4. Academic Intake data (UGT1 + UGT2 + UGT3 + UGT4)
        if (AcademicIntake::where('program_id', $prog->id)->count() === 0) {
            AcademicIntake::create(['program_id' => $prog->id, 'academic_year_id' => $cay->id, 'ug_1st_year' => 120, 'ug_2nd_year' => 128, 'ug_3rd_year' => 125, 'ug_4th_year' => 122, 'lateral_entry' => 12]);
            AcademicIntake::create(['program_id' => $prog->id, 'academic_year_id' => $caym1->id, 'ug_1st_year' => 120, 'ug_2nd_year' => 126, 'ug_3rd_year' => 124, 'ug_4th_year' => 120, 'lateral_entry' => 12]);
            AcademicIntake::create(['program_id' => $prog->id, 'academic_year_id' => $caym2->id, 'ug_1st_year' => 120, 'ug_2nd_year' => 124, 'ug_3rd_year' => 120, 'ug_4th_year' => 118, 'lateral_entry' => 10]);
        }

        // 5. Faculty Profiles (Professors, Assoc Profs, Asst Profs)
        $facultyData = [
            ['name' => 'Dr. Arvind Mehra', 'faculty_id' => 'FAC-ENC-001', 'degree' => 'Ph.D. Computer Science', 'cadre' => 'Professor', 'exp' => 18, 'joining' => '2012-07-01'],
            ['name' => 'Dr. Sunita Varma', 'faculty_id' => 'FAC-ENC-002', 'degree' => 'Ph.D. AI & Deep Learning', 'cadre' => 'Professor', 'exp' => 16, 'joining' => '2014-06-15'],
            ['name' => 'Dr. Rajesh Deshmukh', 'faculty_id' => 'FAC-ENC-003', 'degree' => 'Ph.D. Cloud Computing', 'cadre' => 'Professor', 'exp' => 15, 'joining' => '2015-08-01'],
            ['name' => 'Dr. Priya Nambiar', 'faculty_id' => 'FAC-ENC-004', 'degree' => 'Ph.D. Cybersecurity', 'cadre' => 'Associate Professor', 'exp' => 12, 'joining' => '2017-07-10'],
            ['name' => 'Dr. Ankit Gupta', 'faculty_id' => 'FAC-ENC-005', 'degree' => 'Ph.D. Data Science', 'cadre' => 'Associate Professor', 'exp' => 11, 'joining' => '2018-01-05'],
            ['name' => 'Dr. Sneha Roy', 'faculty_id' => 'FAC-ENC-006', 'degree' => 'Ph.D. Computer Vision', 'cadre' => 'Associate Professor', 'exp' => 10, 'joining' => '2018-07-15'],
            ['name' => 'Dr. Vikas Patel', 'faculty_id' => 'FAC-ENC-007', 'degree' => 'Ph.D. IoT Systems', 'cadre' => 'Associate Professor', 'exp' => 9, 'joining' => '2019-06-01'],
            ['name' => 'Prof. Rohit Kulkarni', 'faculty_id' => 'FAC-ENC-008', 'degree' => 'M.Tech Software Engineering', 'cadre' => 'Associate Professor', 'exp' => 12, 'joining' => '2016-08-10'],
            ['name' => 'Prof. Neha Sen', 'faculty_id' => 'FAC-ENC-009', 'degree' => 'M.Tech Computer Networks', 'cadre' => 'Assistant Professor', 'exp' => 7, 'joining' => '2020-01-10'],
            ['name' => 'Prof. Amitava Ghosh', 'faculty_id' => 'FAC-ENC-010', 'degree' => 'M.Tech Machine Learning', 'cadre' => 'Assistant Professor', 'exp' => 6, 'joining' => '2020-07-01'],
            ['name' => 'Prof. Pooja Hegde', 'faculty_id' => 'FAC-ENC-011', 'degree' => 'M.Tech Distributed Systems', 'cadre' => 'Assistant Professor', 'exp' => 6, 'joining' => '2020-08-15'],
            ['name' => 'Prof. Manoj Iyer', 'faculty_id' => 'FAC-ENC-012', 'degree' => 'M.Tech Information Security', 'cadre' => 'Assistant Professor', 'exp' => 5, 'joining' => '2021-06-01'],
            ['name' => 'Prof. Shweta Joshi', 'faculty_id' => 'FAC-ENC-013', 'degree' => 'M.Tech Web Technologies', 'cadre' => 'Assistant Professor', 'exp' => 5, 'joining' => '2021-07-10'],
            ['name' => 'Prof. Deepali Rathi', 'faculty_id' => 'FAC-ENC-014', 'degree' => 'M.Tech Embedded Systems', 'cadre' => 'Assistant Professor', 'exp' => 4, 'joining' => '2022-01-15'],
            ['name' => 'Prof. Saurabh Mishra', 'faculty_id' => 'FAC-ENC-015', 'degree' => 'M.Tech Cloud Architecture', 'cadre' => 'Assistant Professor', 'exp' => 4, 'joining' => '2022-06-20'],
            ['name' => 'Prof. Kavita Rao', 'faculty_id' => 'FAC-ENC-016', 'degree' => 'M.Tech Database Systems', 'cadre' => 'Assistant Professor', 'exp' => 4, 'joining' => '2022-07-01'],
            ['name' => 'Prof. Tanmay Bhatia', 'faculty_id' => 'FAC-ENC-017', 'degree' => 'M.Tech Algorithm Design', 'cadre' => 'Assistant Professor', 'exp' => 3, 'joining' => '2023-01-05'],
            ['name' => 'Prof. Divya Nair', 'faculty_id' => 'FAC-ENC-018', 'degree' => 'M.Tech Big Data Analytics', 'cadre' => 'Assistant Professor', 'exp' => 3, 'joining' => '2023-06-15'],
            ['name' => 'Prof. Rahul Saxena', 'faculty_id' => 'FAC-ENC-019', 'degree' => 'M.Tech Computer Engineering', 'cadre' => 'Assistant Professor', 'exp' => 2, 'joining' => '2023-07-01'],
            ['name' => 'Prof. Meenakshi S.', 'faculty_id' => 'FAC-ENC-020', 'degree' => 'M.Tech AI & Robotics', 'cadre' => 'Assistant Professor', 'exp' => 2, 'joining' => '2024-01-10'],
            ['name' => 'Prof. Nitin Chawla', 'faculty_id' => 'FAC-ENC-021', 'degree' => 'M.Tech Blockchain Technologies', 'cadre' => 'Assistant Professor', 'exp' => 2, 'joining' => '2024-06-01'],
            ['name' => 'Prof. Ananya Mukherjee', 'faculty_id' => 'FAC-ENC-022', 'degree' => 'M.Tech Human Computer Interaction', 'cadre' => 'Assistant Professor', 'exp' => 1, 'joining' => '2024-07-01'],
            ['name' => 'Dr. Karan Malhotra', 'faculty_id' => 'FAC-ENC-023', 'degree' => 'Ph.D. Computer Science', 'cadre' => 'Associate Professor', 'exp' => 10, 'joining' => '2019-08-01'],
            ['name' => 'Prof. Riya Kapoor', 'faculty_id' => 'FAC-ENC-024', 'degree' => 'M.Tech Software Systems', 'cadre' => 'Assistant Professor', 'exp' => 5, 'joining' => '2021-08-02'],
            ['name' => 'Prof. Aditya Menon', 'faculty_id' => 'FAC-ENC-025', 'degree' => 'M.Tech Artificial Intelligence', 'cadre' => 'Assistant Professor', 'exp' => 4, 'joining' => '2022-07-04'],
            ['name' => 'Prof. Simran Kaur', 'faculty_id' => 'FAC-ENC-026', 'degree' => 'M.Tech Data Engineering', 'cadre' => 'Assistant Professor', 'exp' => 3, 'joining' => '2023-07-03'],
            ['name' => 'Prof. Arjun Verma', 'faculty_id' => 'FAC-ENC-027', 'degree' => 'M.Tech Computer Science', 'cadre' => 'Assistant Professor', 'exp' => 2, 'joining' => '2024-07-08'],
        ];

        foreach ($facultyData as $f) {
            $prof = FacultyProfile::firstOrCreate(
                ['faculty_id' => $f['faculty_id']],
                [
                    'department_id' => $dept->id,
                    'name' => $f['name'],
                    'pan' => 'ABCDE' . rand(1000, 9999) . 'Z',
                    'apaar_id' => 'APAAR-' . rand(10000000, 99999999),
                    'email' => strtolower(str_replace(['Dr. ', 'Prof. ', ' '], ['', '', '.'], $f['name'])) . '@niet-tech.edu.in',
                    'mobile' => '+91 98' . rand(10000000, 99999999),
                    'joining_date' => $f['joining'],
                    'highest_degree' => $f['degree'],
                    'degree_specialization' => $f['degree'],
                    'teaching_experience_years' => $f['exp'],
                    'is_active' => true,
                ]
            );

            // Assign to CAY, CAYm1, CAYm2
            foreach ([$cay, $caym1, $caym2] as $y) {
                FacultyAcademicYear::firstOrCreate(
                    ['faculty_profile_id' => $prof->id, 'academic_year_id' => $y->id],
                    [
                        'program_id' => $prog->id,
                        'designation' => $f['cadre'],
                        'cadre' => $f['cadre'],
                        'pay_scale' => str_contains($f['cadre'], 'Professor') ? '7th CPC Level 14' : (str_contains($f['cadre'], 'Associate') ? '7th CPC Level 13A' : '7th CPC Level 10'),
                        'association_type' => 'Regular',
                        'is_exclusive_to_program' => true,
                    ]
                );
            }

            // Faculty FDP
            if (FacultyFdp::where('faculty_profile_id', $prof->id)->count() === 0) {
                FacultyFdp::create([
                    'faculty_profile_id' => $prof->id,
                    'academic_year_id' => $cay->id,
                    'title' => 'Advanced Pedagogical Training & Outcome Based Education Implementation',
                    'duration_days' => 5,
                    'organizer' => 'AICTE - ATAL Academy',
                    'start_date' => '2024-09-10',
                    'end_date' => '2024-09-15',
                ]);
            }

            // Publication
            if (str_contains($f['degree'], 'Ph.D') && FacultyPublication::where('faculty_profile_id', $prof->id)->count() === 0) {
                FacultyPublication::create([
                    'faculty_profile_id' => $prof->id,
                    'academic_year_id' => $cay->id,
                    'title' => 'Scalable Distributed Architectures for High-Throughput Stream Processing',
                    'journal_conference_name' => 'IEEE Transactions on Computers',
                    'publication_type' => 'Journal',
                    'indexing' => 'SCI',
                    'impact_factor' => 3.85,
                    'published_date' => '2024-11-20',
                ]);
            }
        }

        // 6. Program Outcomes (PO1 to PO12)
        $nbaPos = [
            ['code' => 'PO1', 'title' => 'Engineering Knowledge', 'desc' => 'Apply knowledge of mathematics, science, engineering fundamentals, and electronics and computer engineering specialization.'],
            ['code' => 'PO2', 'title' => 'Problem Analysis', 'desc' => 'Identify, formulate, review research literature, and analyze complex engineering problems reaching substantiated conclusions.'],
            ['code' => 'PO3', 'title' => 'Design/Development of Solutions', 'desc' => 'Design solutions for complex engineering problems and design system components or processes that meet specified needs.'],
            ['code' => 'PO4', 'title' => 'Conduct Investigations of Complex Problems', 'desc' => 'Use research-based knowledge and research methods including design of experiments, analysis and interpretation of data.'],
            ['code' => 'PO5', 'title' => 'Modern Tool Usage', 'desc' => 'Create, select, and apply appropriate techniques, resources, and modern engineering and IT tools.'],
            ['code' => 'PO6', 'title' => 'The Engineer and Society', 'desc' => 'Apply reasoning informed by contextual knowledge to assess societal, health, safety, legal and cultural issues.'],
            ['code' => 'PO7', 'title' => 'Environment and Sustainability', 'desc' => 'Understand the impact of professional engineering solutions in societal and environmental contexts.'],
            ['code' => 'PO8', 'title' => 'Ethics', 'desc' => 'Apply ethical principles and commit to professional ethics and responsibilities and norms of engineering practice.'],
            ['code' => 'PO9', 'title' => 'Individual and Team Work', 'desc' => 'Function effectively as an individual, and as a member or leader in diverse teams, and in multidisciplinary settings.'],
            ['code' => 'PO10', 'title' => 'Communication', 'desc' => 'Communicate effectively on complex engineering activities with the engineering community and with society at large.'],
            ['code' => 'PO11', 'title' => 'Project Management and Finance', 'desc' => 'Demonstrate knowledge and understanding of engineering and management principles and apply these to one’s own work.'],
            ['code' => 'PO12', 'title' => 'Life-long Learning', 'desc' => 'Recognize the need for, and have the preparation and ability to engage in independent and life-long learning.'],
        ];

        $createdPos = [];
        foreach ($nbaPos as $p) {
            $createdPos[$p['code']] = ProgramOutcome::firstOrCreate(
                ['program_id' => $prog->id, 'po_code' => $p['code']],
                ['title' => $p['title'], 'description' => $p['desc']]
            );
        }

        // Program Specific Outcomes (PSOs)
        $nbaPsos = [
            ['code' => 'PSO1', 'title' => 'Hardware & Embedded Systems Architecture', 'desc' => 'Analyze, design, and validate electronic circuits, VLSI systems, microcontrollers, communication hardware, and IoT devices using modern EDA tools.'],
            ['code' => 'PSO2', 'title' => 'Intelligent Computing & Software Integration', 'desc' => 'Develop intelligent algorithms, networking protocols, and full-stack software integrated with embedded and computer systems.'],
        ];

        $createdPsos = [];
        foreach ($nbaPsos as $p) {
            $createdPsos[$p['code']] = ProgramSpecificOutcome::firstOrCreate(
                ['program_id' => $prog->id, 'pso_code' => $p['code']],
                ['title' => $p['title'], 'description' => $p['desc']]
            );
        }
        // 7. Courses & Articulation Matrix
        // Realistic B.Tech CSE course catalog covering Sem 1 to Sem 8
        if (Course::where('program_id', $prog->id)->count() === 0) {

            $coursesData = [
                [
                    'code' => 'UPH013',
                    'name' => 'Physics',
                    'sem' => 1,
                    'credits' => 4.5,
                    'cos' => [
                        'Understand damped and simple harmonic motion, the role of reverberation in designing a hall and generation and detection of ultrasonic waves.',
                        'Use Maxwell\'s equations to describe propagation of EM waves in a medium.',
                        'Demonstrate interference, diffraction and polarization of light.',
                        'Explain the working principle of Lasers.',
                        'Use the concept of wave function to find probability of a particle confined in a box.',
                        'Perform an experiment, collect data, tabulate and report them and interpret the results with error analysis.',
                    ],
                ],
                [
                    'code' => 'UES101',
                    'name' => 'Engineering Drawing',
                    'sem' => 1,
                    'credits' => 4.0,
                    'cos' => [
                        'Creatively comprehend the geometrical details of common engineering objects.',
                        'Draw dimensioned orthographic and isometric projections of simple engineering objects.',
                        'Interpret the meaning and intent of limits, fits and tolerances in the drawing.',
                        'Create/edit the engineering drawings for simple engineering objects using 2D drafting software.',
                        'Create/edit 3D models of engineering components using 3D modelling software.',
                    ],
                ],
                [
                    'code' => 'UHU003',
                    'name' => 'Professional Communication',
                    'sem' => 1,
                    'credits' => 3.0,
                    'cos' => [
                        'Apply communication concepts for effective interpersonal communication.',
                        'Speak assertively and effectively.',
                        'Interpret non-verbal cues in professional communication.',
                        'Write objectively, purposefully and effectively.',
                        'Design effective resumes and reports.',
                    ],
                ],
                [
                    'code' => 'UES102',
                    'name' => 'Manufacturing Processes',
                    'sem' => 1,
                    'credits' => 3.0,
                    'cos' => [
                        'Identify & analyse various machining processes/operations for manufacturing of industrial components.',
                        'Apply the basic principle of bulk and sheet metal forming operations.',
                        'Apply the knowledge of metal casting for different requirements.',
                        'Identify and analyse the requirements for achieving a sound welded joint and apply the concept of smart manufacturing.',
                    ],
                ],
                [
                    'code' => 'UMA022',
                    'name' => 'Calculus for Engineers',
                    'sem' => 1,
                    'credits' => 3.5,
                    'cos' => [
                        'Examine functions of several variables, define and compute partial derivatives, directional derivatives, and their use in finding maxima and minima in engineering problems.',
                        'Evaluate multiple integrals in Cartesian and Polar coordinates, and their applications to find areas and volumes.',
                        'Determine the convergence/divergence of infinite series, approximation of functions using power and series expansion and error estimation in engineering problems.',
                        'Represent complex numbers in Cartesian and Polar forms and test the analyticity of complex functions by using Cauchy Riemann equations.',
                    ],
                ],
                [
                    'code' => 'UES104',
                    'name' => 'Boot Camp',
                    'sem' => 1,
                    'credits' => 1.0,
                    'cos' => [
                        'Acquire orientation on engineering fundamentals and interdisciplinary tools.',
                        'Understand institutional ethics, collaborative workflows, and campus academic resources.',
                    ],
                ],
                [
                    'code' => 'UCB009',
                    'name' => 'Chemistry',
                    'sem' => 2,
                    'credits' => 4.0,
                    'cos' => [
                        'Recognize principles and applications of atomic and molecular spectroscopy.',
                        'Explain the concepts of conductometric titrations, modern batteries and corrosion.',
                        'Apply and execute water quality parameter and treatment methods.',
                        'Discuss the concept of alternative fuels, application of polymers and SMILES.',
                        'Execute laboratory techniques like pH metry, potentiometry, spectrophotometry, conductometry and volumetry.',
                    ],
                ],
                [
                    'code' => 'UES103',
                    'name' => 'Programming for Problem Solving',
                    'sem' => 2,
                    'credits' => 4.0,
                    'cos' => [
                        'Comprehend and analyze the concepts of number system, memory, compilation and debugging of the programs in C language.',
                        'Analyze the control & iterative statements to solve problems with C language source codes.',
                        'Design and create programs for problem solving involving arrays, strings and pointers.',
                        'Evaluate and analyze programming concepts based on user-defined data types and file handling using C language.',
                    ],
                ],
                [
                    'code' => 'UES013',
                    'name' => 'Electrical & Electronics Engineering',
                    'sem' => 2,
                    'credits' => 4.5,
                    'cos' => [
                        'Apply various networks laws and theorems to solve dc circuits.',
                        'Compute different ac quantities with phasor representation.',
                        'Comprehend the operation in magnetic circuits, single phase transformer.',
                        'Recognize and apply number systems and simplification of Boolean expressions using Boolean algebra and K-Map.',
                        'Solve and implement logical functions using logic gates, decoders, and multiplexers.',
                        'Discuss and explain the working of Flip-Flops, diode, transistor and operational amplifier, their configurations and applications.',
                    ],
                ],
                [
                    'code' => 'UEN008',
                    'name' => 'Energy and Environment',
                    'sem' => 2,
                    'credits' => 2.0,
                    'cos' => [
                        'Comprehend the interdisciplinary context of environmental issues with reference to sustainability.',
                        'Assess the impact of anthropogenic activities on the various elements of environment and apply suitable techniques to mitigate their impact.',
                        'Demonstrate the application of technology in real time assessment and control of pollutants.',
                        'Correlate environmental concerns with conventional energy sources and assess the uses and limitations of non-conventional energy technologies.',
                    ],
                ],
                [
                    'code' => 'UMA023',
                    'name' => 'Differential Equations and Linear Algebra',
                    'sem' => 2,
                    'credits' => 3.5,
                    'cos' => [
                        'Solve differential equations of first and 2nd order and basic application problems described by these equations.',
                        'Determine Laplace transforms and inverse Laplace transforms for various functions and solve initial and boundary value problems.',
                        'Determine Fourier series expansions of periodic functions and solve heat and wave equations.',
                        'Solve systems of linear equations using elementary row operations, compute bases/orthonormal bases, express linear transformation in matrix terms and find eigenvalues and eigenvectors.',
                    ],
                ],
                [
                    'code' => 'UNC401',
                    'name' => 'Data Structures',
                    'sem' => 3,
                    'credits' => 4.0,
                    'cos' => [
                        'Implement basic data structures and solve problems using fundamental algorithms.',
                        'Implement various searching and sorting techniques.',
                        'Analyze the complexity of algorithms to provide justification for selection and implementation.',
                        'Apply appropriate data structure and algorithmic technique to solve real-world problems.',
                    ],
                ],
                [
                    'code' => 'UNC305',
                    'name' => 'Discrete Mathematics',
                    'sem' => 3,
                    'credits' => 3.5,
                    'cos' => [
                        'Apply Knowledge of mathematics in truth tables and the rules of propositional & predicate calculus.',
                        'Apply the logical and analytical structure of proofs and work symbolically with connectives and quantifiers to produce logically valid arguments.',
                        'Evaluate elementary mathematical arguments and identify fallacious reasoning.',
                        'Analyze real-world problems using graph theory.',
                    ],
                ],
                [
                    'code' => 'UNC302',
                    'name' => 'Object Oriented Programming',
                    'sem' => 3,
                    'credits' => 4.0,
                    'cos' => [
                        'Explain OOP principles, compare with procedural programming, and explore classes, objects, and methods.',
                        'Implement data abstraction using classes and constructors, and achieve polymorphism through function and operator overloading.',
                        'Apply inheritance and polymorphism concepts to enhance code reusability.',
                        'Analyze I/O file stream, templates and exception handling for robust programming.',
                    ],
                ],
                [
                    'code' => 'UNC501',
                    'name' => 'Operating Systems',
                    'sem' => 3,
                    'credits' => 4.0,
                    'cos' => [
                        'Explain the basics of an operating system viz. system programs, system calls, user mode and kernel mode.',
                        'Select a particular CPU scheduling algorithm for specific situation, and analyze the environment leading to deadlock and its rectification.',
                        'Comprehend concepts related to concurrency, synchronization, and race conditions.',
                        'Explicate memory management techniques viz. caching, paging, segmentation, virtual memory, and thrashing.',
                        'Understand concepts related to file systems, disk-scheduling, and file storage structures.',
                    ],
                ],
                [
                    'code' => 'UEC304',
                    'name' => 'Electronics Devices and Circuits',
                    'sem' => 3,
                    'credits' => 4.5,
                    'cos' => [
                        'Understand the working of PN junction diode and MOS transistor operation.',
                        'Analyze static and dynamic characteristics of Resistive load Inverter and CMOS inverter.',
                        'Implement various CMOS logic circuits and transmission gates.',
                        'Illustrate the fabrication process and design rules for NMOS and CMOS circuits.',
                    ],
                ],
                [
                    'code' => 'UCS321',
                    'name' => 'AI for Engineers',
                    'sem' => 3,
                    'credits' => 3.0,
                    'cos' => [
                        'Understand foundational artificial intelligence concepts, problem formulation, and search strategies.',
                        'Apply knowledge representation, logical reasoning, and probabilistic inference to engineering problems.',
                        'Implement supervised and unsupervised machine learning models using contemporary AI libraries.',
                        'Evaluate AI solutions on real-world engineering datasets considering performance and ethical dimensions.',
                    ],
                ],
                [
                    'code' => 'UTD003',
                    'name' => 'Aptitude Skills Building',
                    'sem' => 3,
                    'credits' => 2.0,
                    'cos' => [
                        'Ability to identify and provide solutions for complex problems and apply the basics of mathematics.',
                        'Able to analyze and interpret data from various sources to identify trends and make data-driven comparisons.',
                        'Able to evaluate logical reasoning scenarios to identify patterns, solve sequences, and make accurate inferences.',
                        'Able to improve communication skills, including correcting errors, refining structure, and enhancing verbal proficiency.',
                        'Able to analyze various types of information to identify key points, infer meaning, and understand underlying tones and purposes.',
                    ],
                ],
                [
                    'code' => 'UEC716',
                    'name' => 'Database Management Systems',
                    'sem' => 4,
                    'credits' => 4.0,
                    'cos' => [
                        'Analyze Information Systems as socio-technical systems, its need and advantages as compared to traditional file-based systems.',
                        'Analyze and design database using E-R data model by identifying entities, attributes and relationships.',
                        'Apply and create Relational Database Design process with Normalization techniques.',
                        'Comprehend concepts of transaction management, concurrency control and recovery management.',
                        'Demonstrate use of SQL and PL/SQL to implement database applications.',
                    ],
                ],
                [
                    'code' => 'UNC311',
                    'name' => 'Probability and Statistics',
                    'sem' => 4,
                    'credits' => 3.5,
                    'cos' => [
                        'Analyze and compute statistical measures to describe and interpret data characteristics and distribution behaviour across applications.',
                        'Apply probability and random variable concepts to model and analyze discrete and continuous probability distributions.',
                        'Analyze random processes and noise models by evaluating stationarity, ergodicity, correlation functions, and power spectral density.',
                        'Evaluate estimation and hypothesis testing methods by assessing bias, variance, mean square error (MSE), confidence intervals, and error probabilities for engineering problems.',
                        'Analyze information-theoretic models and coding schemes by computing entropy, mutual information, and channel capacity, and applying source coding theorems to improve transmission efficiency and reliability.',
                    ],
                ],
                [
                    'code' => 'UNC402',
                    'name' => 'Design and Analysis of Algorithms',
                    'sem' => 4,
                    'credits' => 4.0,
                    'cos' => [
                        'Analyse the complexity of algorithms and implement it in a specific scenario.',
                        'Apply common algorithmic techniques such as greedy, dynamic programming etc. to standard computational problems.',
                        'Design solutions by using appropriate data structures or applying algorithms such as randomized, approximation and graph.',
                        'Develop efficient algorithms for various computational challenging problems solving in computing.',
                    ],
                ],
                [
                    'code' => 'UEC612',
                    'name' => 'Digital System Design',
                    'sem' => 4,
                    'credits' => 4.5,
                    'cos' => [
                        'Apply various minimization methods to simplify boolean functions and construct circuits using logic gates.',
                        'Design combinational circuits and iterative networks using basic modules.',
                        'Design and analyze sequential circuits and finite state machines.',
                        'Evaluate performance of integrated circuits across logic families.',
                        'Design, simulate and synthesize digital systems using HDL (VHDL/Verilog).',
                    ],
                ],
                [
                    'code' => 'UNC403',
                    'name' => 'Machine Learning',
                    'sem' => 4,
                    'credits' => 4.0,
                    'cos' => [
                        'Comprehend fundamental concepts and terminology associated with machine learning and artificial intelligence.',
                        'Apply various regression and classification techniques and evaluate models.',
                        'Implement and apply machine learning algorithms for supervised, unsupervised, and reinforcement learning tasks.',
                        'Apply and evaluate neural network models using gradient-based learning and model selection techniques.',
                    ],
                ],
                [
                    'code' => 'UTA024',
                    'name' => 'Engineering Design Project (Buggy)',
                    'sem' => 4,
                    'credits' => 2.0,
                    'cos' => [
                        'Recognize issues to be addressed in combined hardware and software system design.',
                        'Draw schematic diagrams of electronic circuits and design PCB layouts using CAD tools.',
                        'Apply hands-on experience in electronic circuit implementation and testing.',
                        'Demonstrate programming skills by integrating coding, optimization and debugging for different robotic challenges.',
                        'Develop group working, including task sub-division and integration of individual contributions from the team.',
                    ],
                ],
                [
                    'code' => 'UHU052',
                    'name' => 'The Evolutionary Basis of Human Behavior for Engineers',
                    'sem' => 4,
                    'credits' => 1.0,
                    'cos' => [
                        'Understand evolutionary principles influencing human behavior and cognition.',
                        'Analyze social dynamics, cooperation, and group decision-making in technical teams.',
                        'Apply behavioral insights to human-centered engineering design and workplace ethics.',
                    ],
                ],
                [
                    'code' => 'UCS421',
                    'name' => 'Ethics and Risk Mitigation in AI',
                    'sem' => 4,
                    'credits' => 3.0,
                    'cos' => [
                        'Identify ethical challenges, algorithmic biases, and societal impacts in AI deployments.',
                        'Apply regulatory frameworks, governance models, and data privacy principles to AI systems.',
                        'Formulate risk mitigation strategies, fairness audits, and accountability mechanisms for AI engineering.',
                    ],
                ],
                [
                    'code' => 'UNC303',
                    'name' => 'Computer and Communication Networks',
                    'sem' => 5,
                    'credits' => 3.0,
                    'cos' => [
                        'Understand the layered architecture of Internet\'s reference models: OSI & TCP/IP and basis of physical layer and media.',
                        'Analyze design issues, framing, error detection and correction, channel allocation techniques and link layer protocols.',
                        'Evaluate network layer, internetworking, IP and IP addressing mechanisms.',
                        'Apply various routing algorithms, elements of transport protocols, congestion control, and QOS in networks.',
                        'Understand various communication applications like email, web browser, and familiarization with cryptography and network security.',
                    ],
                ],
                [
                    'code' => 'UEC610',
                    'name' => 'Computer Architecture',
                    'sem' => 5,
                    'credits' => 3.5,
                    'cos' => [
                        'Apply Amdahl\'s law and Processor Performance Equation to evaluate system performance.',
                        'Characterize processor instruction set architecture and addressing modes.',
                        'Identify concepts of parallelism, pipelining, and hazard removal.',
                        'Demonstrate GPU-based system acceleration using CUDA programming.',
                        'Investigate Memory Hierarchy design, memory addressing, and address translation.',
                    ],
                ],
                [
                    'code' => 'UEC504',
                    'name' => 'Signal System and Processing',
                    'sem' => 5,
                    'credits' => 4.5,
                    'cos' => [
                        'Analyze different properties of continuous and discrete-time signals and systems.',
                        'Represent signals and systems in frequency domain using Fourier tools.',
                        'Apply Z-transform to analyze discrete-time signals and systems.',
                        'Design and implement digital discrete-time filters (FIR/IIR).',
                    ],
                ],
                [
                    'code' => 'UNC514',
                    'name' => 'Data Science Fundamentals (Elective-I)',
                    'sem' => 5,
                    'credits' => 3.0,
                    'cos' => [
                        'Understand data science pipelines and methods for acquiring structured and unstructured data.',
                        'Perform exploratory data analysis and statistical visualization.',
                        'Build and evaluate predictive regression and classification models.',
                        'Communicate data insights and deploy scalable data analysis workflows.',
                    ],
                ],
                [
                    'code' => 'UEC523',
                    'name' => 'Applications of AI for Electronic Systems',
                    'sem' => 5,
                    'credits' => 3.0,
                    'cos' => [
                        'Understand machine learning techniques tailored for electronic sensor data.',
                        'Implement TinyML and embedded AI models on microcontrollers and edge hardware.',
                        'Optimize neural network models for power, latency, and memory-constrained electronic systems.',
                        'Apply AI algorithms to fault detection, circuit optimization, and signal classification.',
                    ],
                ],
                [
                    'code' => 'UEC522',
                    'name' => 'Practices in Electronics Fabrication',
                    'sem' => 5,
                    'credits' => 2.0,
                    'cos' => [
                        'Explain fundamentals of integrated circuits, semiconductor materials, crystal growth, and wafer preparation.',
                        'Apply principles of oxidation, diffusion, ion implantation, lithography, etching, and metallization to describe planar IC fabrication.',
                        'Analyse and apply various IC Design rules.',
                        'Understand Packaging technologies by considering electrical, thermal, and mechanical performance requirements.',
                    ],
                ],
                [
                    'code' => 'UTA025',
                    'name' => 'Innovation & Entrepreneurship',
                    'sem' => 5,
                    'credits' => 3.0,
                    'cos' => [
                        'Explain fundamentals behind entrepreneurial personality and intentions.',
                        'Discover, evaluate, and validate business opportunities.',
                        'Identify stakeholders and develop strong value propositions.',
                        'Describe business models and construct a functional Business Model Canvas.',
                        'Analyse and select suitable finance and revenue models for startup ventures.',
                    ],
                ],
                [
                    'code' => 'UEC614',
                    'name' => 'Digital Communication',
                    'sem' => 6,
                    'credits' => 4.5,
                    'cos' => [
                        'Explain concepts of AM, FM, and digital pulse modulation.',
                        'Analyze pulse modulation techniques and baseband waveform shaping.',
                        'Perform statistical analysis of modulated waveforms from estimation and detection perspective.',
                        'Evaluate performance of digital modulation techniques operating over AWGN channels.',
                        'Analyze and apply error control coding in digital communication systems.',
                    ],
                ],
                [
                    'code' => 'UEC615',
                    'name' => 'Embedded Systems and IoT',
                    'sem' => 6,
                    'credits' => 4.0,
                    'cos' => [
                        'Understand embedded system models using C++ language.',
                        'Understand internal architecture of ARM processor, register structure, and addressing modes.',
                        'Design ARM interfacing with memory, I/O devices, and peripherals using C++.',
                        'Analyze design aspects of hardware and software components of IoT including protocols.',
                        'Design interfaces for I/O devices, sensors, and communication modules.',
                    ],
                ],
                [
                    'code' => 'UNC602',
                    'name' => 'Theory of Computation',
                    'sem' => 6,
                    'credits' => 3.5,
                    'cos' => [
                        'Comprehend regular languages and finite automata and establish equivalence between regular expressions, NFAs, and DFAs.',
                        'Disambiguate context-free grammars and understand concepts of context-free languages and pushdown automata.',
                        'Analyse and design efficient Turing Machines.',
                        'Distinguish different computing languages and classify their respective computational complexity types.',
                    ],
                ],
                [
                    'code' => 'UEC633',
                    'name' => 'Network Security and Cryptography (Elective-II)',
                    'sem' => 6,
                    'credits' => 3.0,
                    'cos' => [
                        'Understand cryptographic principles and classical ciphers.',
                        'Apply symmetric and asymmetric encryption algorithms to secure communications.',
                        'Implement hashing, digital signatures, and authentication protocols.',
                        'Analyze network vulnerabilities and design defense-in-depth security strategies.',
                    ],
                ],
                [
                    'code' => 'UEC635',
                    'name' => 'Blockchain Technology (Elective-III)',
                    'sem' => 6,
                    'credits' => 3.0,
                    'cos' => [
                        'Explain concepts of distributed ledgers and blockchain architectures.',
                        'Analyze consensus algorithms including Proof of Work and Proof of Stake.',
                        'Develop and deploy smart contracts on modern blockchain platforms.',
                        'Evaluate enterprise blockchain use-cases in supply chain, identity, and finance.',
                    ],
                ],
                [
                    'code' => 'UNC603',
                    'name' => 'Programming Practices Lab - I',
                    'sem' => 6,
                    'credits' => 2.0,
                    'cos' => [
                        'Apply programming skills in Java/Python to solve computational problems.',
                        'Implement solutions using appropriate data structures and algorithmic techniques.',
                        'Develop logical reasoning and structured problem-solving ability through coding practice.',
                        'Perform timed programming tasks with attention to correctness and efficiency.',
                    ],
                ],
                [
                    'code' => 'UEC797_A',
                    'name' => 'Capstone Project (Starts - Part I)',
                    'sem' => 6,
                    'credits' => 1.0,
                    'cos' => [
                        'Identify a technological need in society or industry and formulate it into a specific engineering problem.',
                        'Design and develop practical hardware, software, or integrated solutions using interdisciplinary engineering knowledge.',
                        'Recognize the importance of engineering solutions that consider safety, economic, environmental, and societal impacts.',
                        'Demonstrate effective teamwork by contributing meaningfully to group-based project tasks in a multidisciplinary environment.',
                        'Communicate technical ideas and project outcomes clearly and professionally with diverse audiences.',
                        'Assess engineering solutions with respect to ethical responsibilities adhering to professional canons.',
                    ],
                ],
                [
                    'code' => 'UHU005',
                    'name' => 'Humanities for Engineers',
                    'sem' => 7,
                    'credits' => 3.0,
                    'cos' => [
                        'Improve understanding of human behavior with the help of interplay of professional, psychological and economic activities.',
                        'Apply knowledge of basic principles of psychology, economics and ethics for the solution of engineering problems.',
                        'Explain the impact of contemporary issues in psychology, economics and ethical principles on engineering.',
                    ],
                ],
                [
                    'code' => 'UEC823',
                    'name' => 'Soft Computing (Elective-IV)',
                    'sem' => 7,
                    'credits' => 3.0,
                    'cos' => [
                        'Understand theoretical foundations of fuzzy logic, neural networks, and evolutionary algorithms.',
                        'Design and implement fuzzy inference systems for engineering control problems.',
                        'Apply genetic algorithms to solve complex non-linear optimization tasks.',
                        'Develop hybrid intelligent systems combining neural and fuzzy approaches.',
                    ],
                ],
                [
                    'code' => 'UCS002',
                    'name' => 'Introduction to Cyber Security (Generic Elective)',
                    'sem' => 7,
                    'credits' => 2.0,
                    'cos' => [
                        'Understand fundamental concepts of cyberspace, security threats, and vulnerabilities.',
                        'Apply security practices to safeguard personal and organizational digital assets.',
                        'Recognize legal and ethical implications under national and international cyber laws.',
                    ],
                ],
                [
                    'code' => 'UVD701',
                    'name' => 'CAD for VLSI (Elective-V)',
                    'sem' => 7,
                    'credits' => 3.0,
                    'cos' => [
                        'Explain electronic design automation (EDA) flows for VLSI circuits.',
                        'Implement partitioning, floorplanning, and placement algorithms.',
                        'Analyze routing algorithms and timing optimization techniques.',
                        'Use industry CAD tools to synthesize, simulate, and verify integrated circuits.',
                    ],
                ],
                [
                    'code' => 'UNC703',
                    'name' => 'Programming Practices Lab - II',
                    'sem' => 7,
                    'credits' => 1.0,
                    'cos' => [
                        'Apply advanced programming techniques in Java/Python to complex algorithmic problems.',
                        'Implement tree and graph algorithms with optimal time and space complexity.',
                        'Formulate strategies for complex competitive programming and technical interviews.',
                        'Perform timed problem solving demonstrating code robustness and edge-case handling.',
                    ],
                ],
                [
                    'code' => 'UEC797_B',
                    'name' => 'Capstone Project (End - Part II)',
                    'sem' => 7,
                    'credits' => 8.0,
                    'cos' => [
                        'Formulate and implement end-to-end engineering solutions addressing real-world problems.',
                        'Validate engineered prototypes through rigorous experimental and simulation benchmarks.',
                        'Evaluate societal, safety, and environmental impacts of the engineered solution.',
                        'Demonstrate effective team leadership and collaborative workflow management.',
                        'Defend project deliverables through professional oral presentations and technical reports.',
                        'Uphold professional ethics and intellectual property standards in engineering practice.',
                    ],
                ],
                [
                    'code' => 'UNC801',
                    'name' => 'Information Retrieval',
                    'sem' => 8,
                    'credits' => 3.0,
                    'cos' => [
                        'Comprehend fundamental concepts and terminology associated with Information retrieval.',
                        'Understand and evaluate various types of indices and efficient storage of indices.',
                        'Evaluate scoring strategies and ranking models.',
                        'Analyze and implement advanced indexing, link analysis, and duplicate detection techniques for scalable web search.',
                        'Utilize machine learning methods for intelligent information retrieval.',
                    ],
                ],
                [
                    'code' => 'UNC802',
                    'name' => 'Prompt Engineering',
                    'sem' => 8,
                    'credits' => 3.0,
                    'cos' => [
                        'Understand basic concepts of prompt engineering and its role in Generative AI systems.',
                        'Write clear and effective prompts using common prompting techniques.',
                        'Apply prompt design methods for structured and multi-step engineering tasks.',
                        'Evaluate AI-generated responses and demonstrate responsible and ethical use of Generative AI tools.',
                    ],
                ],
                [
                    'code' => 'UNC891',
                    'name' => 'Project / Internship Semester',
                    'sem' => 8,
                    'credits' => 9.0,
                    'cos' => [
                        'Formulate and execute industry-relevant technical solutions applying electronics and computer engineering principles.',
                        'Implement, test, and document complex engineering artifacts adhering to professional engineering standards.',
                        'Demonstrate effective project management, oral defense, and professional ethics in workplace settings.',
                    ],
                ],
            ];

            foreach ($coursesData as $c) {

                $course = Course::create([
                    'program_id' => $prog->id,
                    'academic_year_id' => $cay->id,
                    'course_code' => $c['code'],
                    'course_name' => $c['name'],
                    'semester' => $c['sem'],
                    'credits' => $c['credits'],
                    'lecture_hours' => 3,
                    'tutorial_hours' => 0,
                    'practical_hours' => 2,
                    'course_type' => 'Core',
                ]);

                // Create 5 meaningful Course Outcomes per course
                foreach ($c['cos'] as $index => $description) {

                    $coNumber = $index + 1;

                    $bloomLevel = match ($coNumber) {
                        1 => 'Understand',
                        2 => 'Apply',
                        3 => 'Apply',
                        4 => 'Analyze',
                        5 => 'Evaluate',
                        default => 'Create',
                    };

                    $co = CourseOutcome::create([
                        'course_id' => $course->id,
                        'co_code' => "CO{$coNumber}",
                        'description' => $description,
                        'target_percentage' => 65.0,
                        'target_level' => 2.5,
                        'bloom_level' => $bloomLevel,
                    ]);

                    // CO-PO articulation matrix
                    $mapPos = [
                        'PO1' => 3,
                        'PO2' => 3,
                        'PO3' => 3,
                        'PO5' => 2,
                        'PO9' => 2,
                        'PO12' => 2,
                    ];

                    foreach ($mapPos as $poKey => $lvl) {

                        if (isset($createdPos[$poKey])) {

                            $correlationLevel = max(
                                1,
                                min(
                                    3,
                                    $lvl + (($coNumber % 3) - 1)
                                )
                            );

                            CoPoMapping::create([
                                'course_outcome_id' => $co->id,
                                'target_type' => 'PO',
                                'target_id' => $createdPos[$poKey]->id,
                                'correlation_level' => $correlationLevel,
                                'justification' => "The {$co['co_code']} outcome of {$c['name']} contributes to {$poKey} through course-specific knowledge, analysis and practical application.",
                            ]);
                        }
                    }

                    // CO-PSO articulation
                    foreach ($createdPsos as $psoKey => $psoModel) {

                        CoPoMapping::create([
                            'course_outcome_id' => $co->id,
                            'target_type' => 'PSO',
                            'target_id' => $psoModel->id,
                            'correlation_level' => 2,
                            'justification' => "The {$co['co_code']} outcome supports {$psoKey} through application of computing knowledge and industry-oriented skills.",
                        ]);
                    }

                    // CIE Assessment
                    StudentAssessment::create([
                        'course_id' => $course->id,
                        'course_outcome_id' => $co->id,
                        'academic_year_id' => $cay->id,
                        'assessment_type' => 'Internal_Test_1',
                        'total_students_attempted' => 125,
                        'students_meeting_target' => 98,
                        'percentage_achieved' => 78.4,
                        'attainment_level' => 2.8,
                    ]);

                    // SEE Assessment
                    StudentAssessment::create([
                        'course_id' => $course->id,
                        'course_outcome_id' => $co->id,
                        'academic_year_id' => $cay->id,
                        'assessment_type' => 'End_Sem',
                        'total_students_attempted' => 125,
                        'students_meeting_target' => 92,
                        'percentage_achieved' => 73.6,
                        'attainment_level' => 2.6,
                    ]);

                    // CO Attainment Result
                    AttainmentResult::create([
                        'course_outcome_id' => $co->id,
                        'academic_year_id' => $cay->id,
                        'direct_cie_score' => 2.8,
                        'direct_see_score' => 2.6,
                        'direct_attainment' => 2.64,
                        'indirect_attainment' => 2.55,
                        'final_score' => 2.62,
                    ]);
                }
            }
        }
        // 8. Indirect Assessments (Surveys)
        if (IndirectAssessment::where('program_id', $prog->id)->count() === 0) {
            foreach ($createdPos as $poCode => $poModel) {
                IndirectAssessment::create(['program_id' => $prog->id, 'academic_year_id' => $cay->id, 'survey_type' => 'Graduate_Exit_Survey', 'target_type' => 'PO', 'target_id' => $poModel->id, 'average_rating' => 2.65, 'total_respondents' => 110]);
                IndirectAssessment::create(['program_id' => $prog->id, 'academic_year_id' => $cay->id, 'survey_type' => 'Alumni_Survey', 'target_type' => 'PO', 'target_id' => $poModel->id, 'average_rating' => 2.50, 'total_respondents' => 75]);
                IndirectAssessment::create(['program_id' => $prog->id, 'academic_year_id' => $cay->id, 'survey_type' => 'Employer_Survey', 'target_type' => 'PO', 'target_id' => $poModel->id, 'average_rating' => 2.70, 'total_respondents' => 30]);
            }

            foreach ($createdPsos as $psoCode => $psoModel) {
                IndirectAssessment::create(['program_id' => $prog->id, 'academic_year_id' => $cay->id, 'survey_type' => 'Graduate_Exit_Survey', 'target_type' => 'PSO', 'target_id' => $psoModel->id, 'average_rating' => 2.70, 'total_respondents' => 110]);
                IndirectAssessment::create(['program_id' => $prog->id, 'academic_year_id' => $cay->id, 'survey_type' => 'Alumni_Survey', 'target_type' => 'PSO', 'target_id' => $psoModel->id, 'average_rating' => 2.55, 'total_respondents' => 75]);
                IndirectAssessment::create(['program_id' => $prog->id, 'academic_year_id' => $cay->id, 'survey_type' => 'Employer_Survey', 'target_type' => 'PSO', 'target_id' => $psoModel->id, 'average_rating' => 2.75, 'total_respondents' => 30]);
            }
        }

        // 9. Student Cohorts (Criterion 4)
        if (StudentCohort::where('program_id', $prog->id)->count() === 0) {
            StudentCohort::create(['program_id' => $prog->id, 'batch_name' => '2020-2024 (CAYm1)', 'entry_year' => 2020, 'graduation_year' => 2024, 'cohort_type' => 'CAYm1', 'sanctioned_intake' => 120, 'admitted_first_year' => 120, 'lateral_admitted_second_year' => 12, 'graduated_without_backlog' => 102, 'graduated_in_stipulated_time' => 122]);
            StudentCohort::create(['program_id' => $prog->id, 'batch_name' => '2019-2023 (CAYm2)', 'entry_year' => 2019, 'graduation_year' => 2023, 'cohort_type' => 'CAYm2', 'sanctioned_intake' => 120, 'admitted_first_year' => 118, 'lateral_admitted_second_year' => 10, 'graduated_without_backlog' => 96, 'graduated_in_stipulated_time' => 116]);
            StudentCohort::create(['program_id' => $prog->id, 'batch_name' => '2018-2022 (CAYm3)', 'entry_year' => 2018, 'graduation_year' => 2022, 'cohort_type' => 'CAYm3', 'sanctioned_intake' => 120, 'admitted_first_year' => 120, 'lateral_admitted_second_year' => 12, 'graduated_without_backlog' => 94, 'graduated_in_stipulated_time' => 114]);
        }

        // Placements (Criterion 4.5)
        if (StudentPlacement::where('program_id', $prog->id)->count() === 0) {
            StudentPlacement::create(['program_id' => $prog->id, 'academic_year_id' => $cay->id, 'final_year_students' => 122, 'placed_students' => 96, 'higher_studies_students' => 14, 'entrepreneurs_count' => 3, 'median_salary_lpa' => 7.8]);
            StudentPlacement::create(['program_id' => $prog->id, 'academic_year_id' => $caym1->id, 'final_year_students' => 120, 'placed_students' => 92, 'higher_studies_students' => 12, 'entrepreneurs_count' => 2, 'median_salary_lpa' => 7.2]);
            StudentPlacement::create(['program_id' => $prog->id, 'academic_year_id' => $caym2->id, 'final_year_students' => 118, 'placed_students' => 88, 'higher_studies_students' => 15, 'entrepreneurs_count' => 2, 'median_salary_lpa' => 6.5]);
        }

             // 10. Financial Budgets (Criterion 10)
        // Three-year recurring and non-recurring budget ledger
        if (FinancialBudget::where('program_id', $prog->id)->count() === 0) {

            // ==================== 2024-25 (CFY) ====================
            $cfyBudget = FinancialBudget::create([
                'institution_id' => $inst->id,
                'program_id' => $prog->id,
                'academic_year_id' => $cay->id,
                'financial_year_name' => '2024-25 (CFY)',
                'cfy_type' => 'CFY',
                'total_budget_allocated' => 8500000.00,
                'total_actual_expenditure' => 7950000.00,
                'utilization_percentage' => 93.53,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfyBudget->id,
                'category' => 'Recurring',
                'sub_category' => 'Faculty & Staff Salaries',
                'budget_allocated' => 5200000.00,
                'actual_expenditure' => 5180000.00,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfyBudget->id,
                'category' => 'Recurring',
                'sub_category' => 'Software Licenses, Cloud & Subscriptions',
                'budget_allocated' => 800000.00,
                'actual_expenditure' => 780000.00,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfyBudget->id,
                'category' => 'Recurring',
                'sub_category' => 'R&D, Conferences & Faculty Development',
                'budget_allocated' => 500000.00,
                'actual_expenditure' => 490000.00,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfyBudget->id,
                'category' => 'Non-Recurring',
                'sub_category' => 'AI & GPU Lab Infrastructure Upgrade',
                'budget_allocated' => 2000000.00,
                'actual_expenditure' => 1500000.00,
            ]);


            // ==================== 2023-24 (CFYm1) ====================
            $cfym1Budget = FinancialBudget::create([
                'institution_id' => $inst->id,
                'program_id' => $prog->id,
                'academic_year_id' => $caym1->id,
                'financial_year_name' => '2023-24 (CFYm1)',
                'cfy_type' => 'CFYm1',
                'total_budget_allocated' => 7800000.00,
                'total_actual_expenditure' => 7420000.00,
                'utilization_percentage' => 95.13,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfym1Budget->id,
                'category' => 'Recurring',
                'sub_category' => 'Faculty & Staff Salaries',
                'budget_allocated' => 4700000.00,
                'actual_expenditure' => 4650000.00,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfym1Budget->id,
                'category' => 'Recurring',
                'sub_category' => 'Software Licenses & Cloud Services',
                'budget_allocated' => 700000.00,
                'actual_expenditure' => 660000.00,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfym1Budget->id,
                'category' => 'Recurring',
                'sub_category' => 'R&D, Conferences & Faculty Travel',
                'budget_allocated' => 500000.00,
                'actual_expenditure' => 460000.00,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfym1Budget->id,
                'category' => 'Non-Recurring',
                'sub_category' => 'IoT & Embedded Systems Lab Setup',
                'budget_allocated' => 1900000.00,
                'actual_expenditure' => 1650000.00,
            ]);


            // ==================== 2022-23 (CFYm2) ====================
            $cfym2Budget = FinancialBudget::create([
                'institution_id' => $inst->id,
                'program_id' => $prog->id,
                'academic_year_id' => $caym2->id,
                'financial_year_name' => '2022-23 (CFYm2)',
                'cfy_type' => 'CFYm2',
                'total_budget_allocated' => 7100000.00,
                'total_actual_expenditure' => 6680000.00,
                'utilization_percentage' => 94.08,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfym2Budget->id,
                'category' => 'Recurring',
                'sub_category' => 'Faculty & Staff Salaries',
                'budget_allocated' => 4300000.00,
                'actual_expenditure' => 4220000.00,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfym2Budget->id,
                'category' => 'Recurring',
                'sub_category' => 'Software Licenses & IT Services',
                'budget_allocated' => 600000.00,
                'actual_expenditure' => 560000.00,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfym2Budget->id,
                'category' => 'Recurring',
                'sub_category' => 'Research, Training & Faculty Travel',
                'budget_allocated' => 400000.00,
                'actual_expenditure' => 350000.00,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfym2Budget->id,
                'category' => 'Non-Recurring',
                'sub_category' => 'Computer Laboratory Equipment',
                'budget_allocated' => 1400000.00,
                'actual_expenditure' => 1200000.00,
            ]);

            BudgetItem::create([
                'financial_budget_id' => $cfym2Budget->id,
                'category' => 'Non-Recurring',
                'sub_category' => 'Network & Infrastructure Upgrade',
                'budget_allocated' => 400000.00,
                'actual_expenditure' => 350000.00,
            ]);
        }

        // 11. Master SAR Report
        $sar = SarReport::firstOrCreate(
            ['program_id' => $prog->id, 'academic_year_id' => $cay->id],
            [
                'title' => 'Self-Assessment Report (SAR) for UG Engineering - B.Tech Electronics and Computer Engineering (Tier-II)',
                'current_state' => 'DEPARTMENT_CONSOLIDATION',
                'submitted_by' => 'Dr. S. Narayan (NBA Coordinator)',
                'hod_verified_by' => 'Dr. Arvind Mehra (HOD Electronics & Computer Engg)',
                'submitted_at' => now()->subDays(5),
                'is_locked' => false,
                'total_claimed_marks' => 842.50,
                'workflow_notes' => 'Faculty mark entries and articulation matrix consolidated for Electronics and Computer Engineering. Awaiting institutional review.',
            ]
        );

        $sar->update([
            'current_state' => 'DEPARTMENT_CONSOLIDATION',
            'total_claimed_marks' => 842.50,
        ]);

        // SAR Criteria 1 to 10 records
        $criteriaData = [
            ['num' => 1, 'title' => 'Vision, Mission and Program Educational Objectives', 'max' => 60, 'claimed' => 54.0],
            ['num' => 2, 'title' => 'Program Curriculum and Teaching-Learning Processes', 'max' => 120, 'claimed' => 108.0],
            ['num' => 3, 'title' => 'Course Outcomes and Program Outcomes', 'max' => 120, 'claimed' => 104.8],
            ['num' => 4, 'title' => "Students' Performance and Placement", 'max' => 150, 'claimed' => 130.0],
            ['num' => 5, 'title' => 'Faculty Information and Contributions', 'max' => 200, 'claimed' => 176.5],
            ['num' => 6, 'title' => 'Facilities and Technical Support', 'max' => 80, 'claimed' => 72.0],
            ['num' => 7, 'title' => 'Continuous Improvement', 'max' => 50, 'claimed' => 43.0],
            ['num' => 8, 'title' => 'First Year Academics', 'max' => 50, 'claimed' => 44.0],
            ['num' => 9, 'title' => 'Student Support Systems', 'max' => 50, 'claimed' => 45.0],
            ['num' => 10, 'title' => 'Governance, Institutional Support & Financial Resources', 'max' => 120, 'claimed' => 108.0],
        ];

        foreach ($criteriaData as $c) {
            $summaryObs = null;
            if ($c['num'] === 1) {
                $summaryObs = 'Vision: At TIET, our vision rests on two pillars: (1) A deeply meaningful experience for all stakeholders that shapes lives with wisdom, purpose, and compassion; and (2) Socially impactful research that advances knowledge while solving real challenges of industry, society, and humanity. Together, they embody our commitment to seeking excellence for all. | Mission: TIET exists to educate and empower students while advancing knowledge through research, so that we serve our society with wisdom, compassion, and a commitment to seek excellence.';
            }

            SarCriterion::updateOrCreate(
                ['sar_report_id' => $sar->id, 'criterion_number' => $c['num']],
                [
                    'title' => "Criterion {$c['num']}: {$c['title']}",
                    'max_marks' => $c['max'],
                    'claimed_marks' => $c['claimed'],
                    'status' => 'VERIFIED',
                    'is_verified' => true,
                    'summary_observations' => $summaryObs,
                ]
            );
        }

        // Audit Logs
        SarAuditLog::create([
            'sar_report_id' => $sar->id,
            'user_name' => 'Dr. S. Narayan',
            'user_role' => 'NBA Coordinator',
            'action' => 'STATE_TRANSITION',
            'domain_module' => 'SAR',
            'payload' => ['from' => 'DRAFT', 'to' => 'DEPARTMENT_CONSOLIDATION'],
            'description' => 'Submitted SAR report for Department Consolidation review.',
            'ip_address' => '192.168.1.45',
        ]);
    }
}
