<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Domains\SAR\Models\SarCriterion;
use App\Domains\SAR\Models\SarEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SarEvidenceApiController extends Controller
{
    public function index($reportId, $criterionNumber)
    {
        $criterion = SarCriterion::firstOrCreate(
            ['sar_report_id' => $reportId, 'criterion_number' => $criterionNumber],
            ['title' => 'Criterion ' . $criterionNumber, 'max_marks' => 100, 'status' => 'IN_PROGRESS']
        );

        $evidences = $criterion->evidences()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $evidences
        ]);
    }

    public function upload(Request $request, $reportId, $criterionNumber)
    {
        $request->validate([
            'evidence_file' => 'required|file|mimes:pdf|max:10240', // 10MB Max, PDF only
            'upload_reason' => 'nullable|string|max:500',
        ]);

        $criterion = SarCriterion::firstOrCreate(
            ['sar_report_id' => $reportId, 'criterion_number' => $criterionNumber],
            ['title' => 'Criterion ' . $criterionNumber, 'max_marks' => 100, 'status' => 'IN_PROGRESS']
        );

        if ($criterion->sarReport && $criterion->sarReport->is_locked) {
            return response()->json(['success' => false, 'message' => 'SAR report is locked. Evidence cannot be modified.'], 403);
        }

        if ($request->hasFile('evidence_file')) {
            $file = $request->file('evidence_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('sar_evidences', $fileName, 'public');

            // Version control logic
            $latestEvidence = $criterion->evidences()->orderBy('version', 'desc')->first();
            $nextVersion = $latestEvidence ? ($latestEvidence->version + 1) : 1;
            $previousFilePath = $latestEvidence ? $latestEvidence->file_path : null;

            $uploaderName = $request->user_name ?? (auth()->user()->name ?? 'Faculty');

            $evidence = $criterion->evidences()->create([
                'version' => $nextVersion,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'previous_file_path' => $previousFilePath,
                'upload_reason' => $request->upload_reason ?? ($nextVersion > 1 ? "Version {$nextVersion} update" : "Initial submission"),
                'uploaded_by' => $uploaderName,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Evidence Version {$nextVersion} uploaded successfully",
                'data' => $evidence
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'File not provided.'], 400);
    }

    public function history($reportId, $criterionNumber)
    {
        $criterion = SarCriterion::where('sar_report_id', $reportId)
            ->where('criterion_number', $criterionNumber)
            ->first();

        if (!$criterion) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $history = $criterion->evidences()
            ->orderBy('version', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function destroy($evidenceId)
    {
        $evidence = SarEvidence::with('criterion.sarReport')->findOrFail($evidenceId);
        
        if ($evidence->criterion && $evidence->criterion->sarReport && $evidence->criterion->sarReport->is_locked) {
            return response()->json(['success' => false, 'message' => 'SAR report is locked. Evidence cannot be modified.'], 403);
        }
        
        if (Storage::disk('public')->exists($evidence->file_path)) {
            Storage::disk('public')->delete($evidence->file_path);
        }

        $evidence->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evidence deleted successfully'
        ]);
    }

    public function download($evidenceId)
    {
        $evidence = SarEvidence::findOrFail($evidenceId);

        if (!Storage::disk('public')->exists($evidence->file_path)) {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }

        return Storage::disk('public')->download($evidence->file_path, $evidence->file_name);
    }
}
