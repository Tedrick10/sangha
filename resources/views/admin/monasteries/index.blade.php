@extends('admin.layout')

@section('title', t('monasteries'))

@section('content')
<div class="admin-page-header">
    <h1>{{ t('monasteries') }}</h1>
    <a href="{{ route('admin.monasteries.create') }}" class="admin-btn-add">@include('partials.icon', ['name' => 'plus', 'class' => 'w-5 h-5']) {{ t('add_monastery') }}</a>
</div>

<form method="GET" class="admin-filter-bar flex flex-wrap gap-4 items-end">
    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
    @if(request('order'))<input type="hidden" name="order" value="{{ request('order') }}">@endif
    <div class="admin-search-wrap">
        <label for="search" class="admin-filter-label">{{ t('search') }}</label>
        <div class="relative mt-1">
            <svg class="admin-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, region, city, username..." class="admin-search-input">
        </div>
    </div>
    <div class="admin-filter-group">
        <label for="moderation_status" class="admin-filter-label">{{ t('status') }}</label>
        <select name="moderation_status" id="moderation_status" class="admin-select">
            <option value="">All</option>
            <option value="pending" {{ request('moderation_status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('moderation_status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('moderation_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="admin-btn-filter">@include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4']) {{ t('filter') }}</button>
        @if(request()->hasAny(['search', 'moderation_status']))
            <a href="{{ route('admin.monasteries.index') }}" class="admin-btn-clear">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) {{ t('clear') }}</a>
        @endif
    </div>
</form>

<div class="admin-table-card overflow-x-auto">
    <div class="flex justify-start px-4 sm:px-6 py-2 border-b border-slate-100 dark:border-slate-600">
        @include('admin.partials.column-visibility', [
            'tableId' => 'monasteries-table',
            'storageKey' => 'admin-monasteries-columns',
            'columns' => [
                ['id' => 'name', 'label' => t('name')],
                ['id' => 'username', 'label' => t('username')],
                ['id' => 'region_city', 'label' => t('region_city')],
                ['id' => 'moderation', 'label' => t('status')],
            ],
        ])
    </div>
    <table id="monasteries-table" class="admin-table divide-y divide-slate-100">
        <thead>
            <tr>
                <th class="w-12 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">{{ t('no_column') }}</th>
                @include('admin.partials.sortable-th', ['key' => 'name', 'label' => t('name'), 'dataColumn' => 'name'])
                @include('admin.partials.sortable-th', ['key' => 'username', 'label' => t('username'), 'dataColumn' => 'username'])
                @include('admin.partials.sortable-th', ['key' => 'region_city', 'label' => t('region_city'), 'dataColumn' => 'region_city'])
                <th class="min-w-[7rem] text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider" data-column="moderation">{{ t('status') }}</th>
                <th class="text-right">{{ t('actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monasteries as $monastery)
                <tr>
                    <td class="text-slate-600 dark:text-slate-400">{{ $monasteries->firstItem() + $loop->index }}</td>
                    <td data-column="name"><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $monastery->name }}</span></td>
                    <td data-column="username"><span class="font-mono text-sm text-slate-600 dark:text-slate-300">{{ $monastery->username ?? '—' }}</span></td>
                    <td data-column="region_city">{{ $monastery->region ?: '—' }} / {{ $monastery->city ?: '—' }}</td>
                    <td class="min-w-[7rem]" data-column="moderation">
                        @if($monastery->moderationStatus() === 'approved')
                            <span class="admin-badge-yes">Approved</span>
                        @elseif($monastery->moderationStatus() === 'rejected')
                            <span class="admin-badge-rejected">Rejected</span>
                        @else
                            <span class="admin-badge-pending">Pending</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($monastery->moderationStatus() === 'rejected' && $monastery->rejection_reason)
                            <button type="button" class="admin-action-link admin-action-reason js-open-reason-modal" data-reason="{{ e($monastery->rejection_reason) }}">View Reason</button>
                        @endif
                        <!-- <a href="{{ route('admin.monasteries.chat', $monastery) }}" class="admin-action-link">@include('partials.icon', ['name' => 'view', 'class' => 'w-4 h-4']) {{ t('chat', 'Chat') }}</a> -->
                        <a href="{{ route('admin.monasteries.show', $monastery) }}" class="admin-action-link admin-action-view">@include('partials.icon', ['name' => 'view', 'class' => 'w-4 h-4']) {{ t('view') }}</a>
                        <a href="{{ route('admin.monasteries.edit', $monastery) }}" class="admin-action-link admin-action-edit">@include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4']) {{ t('edit') }}</a>
                        <form action="{{ route('admin.monasteries.destroy', $monastery) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('{{ t('delete_monastery') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-action-link admin-action-delete">@include('partials.icon', ['name' => 'trash', 'class' => 'w-4 h-4']) {{ t('delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="admin-table-empty">{{ t('no_monasteries') }} <a href="{{ route('admin.monasteries.create') }}">{{ t('create_one') }}</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('admin.partials.table-pagination', ['paginator' => $monasteries, 'routeName' => 'admin.monasteries.index'])
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
