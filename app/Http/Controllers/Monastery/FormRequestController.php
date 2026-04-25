<?php

namespace App\Http\Controllers\Monastery;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\MonasteryFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FormRequestController extends Controller
{
    public function show(MonasteryFormRequest $monasteryFormRequest): View
    {
        $this->authorizeMonastery($monasteryFormRequest);
        $monasteryFormRequest->load('examType');

        CustomField::syncBuiltInFieldDefinitions();

        $entityType = $monasteryFormRequest->portalCustomFieldEntityType();
        $fields = CustomField::forEntity($entityType)->get();
        if ($fields->isEmpty() && $monasteryFormRequest->isExamFormSubmission()) {
            $fields = CustomField::forEntity('request')->get();
        }

        return view('monastery.form-request-show', [
            'submission' => $monasteryFormRequest,
            'fields' => $fields,
        ]);
    }

    /**
     * Download an uploaded file for this submission (monastery owner only).
     */
    public function file(MonasteryFormRequest $monasteryFormRequest, Request $request)
    {
        $this->authorizeMonastery($monasteryFormRequest);

        $path = $request->query('path');
        if (! is_string($path) || $path === '' || str_contains($path, '..')) {
            abort(404);
        }

        $prefix = 'monastery-form-requests/'.$monasteryFormRequest->id.'/';
        if (! str_starts_with($path, $prefix)) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }

    private function authorizeMonastery(MonasteryFormRequest $monasteryFormRequest): void
    {
        $monastery = Auth::guard('monastery')->user();
        abort_if((int) $monasteryFormRequest->monastery_id !== (int) $monastery->id, 403);
    }
}
