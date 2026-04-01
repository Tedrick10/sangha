@extends('admin.layout')

@section('title', t('languages'))

@section('content')
<div class="admin-page-header">
    <h1>{{ t('languages') }}</h1>
    <a href="{{ route('admin.languages.create') }}" class="admin-btn-add">@include('partials.icon', ['name' => 'plus', 'class' => 'w-5 h-5']) {{ t('add_language') }}</a>
</div>

<form method="GET" class="admin-filter-bar flex flex-wrap gap-4 items-end">
    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
    @if(request('order'))<input type="hidden" name="order" value="{{ request('order') }}">@endif
    <div class="admin-search-wrap">
        <label for="search" class="admin-filter-label">{{ t('search') }}</label>
        <div class="relative mt-1">
            <svg class="admin-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ t('search') }}..." class="admin-search-input">
        </div>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="admin-btn-filter">@include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4']) {{ t('filter') }}</button>
        @if(request('search'))
            <a href="{{ route('admin.languages.index') }}" class="admin-btn-clear">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) {{ t('clear') }}</a>
        @endif
    </div>
</form>

<div class="admin-table-card overflow-x-auto">
    <div class="flex justify-start px-4 sm:px-6 py-2 border-b border-slate-100 dark:border-slate-600">
        @include('admin.partials.column-visibility', [
            'tableId' => 'languages-table',
            'storageKey' => 'admin-languages-columns',
            'columns' => [
                ['id' => 'flag', 'label' => t('language_flag')],
                ['id' => 'name', 'label' => t('name')],
                ['id' => 'code', 'label' => t('language_code')],
                ['id' => 'status', 'label' => t('status')],
            ],
        ])
    </div>
    <table id="languages-table" class="admin-table divide-y divide-slate-100">
        <thead>
            <tr>
                <th class="w-12 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">{{ t('no_column') }}</th>
                <th class="w-14 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">{{ t('language_flag') }}</th>
                @include('admin.partials.sortable-th', ['key' => 'name', 'label' => t('name'), 'dataColumn' => 'name'])
                @include('admin.partials.sortable-th', ['key' => 'code', 'label' => t('language_code'), 'dataColumn' => 'code'])
                @include('admin.partials.sortable-th', ['key' => 'is_active', 'label' => t('status'), 'dataColumn' => 'status'])
                <th class="text-right">{{ t('actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($languages as $lang)
                <tr>
                    <td class="text-slate-600 dark:text-slate-400">{{ $languages->firstItem() + $loop->index }}</td>
                    <td data-column="flag" class="text-lg">
                        @if($lang->flag)
                            @if($lang->isFlagCountryCode())
                                <img src="https://flagcdn.com/w40/{{ strtolower($lang->flag) }}.png" alt="" class="inline-block w-6 h-4 object-cover rounded-sm" width="24" height="16">
                            @else
                                <span aria-hidden="true">{{ $lang->flag }}</span>
                            @endif
                        @else
                            <span class="text-slate-400 dark:text-slate-500">—</span>
                        @endif
                    </td>
                    <td data-column="name"><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $lang->name }}</span></td>
                    <td data-column="code"><span class="font-mono text-sm text-slate-600 dark:text-slate-300">{{ $lang->code }}</span></td>
                    <td data-column="status">
                        @if($lang->is_active)
                            <span class="admin-badge-active">{{ t('active') }}</span>
                        @else
                            <span class="admin-badge-inactive">{{ t('inactive') }}</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.translations.edit', $lang) }}" class="admin-action-link admin-action-edit">@include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4']) {{ t('edit_translations') }}</a>
                        <a href="{{ route('admin.languages.edit', $lang) }}" class="admin-action-link admin-action-edit">@include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4']) {{ t('edit') }}</a>
                        <form action="{{ route('admin.languages.destroy', $lang) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('{{ t('delete_language') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-action-link admin-action-delete">@include('partials.icon', ['name' => 'trash', 'class' => 'w-4 h-4']) {{ t('delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="admin-table-empty">{{ t('no_languages') }} <a href="{{ route('admin.languages.create') }}">{{ t('create_one') }}</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('admin.partials.table-pagination', ['paginator' => $languages, 'routeName' => 'admin.languages.index'])
@endsection
