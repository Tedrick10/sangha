<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\ExamType;
use App\Models\MonasteryFormRequest;
use App\Notifications\Monastery\MonasteryFormRequestDecidedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MonasteryFormRequestController extends Controller
{
    public function index(Request $request): View
    {
        $requestStatus = $request->query('request_status');
        if ($requestStatus !== null && $requestStatus !== '' && ! in_array($requestStatus, ['all', 'pending', 'approved', 'rejected'], true)) {
            $requestStatus = null;
        }

        $formScope = 'general';

        $query = MonasteryFormRequest::query()
            ->with(['monastery', 'examType'])
            ->latest();

        $query->whereNull('exam_type_id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('monastery', function ($q) use ($search) {
                $q->where(function ($qry) use ($search) {
                    $qry->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('region', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            });
        }

        if ($requestStatus === 'pending') {
            $query->where('status', MonasteryFormRequest::STATUS_PENDING);
        } elseif ($requestStatus === 'approved') {
            $query->where('status', MonasteryFormRequest::STATUS_APPROVED);
        } elseif ($requestStatus === 'rejected') {
            $query->where('status', MonasteryFormRequest::STATUS_REJECTED);
        }

        $formRequests = $query->paginate(admin_per_page(15))->withQueryString();

        $examTypesCanonical = ExamType::query()
            ->whereIn('name', ExamType::CANONICAL_NAME_ORDER)
            ->orderByCanonical()
            ->get();

        return view('admin.monastery-form-requests.index', compact(
            'formRequests',
            'requestStatus',
            'formScope',
            'examTypesCanonical'
        ));
    }

    public function show(MonasteryFormRequest $monasteryFormRequest): View
    {
        $monasteryFormRequest->load(['monastery', 'examType', 'reviewer:id,name']);
        $entityType = $monasteryFormRequest->portalCustomFieldEntityType();
        $fields = CustomField::forEntity($entityType)->orderBy('sort_order')->orderBy('name')->get();
        if ($fields->isEmpty() && $monasteryFormRequest->isExamFormSubmission()) {
            $fields = CustomField::forEntity('request')->orderBy('sort_order')->orderBy('name')->get();
        }

        $sanghaFieldMeta = CustomField::sanghaDefinitionsBySlug();
        $sanghaExtraFieldDefinitions = CustomField::forEntity('sangha')
            ->where('is_built_in', false)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $examFieldMeta = CustomField::forEntity('exam')->get()->keyBy('slug');
        $examExtraFieldDefinitions = CustomField::forEntity('exam')
            ->where('is_built_in', false)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.monastery-form-requests.show', [
            'submission' => $monasteryFormRequest,
            'fields' => $fields,
            'sanghaFieldMeta' => $sanghaFieldMeta,
            'sanghaExtraFieldDefinitions' => $sanghaExtraFieldDefinitions,
            'examFieldMeta' => $examFieldMeta,
            'examExtraFieldDefinitions' => $examExtraFieldDefinitions,
        ]);
    }

    public function updateStatus(Request $request, MonasteryFormRequest $monasteryFormRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'rejection_reason' => ['nullable', 'string', 'max:2000', 'required_if:status,rejected'],
        ]);

        $beforeStatus = $monasteryFormRequest->status;

        $status = $validated['status'];
        $payload = [
            'status' => $status === 'pending'
                ? MonasteryFormRequest::STATUS_PENDING
                : ($status === 'approved'
                    ? MonasteryFormRequest::STATUS_APPROVED
                    : MonasteryFormRequest::STATUS_REJECTED),
        ];

        if ($status === 'pending') {
            $payload['rejection_reason'] = null;
            $payload['reviewed_by'] = null;
            $payload['reviewed_at'] = null;
        } elseif ($status === 'approved') {
            $payload['rejection_reason'] = null;
            $payload['reviewed_by'] = Auth::id();
            $payload['reviewed_at'] = now();
        } else {
            $payload['rejection_reason'] = trim((string) ($validated['rejection_reason'] ?? ''));
            $payload['reviewed_by'] = Auth::id();
            $payload['reviewed_at'] = now();
        }

        if ($status === 'approved' && ! $monasteryFormRequest->isExamFormSubmission()) {
            $transferError = $monasteryFormRequest->validateTransferDataForApproval();
            if ($transferError !== null) {
                return redirect()
                    ->back()
                    ->with('error', $transferError);
            }
        }

        $becameApproved = $status === 'approved'
            && $beforeStatus !== MonasteryFormRequest::STATUS_APPROVED;

        try {
            DB::transaction(function () use ($monasteryFormRequest, $payload, $becameApproved): void {
                $monasteryFormRequest->update($payload);
                $monasteryFormRequest->refresh();
                if ($becameApproved && ! $monasteryFormRequest->isExamFormSubmission()) {
                    $monasteryFormRequest->applyApprovedTransferRelocation();
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->with('error', t('transfer_approve_apply_failed', 'Could not complete the transfer. Please try again or contact support.'));
        }

        $monasteryFormRequest->refresh();

        $afterStatus = $monasteryFormRequest->status;
        if ($beforeStatus !== $afterStatus && in_array($afterStatus, [MonasteryFormRequest::STATUS_APPROVED, MonasteryFormRequest::STATUS_REJECTED], true)) {
            $monasteryFormRequest->load('monastery');
            if ($monasteryFormRequest->monastery) {
                $preview = $afterStatus === MonasteryFormRequest::STATUS_REJECTED && filled($monasteryFormRequest->rejection_reason)
                    ? Str::limit($monasteryFormRequest->rejection_reason, 160)
                    : null;
                $monasteryFormRequest->monastery->notify(new MonasteryFormRequestDecidedNotification(
                    $afterStatus,
                    $preview,
                    route('monastery.requests.show', $monasteryFormRequest),
                ));
            }
        }

        return redirect()
            ->back()
            ->with('success', t('request_status_updated', 'Request status updated.'));
    }

    public function destroy(MonasteryFormRequest $monasteryFormRequest): RedirectResponse
    {
        CustomFieldValue::query()
            ->where('entity_id', $monasteryFormRequest->id)
            ->whereIn('entity_type', ['request', 'monastery_exam'])
            ->delete();

        $dir = 'monastery-form-requests/'.$monasteryFormRequest->id;
        if (Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->deleteDirectory($dir);
        }

        $monasteryFormRequest->delete();

        $query = array_filter(
            request()->only(['search', 'request_status', 'form_scope', 'exam_type_id']),
            fn ($v) => $v !== null && $v !== '' && $v !== 'all'
        );

        return redirect()
            ->route('admin.monastery-requests.index', $query)
            ->with('success', t('request_deleted', 'Request deleted.'));
    }

    /**
     * Serve uploaded files for this submission (admin only).
     */
    public function file(MonasteryFormRequest $monasteryFormRequest, Request $request): mixed
    {
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
}
