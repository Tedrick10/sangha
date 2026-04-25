@extends('layouts.monastery')

@section('title', t('request_submission_details', 'Request details'))

@php
    $backUrl = $submission->exam_type_id
        ? route('monastery.dashboard', ['tab' => 'exam', 'exam_type_id' => $submission->exam_type_id])
        : route('monastery.dashboard', ['tab' => 'main', 'screen' => 'request']);
@endphp

@section('content')
<div class="mx-auto max-w-2xl pb-8">
    <div class="mb-6">
        <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 transition hover:text-amber-600 dark:text-slate-400 dark:hover:text-amber-400">
            @include('partials.icon', ['name' => 'arrow-left', 'class' => 'h-4 w-4'])
            {{ t('back_to_requests', 'Back to requests') }}
        </a>
    </div>

    <header class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-500">{{ t('request_number_short', 'Request') }} · {{ $submission->created_at->format('M j, Y · H:i') }}</p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 sm:text-3xl">{{ t('request_submission_details', 'Request details') }}</h1>
        @if($submission->examType)
            <p class="mt-2 inline-flex rounded-full border border-teal-200/80 bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-900 dark:border-teal-700/60 dark:bg-teal-950/40 dark:text-teal-200">{{ $submission->examType->name }}</p>
        @endif
        <div class="mt-4 flex flex-wrap items-center gap-3">
            @if($submission->status === \App\Models\MonasteryFormRequest::STATUS_PENDING)
                <span class="inline-flex rounded-full bg-amber-500/15 px-3 py-1 text-sm font-semibold text-amber-800 ring-1 ring-amber-500/25 dark:text-amber-200">{{ t('status_pending', 'Pending') }}</span>
            @elseif($submission->status === \App\Models\MonasteryFormRequest::STATUS_APPROVED)
                <span class="inline-flex rounded-full bg-emerald-500/15 px-3 py-1 text-sm font-semibold text-emerald-800 ring-1 ring-emerald-500/25 dark:text-emerald-200">{{ t('status_approved', 'Approved') }}</span>
            @else
                <span class="inline-flex rounded-full bg-rose-500/15 px-3 py-1 text-sm font-semibold text-rose-800 ring-1 ring-rose-500/25 dark:text-rose-200">{{ t('status_rejected', 'Rejected') }}</span>
            @endif
            @if($submission->reviewed_at)
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ t('reviewed_at', 'Reviewed') }} · {{ $submission->reviewed_at->format('M j, Y H:i') }}</span>
            @endif
        </div>
    </header>

    @if($submission->status === \App\Models\MonasteryFormRequest::STATUS_REJECTED && filled($submission->rejection_reason))
        <div class="mb-8 rounded-2xl border border-rose-200/90 bg-rose-50 px-5 py-4 dark:border-rose-800/50 dark:bg-rose-950/35">
            <p class="text-xs font-semibold uppercase tracking-wide text-rose-800 dark:text-rose-300">{{ t('rejection_note', 'Rejection note') }}</p>
            <p class="mt-2 text-sm leading-relaxed text-rose-900 dark:text-rose-100/95 whitespace-pre-wrap break-words">{{ $submission->rejection_reason }}</p>
        </div>
    @endif

    <section class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-sm dark:border-slate-700/60 dark:bg-slate-900/60 dark:shadow-none">
        <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-700/80">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ t('submitted_fields', 'Submitted information') }}</h2>
            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ t('submitted_fields_hint', 'Everything you included in this request.') }}</p>
        </div>
        <dl class="divide-y divide-slate-100 dark:divide-slate-700/70">
            @forelse($fields as $field)
                @php
                    $raw = $submission->getRequestFieldValue($field);
                    $isFile = $field->type === 'media' || $field->type === 'document' || $field->type === 'video';
                @endphp
                <div class="px-5 py-5 sm:px-6 sm:py-6">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $field->name }}</dt>
                    <dd class="mt-2 min-w-0 text-base leading-relaxed text-slate-900 dark:text-slate-100">
                        @if(! filled($raw))
                            <span class="text-slate-400 dark:text-slate-500">—</span>
                        @elseif($field->type === 'checkbox')
                            {{ in_array((string) $raw, ['1', 'true'], true) ? t('yes', 'Yes') : t('no', 'No') }}
                        @elseif($isFile)
                            @php $fileUrl = route('monastery.requests.file', ['monasteryFormRequest' => $submission, 'path' => $raw]); @endphp
                            <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex max-w-full items-center gap-2 break-all rounded-xl bg-amber-500/10 px-4 py-3 text-sm font-medium text-amber-900 ring-1 ring-amber-500/20 transition hover:bg-amber-500/15 dark:text-amber-100 dark:ring-amber-500/25">
                                @include('partials.icon', ['name' => 'external-link', 'class' => 'h-4 w-4 shrink-0'])
                                <span class="break-all">{{ basename($raw) }}</span>
                            </a>
                        @elseif($field->type === 'approved_sangha' || ($field->entity_type === 'monastery_exam' && $field->slug === 'exam_session'))
                            <span class="block whitespace-pre-wrap break-words">{{ $submission->displaySubmittedValue($field, $raw) }}</span>
                        @else
                            <span class="block whitespace-pre-wrap break-words">{{ $raw }}</span>
                        @endif
                    </dd>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-slate-500 dark:text-slate-400">{{ t('no_custom_fields', 'No fields for this form.') }}</div>
            @endforelse
        </dl>
    </section>
</div>
@endsection
