@extends('admin.layout')

@section('title', t('request_details', 'Request details'))

@php
    $listQuery = request()->only(['search', 'request_status', 'form_scope', 'exam_type_id']);
    $listQuery = array_filter($listQuery, fn ($v) => $v !== null && $v !== '' && $v !== 'all');
    $isExamFormRequest = $submission->isExamFormSubmission();
    if ($isExamFormRequest) {
        if (! array_key_exists('form_scope', $listQuery)) {
            $listQuery['form_scope'] = 'exam';
        }
        if ($submission->exam_type_id && ! array_key_exists('exam_type_id', $listQuery)) {
            $listQuery['exam_type_id'] = $submission->exam_type_id;
        }
    } elseif (! array_key_exists('form_scope', $listQuery)) {
        $listQuery['form_scope'] = 'general';
    }
    $indexUrl = route('admin.monastery-requests.index');
    if (count($listQuery)) {
        $indexUrl .= '?'.http_build_query($listQuery);
    }
    $backToListLabel = $isExamFormRequest
        ? t('admin_exam_form_submissions', 'Exam Form')
        : t('monastery_requests', 'Transfer Sangha');
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    <div class="admin-form-page-header !mb-0">
        <a href="{{ $indexUrl }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) {{ $backToListLabel }}</a>
        <h1 class="admin-page-title">{{ t('request_details', 'Request details') }}</h1>
    </div>

    {{-- Overview --}}
    <section class="rounded-2xl border border-slate-200/80 dark:border-slate-700/70 bg-white dark:bg-slate-900/50 shadow-sm dark:shadow-none overflow-hidden ring-1 ring-slate-200/50 dark:ring-slate-600/30">
        <div class="px-5 py-5 sm:px-7 sm:py-6 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0 space-y-2">
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 leading-tight">{{ $submission->monastery?->name ?? '—' }}</h2>
                @if($submission->monastery?->username)
                    <p class="font-mono text-sm text-slate-500 dark:text-slate-400">{{ $submission->monastery->username }}</p>
                @endif
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    {{ t('submitted_at', 'Submitted') }}
                    <time class="font-medium text-slate-800 dark:text-slate-200" datetime="{{ $submission->created_at->toIso8601String() }}">{{ $submission->created_at->format('M d, Y · H:i') }}</time>
                </p>
                @if($submission->examType)
                    <p class="text-sm font-medium text-amber-900 dark:text-amber-200">{{ $submission->examType->name }}</p>
                @endif
            </div>
            <div class="flex flex-col sm:items-end gap-3 shrink-0">
                @if($submission->status === \App\Models\MonasteryFormRequest::STATUS_APPROVED)
                    <span class="admin-badge-yes">{{ t('status_approved', 'Approved') }}</span>
                @elseif($submission->status === \App\Models\MonasteryFormRequest::STATUS_REJECTED)
                    <span class="admin-badge-rejected">{{ t('status_rejected', 'Rejected') }}</span>
                @else
                    <span class="inline-flex rounded-full bg-amber-100 dark:bg-amber-900/35 px-3 py-1 text-xs font-semibold text-amber-900 dark:text-amber-200">{{ t('status_pending', 'Pending') }}</span>
                @endif
                @if($submission->reviewed_at)
                    <p class="text-xs text-slate-500 dark:text-slate-400 sm:text-right">
                        <span class="block uppercase tracking-wide font-semibold text-slate-400 dark:text-slate-500">{{ t('reviewed_at', 'Reviewed') }}</span>
                        <span class="block mt-0.5 text-slate-600 dark:text-slate-300">{{ $submission->reviewed_at->format('M d, Y H:i') }}</span>
                        @if($submission->reviewer)
                            <span class="block mt-0.5">{{ $submission->reviewer->name }}</span>
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </section>

    {{-- Submitted information (first) --}}
    <section class="rounded-2xl border border-slate-200/80 dark:border-slate-700/70 bg-white dark:bg-slate-900/50 shadow-sm dark:shadow-none overflow-hidden ring-1 ring-slate-200/50 dark:ring-slate-600/30">
        <header class="px-5 py-4 sm:px-7 sm:py-5 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/40 dark:bg-slate-800/20">
            <h2 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ t('request_form_fields', 'Submitted information') }}</h2>
            <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ t('request_form_fields_subtitle', 'Values provided when this request was submitted.') }}</p>
        </header>
        <div class="p-5 sm:p-7">
            <dl class="space-y-3">
                @forelse($fields as $field)
                    @php
                        $raw = $submission->getRequestFieldValue($field);
                    @endphp
                    <div class="rounded-xl px-4 py-3.5 sm:px-5 sm:py-4 bg-slate-50/60 dark:bg-slate-800/25 border border-slate-100/90 dark:border-slate-700/50">
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $field->name }}</dt>
                        <dd class="mt-1.5 text-slate-900 dark:text-slate-100 break-words text-sm leading-relaxed">
                            @if(! filled($raw))
                                <span class="text-slate-400 dark:text-slate-500">—</span>
                            @elseif($field->type === 'checkbox')
                                {{ in_array((string) $raw, ['1', 'true'], true) ? t('yes', 'Yes') : t('no', 'No') }}
                            @elseif($field->type === 'media' || $field->type === 'document' || $field->type === 'video')
                                @php
                                    $fileUrl = route('admin.monastery-requests.file', ['monasteryFormRequest' => $submission, 'path' => $raw]);
                                @endphp
                                <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-lg bg-amber-500/12 dark:bg-amber-500/15 text-amber-900 dark:text-amber-200 px-3 py-2 text-sm font-medium ring-1 ring-amber-500/20 hover:bg-amber-500/18 transition-colors">
                                    @include('partials.icon', ['name' => 'external-link', 'class' => 'w-4 h-4 shrink-0 opacity-90'])
                                    {{ basename($raw) }}
                                </a>
                            @elseif($field->type === 'approved_sangha')
                                @php
                                    $detailSangha = $submission->approvedSanghaForFieldValue($raw);
                                    $approvedSanghaRowId = 'as-'.$field->id;
                                @endphp
                                <div class="w-full space-y-3">
                                    <div class="flex min-w-0 flex-wrap items-center justify-between gap-3">
                                        <p class="min-w-0 flex-1 whitespace-pre-wrap text-left font-medium text-slate-900 dark:text-slate-100">{{ $submission->displaySubmittedValue($field, $raw) }}</p>
                                        @if($detailSangha)
                                            <button
                                                type="button"
                                                class="mr-inline-details-btn inline-flex shrink-0 items-center justify-center rounded-lg border border-amber-600/45 bg-amber-500/15 px-3 py-1.5 text-xs font-semibold text-amber-950 shadow-sm ring-1 ring-amber-500/25 transition-colors hover:bg-amber-500/25 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/50 dark:border-amber-500/35 dark:bg-amber-950/45 dark:text-amber-100 dark:ring-amber-400/20 dark:hover:bg-amber-900/55"
                                                data-panel-id="mr-sangha-panel-{{ $approvedSanghaRowId }}"
                                                aria-expanded="false"
                                                aria-controls="mr-sangha-panel-{{ $approvedSanghaRowId }}"
                                            >
                                                {{ t('details', 'Details') }}
                                            </button>
                                        @endif
                                    </div>
                                    @if($detailSangha)
                                        <div
                                            id="mr-sangha-panel-{{ $approvedSanghaRowId }}"
                                            class="mr-approved-sangha-panel hidden w-full rounded-xl border border-slate-200/80 bg-white/70 px-3 py-4 dark:border-slate-600/50 dark:bg-slate-900/50"
                                            role="region"
                                            aria-hidden="true"
                                        >
                                            @include('admin.partials.sangha-registration-snapshot', [
                                                'sangha' => $detailSangha,
                                                'sanghaFieldMeta' => $sanghaFieldMeta,
                                                'sanghaExtraFieldDefinitions' => $sanghaExtraFieldDefinitions,
                                            ])
                                        </div>
                                    @endif
                                </div>
                            @elseif($field->entity_type === 'monastery_exam' && $field->slug === 'exam_year' && filled($raw))
                                @php
                                    $yearInt = (int) trim((string) $raw);
                                    $yearOk = $yearInt >= 1900 && $yearInt <= 2100;
                                    $examsForYear = $yearOk ? \App\Models\Exam::activeExamsForCalendarYear($yearInt) : collect();
                                    $yearRowId = 'yr-'.$field->id;
                                    $yearPanelId = 'mr-year-panel-'.$yearRowId;
                                @endphp
                                <div class="w-full space-y-3">
                                    <div class="flex min-w-0 flex-wrap items-center justify-between gap-3">
                                        <p class="min-w-0 flex-1 whitespace-pre-wrap text-left font-medium text-slate-900 dark:text-slate-100">{{ $raw }}</p>
                                        @if($yearOk)
                                            <button
                                                type="button"
                                                class="mr-inline-details-btn inline-flex shrink-0 items-center justify-center rounded-lg border border-amber-600/45 bg-amber-500/15 px-3 py-1.5 text-xs font-semibold text-amber-950 shadow-sm ring-1 ring-amber-500/25 transition-colors hover:bg-amber-500/25 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/50 dark:border-amber-500/35 dark:bg-amber-950/45 dark:text-amber-100 dark:ring-amber-400/20 dark:hover:bg-amber-900/55"
                                                data-panel-id="{{ $yearPanelId }}"
                                                aria-expanded="false"
                                                aria-controls="{{ $yearPanelId }}"
                                            >
                                                {{ t('details', 'Details') }}
                                            </button>
                                        @endif
                                    </div>
                                    @if($yearOk)
                                        <div
                                            id="{{ $yearPanelId }}"
                                            class="mr-year-details-panel hidden w-full rounded-xl border border-slate-200/80 bg-white/70 px-3 py-4 dark:border-slate-600/50 dark:bg-slate-900/50"
                                            role="region"
                                            aria-hidden="true"
                                        >
                                            @include('admin.partials.exam-year-details-panel', ['examsForYear' => $examsForYear, 'yearInt' => $yearInt])
                                        </div>
                                    @endif
                                </div>
                            @elseif($field->entity_type === 'monastery_exam' && $field->slug === 'exam_session' && filled($raw))
                                @php
                                    $detailExam = $submission->examForSessionFieldValue($raw);
                                    $examRowId = 'ex-'.$field->id;
                                    $examPanelId = 'mr-exam-panel-'.$examRowId;
                                @endphp
                                <div class="w-full space-y-3">
                                    <div class="flex min-w-0 flex-wrap items-center justify-between gap-3">
                                        <p class="min-w-0 flex-1 whitespace-pre-wrap text-left font-medium text-slate-900 dark:text-slate-100">{{ $submission->displaySubmittedValue($field, $raw) }}</p>
                                        @if($detailExam)
                                            <button
                                                type="button"
                                                class="mr-inline-details-btn inline-flex shrink-0 items-center justify-center rounded-lg border border-amber-600/45 bg-amber-500/15 px-3 py-1.5 text-xs font-semibold text-amber-950 shadow-sm ring-1 ring-amber-500/25 transition-colors hover:bg-amber-500/25 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/50 dark:border-amber-500/35 dark:bg-amber-950/45 dark:text-amber-100 dark:ring-amber-400/20 dark:hover:bg-amber-900/55"
                                                data-panel-id="{{ $examPanelId }}"
                                                aria-expanded="false"
                                                aria-controls="{{ $examPanelId }}"
                                            >
                                                {{ t('details', 'Details') }}
                                            </button>
                                        @endif
                                    </div>
                                    @if($detailExam)
                                        <div
                                            id="{{ $examPanelId }}"
                                            class="mr-exam-details-panel hidden w-full rounded-xl border border-slate-200/80 bg-white/70 px-3 py-4 dark:border-slate-600/50 dark:bg-slate-900/50"
                                            role="region"
                                            aria-hidden="true"
                                        >
                                            @include('admin.partials.exam-session-details-panel', [
                                                'exam' => $detailExam,
                                                'examFieldMeta' => $examFieldMeta,
                                                'examExtraFieldDefinitions' => $examExtraFieldDefinitions,
                                            ])
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="whitespace-pre-wrap">{{ $raw }}</span>
                            @endif
                        </dd>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400 py-2">{{ t('no_custom_fields', 'No custom fields defined for this form.') }}</p>
                @endforelse
            </dl>
        </div>
    </section>

    {{-- Update status (below submitted information) --}}
    <section class="rounded-2xl border border-slate-200/80 dark:border-slate-700/70 bg-white dark:bg-slate-900/50 shadow-sm dark:shadow-none overflow-hidden ring-1 ring-slate-200/50 dark:ring-slate-600/30">
        <header class="px-5 py-4 sm:px-7 sm:py-4 border-b border-slate-100 dark:border-slate-700/60">
            <h2 class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ t('update_request_status', 'Update status') }}</h2>
        </header>
        <div class="p-5 sm:p-7">
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-lg leading-relaxed">{{ t('request_status_form_hint', 'Change the request status below. Rejected requests require a short note for the monastery.') }}</p>
            <form action="{{ route('admin.monastery-requests.update-status', $submission) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="admin-form-group max-w-md">
                    <label for="mr_status" class="admin-form-label">{{ t('status', 'Status') }}</label>
                    <select name="status" id="mr_status" class="admin-select-input w-full">
                        <option value="pending" @selected($submission->status === \App\Models\MonasteryFormRequest::STATUS_PENDING)>{{ t('status_pending', 'Pending') }}</option>
                        <option value="approved" @selected($submission->status === \App\Models\MonasteryFormRequest::STATUS_APPROVED)>{{ t('status_approved', 'Approved') }}</option>
                        <option value="rejected" @selected($submission->status === \App\Models\MonasteryFormRequest::STATUS_REJECTED)>{{ t('status_rejected', 'Rejected') }}</option>
                    </select>
                    @error('status')<p class="admin-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="admin-form-group max-w-xl {{ $submission->status !== \App\Models\MonasteryFormRequest::STATUS_REJECTED ? 'hidden' : '' }}" id="mr-rejection-wrap">
                    <label for="mr_rejection_reason" class="admin-form-label">{{ t('rejection_note', 'Rejection note') }} <span class="text-red-600 dark:text-red-400" id="mr-rejection-required" hidden>*</span></label>
                    <textarea name="rejection_reason" id="mr_rejection_reason" rows="4" class="admin-textarea" placeholder="{{ t('explain_rejection', 'Explain why this request is rejected…') }}">{{ old('rejection_reason', $submission->rejection_reason) }}</textarea>
                    @error('rejection_reason')<p class="admin-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="admin-form-actions !mt-2 pt-2">
                    <button type="submit" class="admin-btn-primary inline-flex items-center gap-2">@include('partials.icon', ['name' => 'check', 'class' => 'w-5 h-5']) {{ t('update_request', 'Update request') }}</button>
                    <a href="{{ $indexUrl }}" class="admin-btn-secondary inline-flex items-center gap-2">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) {{ t('cancel', 'Cancel') }}</a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var statusEl = document.getElementById('mr_status');
    var wrap = document.getElementById('mr-rejection-wrap');
    var reasonEl = document.getElementById('mr_rejection_reason');
    var reqMark = document.getElementById('mr-rejection-required');
    if (!statusEl || !wrap || !reasonEl) return;
    function refresh() {
        var rejected = statusEl.value === 'rejected';
        wrap.classList.toggle('hidden', !rejected);
        reasonEl.required = rejected;
        if (reqMark) reqMark.hidden = !rejected;
    }
    statusEl.addEventListener('change', refresh);
    refresh();
})();
(function () {
    document.querySelectorAll('.mr-inline-details-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var pid = btn.getAttribute('data-panel-id');
            if (! pid) return;
            var panel = document.getElementById(pid);
            if (! panel) return;
            panel.classList.toggle('hidden');
            var open = ! panel.classList.contains('hidden');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            panel.setAttribute('aria-hidden', open ? 'false' : 'true');
        });
    });
})();
</script>
@endpush
