@extends('admin.layout')

@section('title', t('monastery_requests', 'Transfer Sangha'))

@php
    $filterStatus = request('request_status');
    if (in_array($filterStatus, ['all', null, ''], true)) {
        $filterStatus = '';
    }
    $detailQuery = array_filter(request()->only(['search', 'request_status', 'form_scope', 'exam_type_id']), fn ($v) => $v !== null && $v !== '' && $v !== 'all');
@endphp

@section('content')
<div class="admin-page-header">
    <div>
        <h1>{{ t('monastery_requests', 'Transfer Sangha') }}</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ t('monastery_requests_subtitle', 'Review and update monastery portal submissions.') }}</p>
    </div>
</div>

<form method="GET" class="admin-filter-bar flex flex-wrap gap-4 items-end mb-6">
    <input type="hidden" name="form_scope" value="general">
    <div class="admin-search-wrap">
        <label for="search" class="admin-filter-label">{{ t('search', 'Search') }}</label>
        <div class="relative mt-1">
            <svg class="admin-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ t('monastery_request_search_placeholder', 'Name, region, city, username…') }}" class="admin-search-input">
        </div>
    </div>
    <div class="admin-filter-group">
        <label for="request_status" class="admin-filter-label">{{ t('status', 'Status') }}</label>
        <select name="request_status" id="request_status" class="admin-select">
            <option value="" {{ $filterStatus === '' ? 'selected' : '' }}>{{ t('all', 'All') }}</option>
            <option value="pending" {{ $filterStatus === 'pending' ? 'selected' : '' }}>{{ t('status_pending', 'Pending') }}</option>
            <option value="approved" {{ $filterStatus === 'approved' ? 'selected' : '' }}>{{ t('status_approved', 'Approved') }}</option>
            <option value="rejected" {{ $filterStatus === 'rejected' ? 'selected' : '' }}>{{ t('status_rejected', 'Rejected') }}</option>
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="admin-btn-filter">@include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4']) {{ t('filter', 'Filter') }}</button>
        @if(request()->hasAny(['search', 'request_status']) && (filled(request('search')) || filled(request('request_status'))))
            <a href="{{ route('admin.monastery-requests.index', array_filter(['form_scope' => $formScope, 'exam_type_id' => request('exam_type_id')])) }}" class="admin-btn-clear">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) {{ t('clear', 'Clear') }}</a>
        @endif
    </div>
</form>

<div class="admin-table-card overflow-x-auto rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-sm dark:shadow-none">
    <table class="admin-table divide-y divide-slate-100 dark:divide-slate-700/80">
        <thead>
            <tr>
                <th class="w-12 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">{{ t('no_column', 'No.') }}</th>
                <th>{{ t('submitted_at', 'Submitted') }}</th>
                <th>{{ t('monastery', 'Monastery') }}</th>
                @if($formScope === 'exam')
                    <th>{{ t('exam_type') }}</th>
                @endif
                <th class="max-w-[200px]">{{ t('summary', 'Summary') }}</th>
                <th data-column="status" class="whitespace-nowrap">{{ t('status', 'Status') }}</th>
                <th class="text-right min-w-[9rem] sm:min-w-[11rem]">{{ t('actions', 'Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($formRequests as $row)
                <tr>
                    <td class="text-slate-500 dark:text-slate-400">{{ $formRequests->firstItem() + $loop->index }}</td>
                    <td class="text-slate-600 dark:text-slate-400 whitespace-nowrap text-sm">{{ $row->created_at->format('M d, Y H:i') }}</td>
                    <td>
                        <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $row->monastery?->name ?? '—' }}</span>
                        <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $row->monastery?->username ?? '' }}</span>
                    </td>
                    @if($formScope === 'exam')
                        <td class="text-sm text-slate-700 dark:text-slate-300">{{ $row->examType?->name ?? '—' }}</td>
                    @endif
                    <td class="text-slate-700 dark:text-slate-300 text-sm leading-snug">{{ $row->summaryPreview() }}</td>
                    <td data-column="status">
                        @if($row->status === \App\Models\MonasteryFormRequest::STATUS_APPROVED)
                            <span class="admin-badge-yes">{{ t('status_approved', 'Approved') }}</span>
                        @elseif($row->status === \App\Models\MonasteryFormRequest::STATUS_REJECTED)
                            <span class="admin-badge-rejected">{{ t('status_rejected', 'Rejected') }}</span>
                        @else
                            <span class="admin-badge-pending">{{ t('status_pending', 'Pending') }}</span>
                        @endif
                    </td>
                    <td class="admin-table-actions-stack text-right align-top">
                        <div class="flex flex-col items-stretch gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end sm:gap-x-2 sm:gap-y-1">
                            @if($row->status === \App\Models\MonasteryFormRequest::STATUS_REJECTED && $row->rejection_reason)
                                <button type="button" class="admin-action-link admin-action-reason js-open-reason-modal justify-center sm:justify-start" data-reason="{{ e($row->rejection_reason) }}">{{ t('view_reason', 'View Reason') }}</button>
                            @endif
                            <a href="{{ route('admin.monastery-requests.show', $row) }}{{ count($detailQuery) ? '?' . http_build_query($detailQuery) : '' }}" class="admin-action-link admin-action-edit inline-flex items-center justify-center gap-1 sm:justify-start">@include('partials.icon', ['name' => 'eye', 'class' => 'w-4 h-4']) {{ t('details', 'Details') }}</a>
                            <form action="{{ route('admin.monastery-requests.destroy', $row) }}" method="POST" class="block sm:inline-block sm:ml-0" onsubmit="return confirm(@json(t('confirm_delete_request', 'Delete this request permanently?')));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-action-link admin-action-delete inline-flex w-full items-center justify-center gap-1 sm:inline-flex sm:w-auto sm:justify-start">@include('partials.icon', ['name' => 'trash', 'class' => 'w-4 h-4']) {{ t('delete', 'Delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $formScope === 'exam' ? 7 : 6 }}" class="admin-table-empty py-12">{{ t('no_monastery_requests_yet', 'No monastery requests yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('admin.partials.table-pagination', ['paginator' => $formRequests, 'routeName' => 'admin.monastery-requests.index'])

<div id="reason-modal" class="admin-reason-modal hidden" aria-hidden="true">
    <div class="admin-reason-modal__backdrop js-close-reason-modal"></div>
    <div class="admin-reason-modal__panel" role="dialog" aria-modal="true" aria-labelledby="reason-modal-title">
        <div class="admin-reason-modal__header">
            <h3 id="reason-modal-title" class="admin-reason-modal__title">{{ t('rejection_reason', 'Rejection reason') }}</h3>
            <button type="button" class="admin-reason-modal__close js-close-reason-modal" aria-label="{{ t('close', 'Close') }}">✕</button>
        </div>
        <div id="reason-modal-content" class="admin-reason-modal__content"></div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('reason-modal');
    var content = document.getElementById('reason-modal-content');
    if (!modal || !content) return;

    function closeModal() {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.js-open-reason-modal').forEach(function (button) {
        button.addEventListener('click', function () {
            content.textContent = button.getAttribute('data-reason') || '';
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        });
    });

    document.querySelectorAll('.js-close-reason-modal').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
});
</script>
@endpush
@endsection
