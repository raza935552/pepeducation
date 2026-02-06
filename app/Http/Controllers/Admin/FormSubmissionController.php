<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Http\JsonResponse;

class FormSubmissionController extends Controller
{
    public function index(): JsonResponse
    {
        $submissions = FormSubmission::with('page:id,title')
            ->orderByDesc('created_at')
            ->paginate(50);

        return response()->json($submissions);
    }

    public function destroy(FormSubmission $submission): JsonResponse
    {
        $submission->delete();

        return response()->json(['success' => true]);
    }
}
