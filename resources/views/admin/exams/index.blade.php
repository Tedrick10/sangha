@extends('admin.layout')

@section('title', 'Exams')

@section('content')
<div class="admin-page-header">
    <h1>Exams</h1>
    <a href="{{ route('admin.exams.create') }}" class="admin-btn-add">@include('partials.icon', ['name' => 'plus', 'class' => 'w-5 h-5']) Add Exam</a>
</div>

<form method="GET" class="admin-filter-bar flex flex-wrap gap-4 items-end">
    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
    @if(request('order'))<input type="hidden" name="order" value="{{ request('order') }}">@endif
    <div class="admin-search-wrap">
        <label for="search" class="admin-filter-label">Search</label>
        <div class="relative mt-1">
            <svg class="admin-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, location, description..." class="admin-search-input">
        </div>
    </div>
    <div class="admin-filter-group">
        <label for="exam_type_id" class="admin-filter-label">Exam Type</label>
        <select name="exam_type_id" id="exam_type_id" class="admin-select">
            <option value="">All</option>
            @foreach($examTypes as $et)
                <option value="{{ $et->id }}" {{ request('exam_type_id') == $et->id ? 'selected' : '' }}>{{ $et->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="admin-filter-group">
        <label for="is_active" class="admin-filter-label">Status</label>
        <select name="is_active" id="is_active" class="admin-select">
            <option value="">All</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="admin-btn-filter">@include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4']) Filter</button>
        @if(request()->hasAny(['search', 'exam_type_id', 'is_active']))
            <a href="{{ route('admin.exams.index') }}" class="admin-btn-clear">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) Clear</a>
        @endif
    </div>
</form>

<div class="admin-table-card overflow-x-auto">
    <div class="flex justify-start px-4 sm:px-6 py-2 border-b border-slate-100 dark:border-slate-600">
        @include('admin.partials.column-visibility', [
            'tableId' => 'exams-table',
            'storageKey' => 'admin-exams-columns',
            'columns' => [
                ['id' => 'name', 'label' => 'Name'],
                ['id' => 'date', 'label' => 'Date'],
                ['id' => 'exam_type_location', 'label' => 'Exam Type / Location'],
                ['id' => 'status', 'label' => 'Status'],
            ],
        ])
    </div>
    <table id="exams-table" class="admin-table divide-y divide-slate-100">
        <thead>
            <tr>
                <th class="w-12 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">No.</th>
                @include('admin.partials.sortable-th', ['key' => 'name', 'label' => 'Name', 'dataColumn' => 'name'])
                @include('admin.partials.sortable-th', ['key' => 'exam_date', 'label' => 'Date', 'dataColumn' => 'date'])
                @include('admin.partials.sortable-th', ['key' => 'exam_type_location', 'label' => 'Exam Type / Location', 'dataColumn' => 'exam_type_location'])
                @include('admin.partials.sortable-th', ['key' => 'is_active', 'label' => 'Status', 'dataColumn' => 'status'])
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exams as $exam)
                <tr>
                    <td class="text-slate-600 dark:text-slate-400">{{ $exams->firstItem() + $loop->index }}</td>
                    <td data-column="name"><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $exam->name }}</span></td>
                    <td data-column="date">{{ $exam->exam_date?->format('M d, Y') ?? '—' }}</td>
                    <td data-column="exam_type_location">{{ $exam->examType?->name ?? $exam->location ?? '—' }}</td>
                    <td data-column="status">
                        @if($exam->is_active)
                            <span class="admin-badge-active">Active</span>
                        @else
                            <span class="admin-badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td class="text-right whitespace-nowrap">
                        <a href="{{ route('admin.exams.entrances', $exam) }}" class="admin-action-link admin-action-view">{{ t('exam_entrance_short', 'Entrance') }}</a>
                        <a href="{{ route('admin.exams.edit', $exam) }}" class="admin-action-link admin-action-edit">@include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4']) Edit</a>
                        <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Delete this exam?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-action-link admin-action-delete">@include('partials.icon', ['name' => 'trash', 'class' => 'w-4 h-4']) Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="admin-table-empty">No exams yet. <a href="{{ route('admin.exams.create') }}">Create one</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('admin.partials.table-pagination', ['paginator' => $exams, 'routeName' => 'admin.exams.index'])
@endsection
