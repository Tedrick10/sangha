@extends('admin.layout')

@section('title', 'Sanghas')

@section('content')
@php
    $sanghaListUrl = function (array $overrides = []): string {
        $q = request()->except('page');
        foreach ($overrides as $key => $value) {
            if ($value === null || $value === '') {
                unset($q[$key]);
            } else {
                $q[$key] = $value;
            }
        }
        return route('admin.sanghas.index', $q);
    };
    $examTypeTabActive = ! request()->filled('exam_type_id');
@endphp
<div class="admin-page-header">
    <h1>Sanghas</h1>
    <div class="flex items-center gap-2">
        <form method="POST" action="{{ route('admin.sanghas.generate-eligible-list') }}">
            @csrf
            @if(request()->filled('exam_type_id'))
                <input type="hidden" name="exam_type_id" value="{{ request('exam_type_id') }}">
            @endif
            @if(request()->filled('exam_id'))
                <input type="hidden" name="exam_id" value="{{ request('exam_id') }}">
            @endif
            <button type="submit" class="admin-btn-filter">
            @include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4'])
            Generate
            </button>
        </form>
        <a href="{{ route('admin.sanghas.create') }}" class="admin-btn-add">@include('partials.icon', ['name' => 'plus', 'class' => 'w-5 h-5']) Add Sangha</a>
    </div>
</div>

<div class="mb-4 sm:mb-6">
    <p class="admin-filter-label mb-2">{{ t('exam_type', 'Exam Type') }}</p>
    <div class="admin-sangha-segmented-track" role="tablist" aria-label="{{ t('exam_type', 'Exam Type') }}">
        <a href="{{ $sanghaListUrl(['exam_type_id' => null]) }}"
            class="admin-sangha-segment {{ $examTypeTabActive ? 'admin-sangha-segment--active' : '' }}"
            @if($examTypeTabActive) aria-current="true" @endif
            role="tab">{{ t('all', 'All') }}</a>
        @foreach($examTypes as $et)
            @php $etActive = (string) request('exam_type_id') === (string) $et->id; @endphp
            <a href="{{ $sanghaListUrl(['exam_type_id' => $et->id]) }}"
                class="admin-sangha-segment {{ $etActive ? 'admin-sangha-segment--active' : '' }}"
                @if($etActive) aria-current="true" @endif
                role="tab">{{ $et->name }}</a>
        @endforeach
    </div>
</div>

<form method="GET" class="admin-filter-bar flex flex-nowrap items-end gap-2 sm:gap-3 overflow-x-auto">
    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
    @if(request('order'))<input type="hidden" name="order" value="{{ request('order') }}">@endif
    @if(request()->filled('exam_type_id'))<input type="hidden" name="exam_type_id" value="{{ request('exam_type_id') }}">@endif
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
            <option value="eligible" {{ request('moderation_status') === 'eligible' ? 'selected' : '' }}>{{ t('mp_card_eligible', 'Eligible') }}</option>
            <option value="pending" {{ request('moderation_status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('moderation_status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="needed_update" {{ request('moderation_status') === 'needed_update' ? 'selected' : '' }}>{{ t('status_needed_update', 'Needed Update') }}</option>
            <option value="rejected" {{ request('moderation_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </div>
    <div class="flex flex-wrap gap-2 shrink-0 ml-auto self-end items-center">
        <button type="submit" class="admin-btn-filter">@include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4']) Filter</button>
        @if(request()->hasAny(['search', 'monastery_id', 'exam_id', 'exam_type_id', 'moderation_status']))
            <a href="{{ route('admin.sanghas.index') }}" class="admin-btn-clear">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) Clear</a>
        @endif
    </div>
</form>

<div class="admin-table-card overflow-x-auto">
    <div class="flex justify-start px-4 sm:px-6 py-2 border-b border-slate-100 dark:border-slate-600">
        @include('admin.partials.column-visibility', [
            'tableId' => 'sanghas-table',
            'storageKey' => 'admin-sanghas-columns',
            'columns' => [
                ['id' => 'roll_number', 'label' => t('roll_number', 'Roll Number')],
                ['id' => 'desk_number', 'label' => t('desk_number_short', 'Desk No.') . ' / ' . t('exam_roll_number', 'Exam Roll Number')],
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
                @include('admin.partials.sortable-th', ['key' => 'eligible_roll_number', 'label' => t('roll_number', 'Roll Number'), 'dataColumn' => 'roll_number', 'class' => 'w-[11%] min-w-[96px]'])
                <th data-column="desk_number" class="text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider w-[12%] min-w-[110px]">
                    <span class="block">{{ t('desk_number_short', 'Desk No.') }}</span>
                    <span class="block font-normal normal-case text-[10px] leading-tight text-slate-500 dark:text-slate-400">({{ t('exam_roll_number', 'Exam Roll Number') }})</span>
                </th>
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
                    <td data-column="roll_number" class="align-top whitespace-nowrap"><span class="font-mono text-sm text-slate-600 dark:text-slate-300">{{ $sangha->eligible_roll_number ?? '—' }}</span></td>
                    <td data-column="desk_number" class="align-top whitespace-nowrap">
                        @if($sangha->moderationStatus() === \App\Models\Sangha::STATUS_APPROVED && filled($sangha->desk_number))
                            <span class="font-mono text-sm text-slate-700 dark:text-slate-200">{{ ($sangha->exam?->desk_number_prefix ?? '') . str_pad((string) $sangha->desk_number, 6, '0', STR_PAD_LEFT) }}</span>
                        @else
                            <span class="text-slate-500 dark:text-slate-400">—</span>
                        @endif
                    </td>
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
                        @if($sangha->moderationStatus() === \App\Models\Sangha::STATUS_APPROVED)
                            <span class="admin-badge-yes">Approved</span>
                        @elseif($sangha->moderationStatus() === \App\Models\Sangha::STATUS_NEEDED_UPDATE)
                            <span class="inline-flex items-center rounded-full bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300 px-2 py-0.5 text-xs font-semibold">{{ t('status_needed_update', 'Needed Update') }}</span>
                        @elseif($sangha->moderationStatus() === \App\Models\Sangha::STATUS_REJECTED)
                            <span class="admin-badge-rejected">Rejected</span>
                        @elseif($sangha->moderationStatus() === \App\Models\Sangha::STATUS_ELIGIBLE)
                            <span class="inline-flex items-center rounded-full bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300 px-2 py-0.5 text-xs font-semibold">Eligible</span>
                        @else
                            <span class="admin-badge-pending">Pending</span>
                        @endif
                    </td>
                    <td class="align-top text-right whitespace-nowrap">
                        @if(in_array($sangha->moderationStatus(), ['rejected', 'needed_update'], true) && $sangha->rejection_reason)
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
                    <td colspan="9" class="admin-table-empty">No sanghas yet. <a href="{{ route('admin.sanghas.create') }}">Create one</a>.</td>
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
            <h3 id="reason-modal-title" class="admin-reason-modal__title">{{ t('feedback_reason', 'Reason') }}</h3>
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
