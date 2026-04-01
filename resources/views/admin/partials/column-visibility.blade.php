{{--
    Column visibility control for admin tables.
    Usage: @include('admin.partials.column-visibility', [
        'tableId' => 'sanghas-table',
        'storageKey' => 'admin-sanghas-columns',
        'columns' => [
            ['id' => 'name', 'label' => 'Name'],
            ['id' => 'username', 'label' => 'Username'],
            ...
        ],
    ])
    Actions column is always visible.
--}}
@php $columns = $columns ?? []; @endphp
<div class="relative inline-block" id="column-visibility-{{ $tableId }}">
    <button type="button" class="column-visibility-btn admin-dropdown-trigger whitespace-nowrap">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
        Columns
    </button>
    <div class="column-visibility-menu admin-dropdown-panel !left-0 !right-auto w-52 max-h-72 overflow-auto py-2 z-[90] hidden">
        @foreach($columns as $col)
            <label class="flex items-center gap-2 px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/80 active:bg-slate-100 dark:active:bg-slate-600/50 cursor-pointer transition-colors rounded-lg mx-1">
                <input type="checkbox" class="column-toggle rounded border-slate-300 dark:border-slate-500 text-amber-500 focus:ring-amber-400"
                       data-column="{{ $col['id'] }}" data-table="{{ $tableId }}" data-storage="{{ $storageKey }}">
                <span class="text-sm text-slate-700 dark:text-slate-200">{{ $col['label'] }}</span>
            </label>
        @endforeach
    </div>
</div>
