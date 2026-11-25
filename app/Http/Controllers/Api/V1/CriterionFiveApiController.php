<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Domains\Academic\Models\Program;
use App\Domains\Academic\Models\AcademicYear;
use App\Domains\Faculty\Models\FacultyProfile;
use App\Domains\Faculty\Models\FacultyAcademicYear;
use App\Domains\Faculty\Models\VisitingFaculty;
use App\Domains\Faculty\Services\CriterionFiveService;

class CriterionFiveApiController extends Controller
{
    protected CriterionFiveService $criterionService;

    public function __construct(CriterionFiveService $criterionService)
    {
        $this->criterionService = $criterionService;
    }

    /**
     * Complete Criterion 5 Data & Calculations (5.1 - 5.5)
     */
    public function index(Request $request): JsonResponse
    {
        $programId = $request->query('program_id');
        $program = $programId ? Program::find($programId) : Program::first();

        if (!$program) {
            return response()->json(['success' => false, 'message' => 'Program not found'], 404);
        }

        $data = $this->criterionService->calculateFullCriterion($program);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Table 5A: Full Faculty Directory
     */
    public function facultyList(Request $request): JsonResponse
    {
        $query = FacultyProfile::with('department');

        if ($request->filled('designation')) {
            $desig = $request->query('designation');
            $query->whereHas('facultyAcademicYears', function ($q) use ($desig) {
                $q->where('designation', 'like', "%{$desig}%");
            });
        }

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('faculty_id', 'like', "%{$s}%")
                  ->orWhere('highest_degree', 'like', "%{$s}%")
                  ->orWhere('degree_specialization', 'like', "%{$s}%");
            });
        }

        if ($request->filled('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $list = $query->orderBy('joining_date', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $list,
            'count' => $list->count(),
        ]);
    }

    /**
     * Add new faculty member
     */
    public function storeFaculty(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'highest_degree' => 'required|string|max:50',
            'designation' => 'required|string|max:100',
            'cadre' => 'required|string|in:Professor,Associate Professor,Assistant Professor',
            'joining_date' => 'required|date',
            'degree_specialization' => 'nullable|string|max:200',
            'university_awarded' => 'nullable|string|max:200',
            'association_type' => 'required|string|in:Regular,Contractual Fulltime,Adjunct',
            'program_id' => 'nullable|exists:programs,id',
            'is_allied_department' => 'boolean',
            'first_year_teaching_load_percentage' => 'numeric|min:0|max:100',
        ]);

        $program = isset($validated['program_id']) ? Program::find($validated['program_id']) : Program::first();
        $deptId = $program ? $program->department_id : 1;

        $profile = FacultyProfile::create([
            'department_id' => $deptId,
            'faculty_id' => 'FAC-' . strtoupper(substr(uniqid(), -6)),
            'name' => $validated['name'],
            'highest_degree' => $validated['highest_degree'],
            'degree_specialization' => $validated['degree_specialization'] ?? null,
            'university_awarded' => $validated['university_awarded'] ?? null,
            'joining_date' => $validated['joining_date'],
            'teaching_experience_years' => max(0, Carbon::parse($validated['joining_date'])->diffInYears(now())),
            'is_active' => true,
        ]);

        // Link to active academic years
        $years = AcademicYear::whereIn('type', ['CAY', 'CAYm1', 'CAYm2'])->get();
        foreach ($years as $y) {
            FacultyAcademicYear::create([
                'faculty_profile_id' => $profile->id,
                'academic_year_id' => $y->id,
                'program_id' => $program->id ?? null,
                'designation' => $validated['designation'],
                'cadre' => $validated['cadre'],
                'association_type' => $validated['association_type'],
                'is_allied_department' => $validated['is_allied_department'] ?? false,
                'first_year_teaching_load_percentage' => $validated['first_year_teaching_load_percentage'] ?? 0.0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Faculty member added successfully.',
            'data' => $profile,
        ], 201);
    }

    /**
     * Update faculty member
     */
    public function updateFaculty(Request $request, int $id): JsonResponse
    {
        $profile = FacultyProfile::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'highest_degree' => 'required|string|max:50',
            'degree_specialization' => 'nullable|string|max:200',
            'university_awarded' => 'nullable|string|max:200',
            'joining_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $profile->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Faculty profile updated successfully.',
            'data' => $profile,
        ]);
    }

    /**
     * Delete faculty profile
     */
    public function destroyFaculty(int $id): JsonResponse
    {
        $profile = FacultyProfile::findOrFail($id);
        $profile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Faculty record deleted successfully.',
        ]);
    }

    /**
     * Visiting Faculty CRUD (Sub-criterion 5.4)
     */
    public function storeVisitingFaculty(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'designation' => 'required|string|max:100',
            'organization' => 'required|string|max:200',
            'course_name' => 'required|string|max:200',
            'course_code' => 'nullable|string|max:50',
            'contribution_type' => 'required|string|max:100',
            'interaction_hours' => 'required|numeric|min:1',
            'academic_year_id' => 'required|exists:academic_years,id',
            'program_id' => 'nullable|exists:programs,id',
        ]);

        $programId = $validated['program_id'] ?? (Program::first()->id ?? 1);

        $visiting = VisitingFaculty::create([
            'program_id' => $programId,
            'academic_year_id' => $validated['academic_year_id'],
            'name' => $validated['name'],
            'designation' => $validated['designation'],
            'organization' => $validated['organization'],
            'course_name' => $validated['course_name'],
            'course_code' => $validated['course_code'] ?? null,
            'contribution_type' => $validated['contribution_type'],
            'interaction_hours' => $validated['interaction_hours'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visiting faculty record saved successfully.',
            'data' => $visiting,
        ], 201);
    }

    public function updateVisitingFaculty(Request $request, int $id): JsonResponse
    {
        $visiting = VisitingFaculty::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'designation' => 'required|string|max:100',
            'organization' => 'required|string|max:200',
            'course_name' => 'required|string|max:200',
            'course_code' => 'nullable|string|max:50',
            'contribution_type' => 'required|string|max:100',
            'interaction_hours' => 'required|numeric|min:1',
        ]);

        $visiting->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Visiting faculty record updated.',
            'data' => $visiting,
        ]);
    }

    public function destroyVisitingFaculty(int $id): JsonResponse
    {
        $visiting = VisitingFaculty::findOrFail($id);
        $visiting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Visiting faculty record deleted.',
        ]);
    }
}
