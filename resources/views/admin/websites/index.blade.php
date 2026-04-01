@extends('admin.layout')

@section('title', 'Website')

@section('content')
<div class="admin-page-header">
    <div>
        <h1>Website Content</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">Edit text, images, and sections shown on the public website. Changes here appear on the site.</p>
    </div>
</div>

<form method="GET" class="admin-filter-bar flex flex-wrap gap-4 items-end">
    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
    @if(request('order'))<input type="hidden" name="order" value="{{ request('order') }}">@endif
    <div class="admin-search-wrap">
        <label for="search" class="admin-filter-label">Search</label>
        <div class="relative mt-1">
            <svg class="admin-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Title, slug, content..." class="admin-search-input">
        </div>
    </div>
    <div class="admin-filter-group">
        <label for="is_published" class="admin-filter-label">Status</label>
        <select name="is_published" id="is_published" class="admin-select">
            <option value="">All</option>
            <option value="1" {{ request('is_published') === '1' ? 'selected' : '' }}>Published</option>
            <option value="0" {{ request('is_published') === '0' ? 'selected' : '' }}>Draft</option>
        </select>
    </div>
    @if($types->isNotEmpty())
    <div class="admin-filter-group">
        <label for="type" class="admin-filter-label">Type</label>
        <select name="type" id="type" class="admin-select">
            <option value="">All</option>
            @foreach($types as $t)
                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
    </div>
    @endif
    <div class="flex gap-2">
        <button type="submit" class="admin-btn-filter">@include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4']) Filter</button>
        @if(request()->hasAny(['search', 'is_published', 'type']))
            <a href="{{ route('admin.websites.index') }}" class="admin-btn-clear">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) Clear</a>
        @endif
    </div>
</form>

<div class="admin-table-card overflow-x-auto">
    <div class="flex justify-start px-4 sm:px-6 py-2 border-b border-slate-100 dark:border-slate-600">
        @include('admin.partials.column-visibility', [
            'tableId' => 'websites-table',
            'storageKey' => 'admin-websites-columns',
            'columns' => [
                ['id' => 'title', 'label' => 'Title'],
                ['id' => 'slug', 'label' => 'Slug'],
                ['id' => 'type', 'label' => 'Type'],
                ['id' => 'status', 'label' => 'Status'],
            ],
        ])
    </div>
    <table id="websites-table" class="admin-table divide-y divide-slate-100">
        <thead>
            <tr>
                <th class="w-12 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">No.</th>
                @include('admin.partials.sortable-th', ['key' => 'title', 'label' => 'Title', 'dataColumn' => 'title'])
                @include('admin.partials.sortable-th', ['key' => 'slug', 'label' => 'Slug', 'dataColumn' => 'slug'])
                @include('admin.partials.sortable-th', ['key' => 'type', 'label' => 'Type', 'dataColumn' => 'type'])
                @include('admin.partials.sortable-th', ['key' => 'is_published', 'label' => 'Status', 'dataColumn' => 'status'])
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($websites as $website)
                <tr>
                    <td class="text-slate-600 dark:text-slate-400">{{ $websites->firstItem() + $loop->index }}</td>
                    <td data-column="title"><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $website->title }}</span></td>
                    <td data-column="slug"><span class="font-mono text-sm text-slate-600 dark:text-slate-300">{{ $website->slug }}</span></td>
                    <td data-column="type">{{ $website->type ?: 'page' }}</td>
                    <td data-column="status">
                        @if($website->is_published)
                            <span class="admin-badge-active">Published</span>
                        @else
                            <span class="admin-badge-draft">Draft</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.websites.edit', $website) }}" class="admin-action-link admin-action-edit">@include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4']) Edit</a>
                        <a href="{{ $website->type === 'page' && $website->slug !== 'home' ? route('website.page', $website->slug) : url('/') }}" target="_blank" rel="noopener" class="admin-action-link ml-2">@include('partials.icon', ['name' => 'external-link', 'class' => 'w-4 h-4']) View on site</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="admin-table-empty">No website content yet. Run the database seeder to create default pages and sections.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('admin.partials.table-pagination', ['paginator' => $websites, 'routeName' => 'admin.websites.index'])
@endsection
