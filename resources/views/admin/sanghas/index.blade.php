@extends('admin.layout')

@section('title', 'Sanghas')

@section('content')
<div class="admin-page-header">
    <h1>Sanghas</h1>
    <a href="{{ route('admin.sanghas.create') }}" class="admin-btn-add">@include('partials.icon', ['name' => 'plus', 'class' => 'w-5 h-5']) Add Sangha</a>
</div>

<form method="GET" class="admin-filter-bar flex flex-nowrap items-end gap-2 sm:gap-3 overflow-x-auto">
    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
    @if(request('order'))<input type="hidden" name="order" value="{{ request('order') }}">@endif
    <div class="flex flex-col gap-1 shrink-0 min-w-0">
        <label for="search" class="admin-filter-label text-xs max-w-[80px] sm:max-w-none">Search</label>
        <div class="relative w-36 sm:w-40 shrink-0">
            <svg class="admin-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name..." class="admin-search-input py-2 text-sm">
        </div>
    </div>
    <div class="flex flex-col gap-1 shrink-0 min-w-0">
        <label for="monastery_id" class="admin-filter-label text-xs max-w-[80px] sm:max-w-none">Monastery</label>
        <div class="admin-filter-select-wrap w-44 sm:w-56 max-w-[240px]">
            <select name="monastery_id" id="monastery_id" class="admin-select py-2 text-sm w-full">
                <option value="">All</option>
                @foreach($monasteries as $m)
                    <option value="{{ $m->id }}" {{ request('monastery_id') == $m->id ? 'selected' : '' }}>{{ Str::limit($m->name, 20) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="flex flex-col gap-1 shrink-0 min-w-0">
        <label for="exam_id" class="admin-filter-label text-xs max-w-[80px] sm:max-w-none">Exam</label>
        <div class="admin-filter-select-wrap w-44 sm:w-56 max-w-[240px]">
            <select name="exam_id" id="exam_id" class="admin-select py-2 text-sm w-full">
                <option value="">All</option>
                @foreach($exams as $e)
                    <option value="{{ $e->id }}" {{ request('exam_id') == $e->id ? 'selected' : '' }}>{{ Str::limit($e->name . ($e->exam_date ? ' ' . $e->exam_date->format('M d') : ''), 24) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="flex flex-col gap-1 shrink-0 min-w-0">
        <label for="moderation_status" class="admin-filter-label text-xs max-w-[80px] sm:max-w-none">Status</label>
        <select name="moderation_status" id="moderation_status" class="admin-select py-2 text-sm w-28 shrink-0">
            <option value="">All</option>
            <option value="pending" {{ request('moderation_status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('moderation_status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('moderation_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </div>
    <div class="flex gap-2 shrink-0 ml-auto self-end">
        <button type="submit" class="admin-btn-filter py-2 text-sm">@include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4']) Filter</button>
        @if(request()->hasAny(['search', 'monastery_id', 'exam_id', 'moderation_status']))
            <a href="{{ route('admin.sanghas.index') }}" class="admin-btn-clear py-2 text-sm">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) Clear</a>
        @endif
    </div>
</form>

<div class="admin-table-card overflow-x-auto">
    <div class="flex justify-start px-4 sm:px-6 py-2 border-b border-slate-100 dark:border-slate-600">
        @include('admin.partials.column-visibility', [
            'tableId' => 'sanghas-table',
            'storageKey' => 'admin-sanghas-columns',
            'columns' => [
                ['id' => 'user_id', 'label' => t('user_id', 'Student Id')],
                ['id' => 'name', 'label' => 'Name'],
                ['id' => 'father_nrc', 'label' => t('score_table_father_nrc', 'Father / NRC')],
                ['id' => 'monastery', 'label' => 'Monastery'],
                ['id' => 'exam', 'label' => 'Exam'],
                ['id' => 'status', 'label' => 'Status'],
            ],
        ])
    </div>
    <table id="sanghas-table" class="admin-table divide-y divide-slate-100">
        <thead>
            <tr>
                <th class="w-12 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">No.</th>
                @include('admin.partials.sortable-th', ['key' => 'username', 'label' => t('user_id', 'Student Id'), 'dataColumn' => 'user_id', 'class' => 'w-[11%] min-w-[96px]'])
                @include('admin.partials.sortable-th', ['key' => 'name', 'label' => 'Name', 'dataColumn' => 'name', 'class' => 'w-[14%] min-w-[120px]'])
                <th data-column="father_nrc" class="text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider w-[14%] min-w-[140px] max-w-[220px]">{{ t('score_table_father_nrc', 'Father / NRC') }}</th>
                @include('admin.partials.sortable-th', ['key' => 'monastery', 'label' => 'Monastery', 'dataColumn' => 'monastery', 'class' => 'w-[14%] min-w-[120px]'])
                @include('admin.partials.sortable-th', ['key' => 'exam', 'label' => 'Exam', 'dataColumn' => 'exam', 'class' => 'w-[16%] min-w-[130px]'])
                <th data-column="status" class="w-[16%] min-w-[170px] text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Status</th>
                <th class="w-[12%] min-w-[180px] text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sanghas as $sangha)
                <tr>
                    <td class="align-top text-slate-600 dark:text-slate-400">{{ $sanghas->firstItem() + $loop->index }}</td>
                    <td data-column="user_id" class="align-top whitespace-nowrap"><span class="font-mono text-sm text-slate-600 dark:text-slate-300">{{ $sangha->username ?? '—' }}</span></td>
                    <td data-column="name" class="align-top"><span class="font-semibold text-slate-900 dark:text-slate-100 block break-words">{{ $sangha->name }}</span></td>
                    <td data-column="father_nrc" class="align-top max-w-[220px] min-w-0">
                        @php
                            $hasFather = filled($sangha->father_name);
                            $hasNrc = filled($sangha->nrc_number);
                        @endphp
                        @if(! $hasFather && ! $hasNrc)
                            <span class="text-slate-500 dark:text-slate-400">—</span>
                        @else
                            <div class="flex flex-col gap-0.5">
                                @if($hasFather)
                                    <span class="font-semibold text-slate-900 dark:text-slate-100 break-words leading-snug">{{ $sangha->father_name }}</span>
                                @endif
                                @if($hasNrc)
                                    <span class="text-xs font-mono text-slate-500 dark:text-slate-400 break-words leading-snug">{{ $sangha->nrc_number }}</span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td data-column="monastery" class="align-top"><span class="block break-words">{{ $sangha->monastery->name }}</span></td>
                    <td data-column="exam" class="align-top"><span class="block break-words">{{ $sangha->exam?->name ?? '—' }}</span></td>
                    <td data-column="status" class="align-top">
                        @if($sangha->moderationStatus() === 'approved')
                            <span class="admin-badge-yes">Approved</span>
                        @elseif($sangha->moderationStatus() === 'rejected')
                            <span class="admin-badge-rejected">Rejected</span>
                        @else
                            <span class="admin-badge-pending">Pending</span>
                        @endif
                    </td>
                    <td class="align-top text-right whitespace-nowrap">
                        @if($sangha->moderationStatus() === 'rejected' && $sangha->rejection_reason)
                            <button type="button" class="admin-action-link admin-action-reason js-open-reason-modal" data-reason="{{ e($sangha->rejection_reason) }}">Reason</button>
                        @endif
                        <a href="{{ route('admin.sanghas.show', $sangha) }}" class="admin-action-link admin-action-view">View</a>
                        <a href="{{ route('admin.sanghas.edit', $sangha) }}" class="admin-action-link admin-action-edit">@include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4']) Edit</a>
                        <form action="{{ route('admin.sanghas.destroy', $sangha) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Delete this sangha?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-action-link admin-action-delete">@include('partials.icon', ['name' => 'trash', 'class' => 'w-4 h-4']) Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="admin-table-empty">No sanghas yet. <a href="{{ route('admin.sanghas.create') }}">Create one</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('admin.partials.table-pagination', ['paginator' => $sanghas, 'routeName' => 'admin.sanghas.index'])
<div id="reason-modal" class="admin-reason-modal hidden" aria-hidden="true">
    <div class="admin-reason-modal__backdrop js-close-reason-modal"></div>
    <div class="admin-reason-modal__panel" role="dialog" aria-modal="true" aria-labelledby="reason-modal-title">
        <div class="admin-reason-modal__header">
            <h3 id="reason-modal-title" class="admin-reason-modal__title">Rejection Reason</h3>
            <button type="button" class="admin-reason-modal__close js-close-reason-modal" aria-label="Close">✕</button>
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
