<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormSubmitController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'form_name' => 'required|string|max:100',
            'page_slug' => 'nullable|string|max:255',
            'fields' => 'required|array|max:30',
            'fields.*' => 'nullable|string|max:5000',
        ]);

        $pageId = null;
        if ($request->page_slug) {
            $pageId = Page::where('slug', $request->page_slug)->value('id');
        }

        FormSubmission::create([
            'page_id' => $pageId,
            'form_name' => $request->form_name,
            'data' => $request->fields,
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Form submitted successfully!']);
    }
}
