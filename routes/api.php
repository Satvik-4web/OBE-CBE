<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\NotificationApiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\StaffManagementApiController;
use App\Http\Controllers\Api\V1\SettingsApiController;
use App\Http\Controllers\Api\V1\CourseManagementApiController;
use App\Http\Controllers\Api\V1\AssessmentEntryApiController;
use App\Http\Controllers\Api\V1\NbaDashboardApiController;
use App\Http\Controllers\Api\V1\SfrCalculatorApiController;
use App\Http\Controllers\Api\V1\FqiCalculatorApiController;
use App\Http\Controllers\Api\V1\ArticulationMatrixApiController;
use App\Http\Controllers\Api\V1\AttainmentApiController;
use App\Http\Controllers\Api\V1\SarWorkflowApiController;
use App\Http\Controllers\Api\V1\SarReportExportApiController;
use App\Http\Controllers\Api\V1\StudentMarksCsvController;
use App\Http\Controllers\Api\V1\AssessmentBlueprintController;

Route::prefix('v1')->group(function () {

    // 1. Authentication & Session
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // 2. Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationApiController::class, 'index']);
        Route::patch('/{notification}/read', [NotificationApiController::class, 'markAsRead']);
        Route::delete('/{notification}', [NotificationApiController::class, 'destroy']);
    });

    // 3. Staff & Faculty Management (Super Admin & HOD)
    Route::prefix('staff')->middleware('role:Super Admin,HOD')->group(function () {
        Route::get('/', [StaffManagementApiController::class, 'index']);
        Route::post('/', [StaffManagementApiController::class, 'store']);
        Route::put('/{id}', [StaffManagementApiController::class, 'update']);
        Route::post('/{id}/toggle-status', [StaffManagementApiController::class, 'toggleStatus']);
        Route::delete('/{id}', [StaffManagementApiController::class, 'destroy']);

        Route::post(
            '/assessments/{assessmentBatch}/marks/upload',
            [StudentMarksCsvController::class, 'upload']
        );

        Route::post(
            '/assessments/blueprint',
            [AssessmentBlueprintController::class, 'store']
        );

        Route::get(
            '/assessments/{assessmentBatch}/blueprint',
            [AssessmentBlueprintController::class, 'show']
        );
    });

    // 4. System Settings & Branding (Super Admin)
    Route::prefix('settings')->middleware('role:Super Admin')->group(function () {
        Route::get('/', [SettingsApiController::class, 'index']);
        Route::post('/', [SettingsApiController::class, 'update']);
        Route::post('/upload-image', [SettingsApiController::class, 'uploadImage']);
        Route::post('/reset-academic-data', [SettingsApiController::class, 'resetAcademicData']);
        Route::post('/restore-sample-data', [SettingsApiController::class, 'restoreSampleData']);
    });

    // 5. Curriculum & Course Management CRUD
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseManagementApiController::class, 'index']);
        Route::post('/', [CourseManagementApiController::class, 'store']);
        Route::post('/{id}/co', [CourseManagementApiController::class, 'addCourseOutcome']);
        Route::delete('/{id}', [CourseManagementApiController::class, 'destroy']);
    });

    // 6. Direct & Indirect Attainment Mark Logging
    Route::prefix('attainment')->group(function () {
        Route::post('/record-marks', [AssessmentEntryApiController::class, 'recordMarks']);
        Route::post('/record-survey', [AssessmentEntryApiController::class, 'recordSurvey']);
        Route::get('/surveys', [AssessmentEntryApiController::class, 'surveys']);
        Route::get('/survey-options', [AssessmentEntryApiController::class, 'surveyOptions']);
        Route::put('/surveys/{id}', [AssessmentEntryApiController::class, 'updateSurvey']);
        Route::delete('/surveys/{id}', [AssessmentEntryApiController::class, 'destroySurvey']);
        Route::get('/co-trends', [AttainmentApiController::class, 'getCoTrends']);
        Route::get('/cie-heatmap', [AttainmentApiController::class, 'getCieHeatmap']);
        Route::get('/alerts', [AttainmentApiController::class, 'getLowAttainmentAlerts']);
    });

    // 7. NBA SAR Engines & Matrices
    Route::prefix('nba')->group(function () {

        // Executive Dashboard & Overview
        Route::get('/dashboard', [NbaDashboardApiController::class, 'index']);

        // Criterion 5: Faculty Engines (SFR, FQI, Cadre)
        Route::get('/sfr-metrics', [SfrCalculatorApiController::class, 'getMetrics']);
        Route::post('/sfr-simulate', [SfrCalculatorApiController::class, 'simulate']);

        Route::get('/fqi-metrics', [FqiCalculatorApiController::class, 'getMetrics']);
        Route::post('/fqi-simulate', [FqiCalculatorApiController::class, 'simulate']);

        // Criterion 2 & 3: Curriculum & Articulation Matrix
        Route::get(
            '/course-matrix/{courseId}',
            [ArticulationMatrixApiController::class, 'getCourseMatrix']
        );

        // CLO Evaluation & Ongoing Performance Workflow (TIET/NBA Flow)
        Route::post('/clo-workflow/parse-performance', [\App\Http\Controllers\Api\V1\CloWorkflowApiController::class, 'parsePerformance']);
        Route::post('/clo-workflow/calculate', [\App\Http\Controllers\Api\V1\CloWorkflowApiController::class, 'calculate']);
        Route::get('/clo-workflow/export-excel', [\App\Http\Controllers\Api\V1\CloWorkflowApiController::class, 'exportExcel']);

        Route::get(
            '/program-matrix',
            [ArticulationMatrixApiController::class, 'getProgramMatrix']
        );

        Route::post(
            '/update-mapping',
            [ArticulationMatrixApiController::class, 'updateMapping']
        );

        // Criterion 3: Attainment (Direct, Indirect, Final PO Attainment)
        Route::get(
            '/attainment/final',
            [AttainmentApiController::class, 'getFinalAttainment']
        );

        Route::get(
            '/attainment/course/{courseId}',
            [AttainmentApiController::class, 'getCourseDirectAttainment']
        );

        Route::get(
            '/attainment/course/{courseId}/performance',
            [AttainmentApiController::class, 'getCoursePerformance']
        );

        Route::get('/attainment/co-trends', [AttainmentApiController::class, 'getCoTrends']);
        Route::get('/attainment/cie-heatmap', [AttainmentApiController::class, 'getCieHeatmap']);
        Route::get('/attainment/alerts', [AttainmentApiController::class, 'getLowAttainmentAlerts']);

        Route::get(
            '/attainment/indirect',
            [AttainmentApiController::class, 'getIndirectAttainment']
        );

        Route::post(
            '/attainment/record-marks',
            [AssessmentEntryApiController::class, 'recordMarks']
        );

        Route::post(
            '/attainment/record-survey',
            [AssessmentEntryApiController::class, 'recordSurvey']
        );

        // SAR Workflow State Engine & Audit Logs
        Route::get(
            '/workflow/{reportId}',
            [SarWorkflowApiController::class, 'getStatus']
        );

        Route::post(
            '/workflow/{reportId}/transition',
            [SarWorkflowApiController::class, 'transition']
        );

        // SAR Evidence
        Route::get(
            '/workflow/{reportId}/criteria/{criterionId}/evidence',
            [\App\Http\Controllers\Api\V1\SarEvidenceApiController::class, 'index']
        );

        Route::get(
            '/workflow/{reportId}/criteria/{criterionId}/evidence/history',
            [\App\Http\Controllers\Api\V1\SarEvidenceApiController::class, 'history']
        );

        Route::post(
            '/workflow/{reportId}/criteria/{criterionId}/evidence',
            [\App\Http\Controllers\Api\V1\SarEvidenceApiController::class, 'upload']
        );

        Route::delete(
            '/evidence/{evidenceId}',
            [\App\Http\Controllers\Api\V1\SarEvidenceApiController::class, 'destroy']
        );

        Route::get(
            '/evidence/{evidenceId}/download',
            [\App\Http\Controllers\Api\V1\SarEvidenceApiController::class, 'download']
        );

        // Export Full SAR Report
        Route::get(
            '/export/{reportId}',
            [SarReportExportApiController::class, 'getFullDocument']
        );

        // Criterion 5 - Dedicated Endpoint under NBA
        Route::get('/criterion-5', [\App\Http\Controllers\Api\V1\CriterionFiveApiController::class, 'index']);
    });

    // 8. NBA Criterion 5 – Faculty Information & Contributions Management
    Route::prefix('sar/criterion-5')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\V1\CriterionFiveApiController::class, 'index']);
        Route::get('/faculty', [\App\Http\Controllers\Api\V1\CriterionFiveApiController::class, 'facultyList']);
        Route::post('/faculty', [\App\Http\Controllers\Api\V1\CriterionFiveApiController::class, 'storeFaculty']);
        Route::put('/faculty/{id}', [\App\Http\Controllers\Api\V1\CriterionFiveApiController::class, 'updateFaculty']);
        Route::delete('/faculty/{id}', [\App\Http\Controllers\Api\V1\CriterionFiveApiController::class, 'destroyFaculty']);

        Route::post('/visiting-faculty', [\App\Http\Controllers\Api\V1\CriterionFiveApiController::class, 'storeVisitingFaculty']);
        Route::put('/visiting-faculty/{id}', [\App\Http\Controllers\Api\V1\CriterionFiveApiController::class, 'updateVisitingFaculty']);
        Route::delete('/visiting-faculty/{id}', [\App\Http\Controllers\Api\V1\CriterionFiveApiController::class, 'destroyVisitingFaculty']);
    });
});