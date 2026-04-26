@extends('admin.layout')

@section('title', 'Custom Fields')

@section('content')
<div class="admin-page-header">
    <h1>Custom Fields</h1>
</div>

@php
    $entityTypes = \App\Models\CustomField::entityTypes();
    $activeEntity = request('entity_type', array_key_first($entityTypes));
@endphp

{{-- Tabs --}}
<div class="rounded-xl border overflow-x-auto overflow-y-hidden mb-4 sm:mb-6 -mx-2 sm:mx-0 bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-600 shadow-sm dark:shadow-none">
    <nav class="flex gap-0 min-w-max sm:min-w-0" role="tablist">
        @foreach($entityTypes as $entityKey => $formLabel)
            <a href="{{ route('admin.custom-fields.index', array_filter(['entity_type' => $entityKey, 'search' => request('search'), 'sort' => request('sort'), 'order' => request('order')])) }}"
               class="px-5 py-3.5 font-medium text-sm transition-all duration-200 {{ $activeEntity === $entityKey ? 'bg-white dark:bg-slate-800 text-amber-600 dark:text-amber-400 border-b-2 border-amber-500' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                {{ $formLabel }}
            </a>
        @endforeach
    </nav>
</div>

@php
    $customFieldsForForm = $groupedByForm->get($activeEntity, collect());
    $programmeEntities = ['programme_primary', 'programme_intermediate', 'programme_level_1', 'programme_level_2', 'programme_level_3'];
    $linkedCoreFields = in_array($activeEntity, $programmeEntities, true)
        ? ($linkedSanghaCoreFieldsByProgramme ?? collect())
        : collect();
    $displayFields = $linkedCoreFields->concat($customFieldsForForm)->values();
@endphp

<div class="flex flex-col sm:flex-row flex-wrap items-start sm:items-center justify-between gap-3 sm:gap-4 mb-4">
    <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100">{{ $entityTypes[$activeEntity] ?? $activeEntity }} form fields</h2>
    <div class="flex flex-wrap gap-2 sm:gap-3 items-center w-full sm:w-auto">
        <form method="GET" class="flex flex-wrap gap-2 items-center flex-1 sm:flex-initial min-w-0">
            <input type="hidden" name="entity_type" value="{{ $activeEntity }}">
            @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
            @if(request('order'))<input type="hidden" name="order" value="{{ request('order') }}">@endif
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search label, placeholder..." class="admin-search-input pl-9 py-2 w-full min-w-0 sm:w-64 text-sm">
            </div>
            <button type="submit" class="admin-btn-filter text-sm py-2">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4']) Search</button>
            @if(request('search'))
                <a href="{{ route('admin.custom-fields.index', ['entity_type' => $activeEntity]) }}" class="admin-btn-clear text-sm py-2">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) Clear</a>
            @endif
        </form>
        <a href="{{ route('admin.custom-fields.create', ['entity_type' => $activeEntity]) }}" class="admin-btn-add text-sm py-2">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-5 h-5']) Add Custom Field
        </a>
    </div>
</div>

<div class="admin-table-card overflow-x-auto">
    <div class="flex justify-start px-4 sm:px-6 py-2 border-b border-slate-100 dark:border-slate-600">
        @include('admin.partials.column-visibility', [
            'tableId' => 'custom-fields-table',
            'storageKey' => 'admin-custom-fields-columns',
            'columns' => [
                ['id' => 'label', 'label' => 'Label'],
                ['id' => 'type', 'label' => 'Type'],
                ['id' => 'placeholder', 'label' => 'Placeholder'],
                ['id' => 'required', 'label' => 'Required'],
            ],
        ])
    </div>
    <table id="custom-fields-table" class="admin-table divide-y divide-slate-100">
        <thead>
            <tr>
                <th class="w-12 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">No.</th>
                <th class="w-10 px-4"></th>
                @include('admin.partials.sortable-th', ['key' => 'name', 'label' => 'Label', 'dataColumn' => 'label'])
                @include('admin.partials.sortable-th', ['key' => 'type', 'label' => 'Type', 'dataColumn' => 'type'])
                @include('admin.partials.sortable-th', ['key' => 'placeholder', 'label' => 'Placeholder', 'dataColumn' => 'placeholder'])
                @include('admin.partials.sortable-th', ['key' => 'required', 'label' => 'Required', 'dataColumn' => 'required'])
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody id="sortable-tbody" data-entity-type="{{ $activeEntity }}">
            @forelse($displayFields as $index => $field)
                @php
                    $isLinkedCore = $field->entity_type !== $activeEntity;
                @endphp
                <tr @if(!$isLinkedCore) data-id="{{ $field->id }}" @endif>
                    <td class="text-slate-600 dark:text-slate-400">{{ $loop->iteration }}</td>
                    <td class="px-4 text-slate-400 {{ $isLinkedCore ? '' : 'cursor-grab active:cursor-grabbing drag-handle' }}" title="{{ $isLinkedCore ? 'Linked from Sangha' : 'Drag to reorder' }}">
                        @if(!$isLinkedCore)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6h2v2H8V6zm0 5h2v2H8v-2zm0 5h2v2H8v-2zm5-10h2v2h-2V6zm0 5h2v2h-2v-2zm0 5h2v2h-2v-2z"/></svg>
                        @else
                            —
                        @endif
                    </td>
                    <td data-column="label">
                        <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $field->name }}</span>
                        @if($field->is_built_in ?? false)
                            <span class="inline-block mt-1 text-xs text-slate-500 bg-slate-200 px-2 py-0.5 rounded-lg">Built-in</span>
                        @endif
                    </td>
                    <td data-column="type">{{ \App\Models\CustomField::fieldTypes()[$field->type] ?? $field->type }}</td>
                    <td data-column="placeholder">{{ $field->placeholder ?: '—' }}</td>
                    <td data-column="required">
                        @if($field->required)
                            <span class="admin-badge-yes">Yes</span>
                        @else
                            <span class="text-slate-400 font-medium">No</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.custom-fields.edit', $field) }}" class="admin-action-link admin-action-edit">@include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4']) Edit</a>
                        @if(\App\Models\CustomField::canDeleteInAdmin($field))
                            <form action="{{ route('admin.custom-fields.destroy', $field) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm(@json($field->is_built_in ? 'Delete this built-in field? It will not be restored automatically on the next sync.' : 'Delete this custom field?'));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-action-link admin-action-delete">@include('partials.icon', ['name' => 'trash', 'class' => 'w-4 h-4']) Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="admin-table-empty">
                        No custom fields for this form yet. <a href="{{ route('admin.custom-fields.create', ['entity_type' => $activeEntity]) }}">Add one</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($customFieldsForForm->isNotEmpty())
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tbody = document.getElementById('sortable-tbody');
    if (!tbody) return;
    var rows = tbody.querySelectorAll('tr[data-id]');
    if (rows.length === 0) return;

    new Sortable(tbody, {
        handle: '.drag-handle',
        animation: 150,
        draggable: 'tr[data-id]',
        onEnd: function(evt) {
            var ids = Array.from(tbody.querySelectorAll('tr[data-id]')).map(function(tr) {
                return parseInt(tr.getAttribute('data-id'), 10);
            });
            fetch('{{ route("admin.custom-fields.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    order: ids,
                    entity_type: tbody.getAttribute('data-entity-type')
                })
            });
        }
    });
});
</script>
@endpush
@endif
@endsection
