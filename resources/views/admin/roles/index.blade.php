@extends('admin.layout')

@section('title', t('roles'))

@section('content')
<div class="admin-page-header">
    <h1>{{ t('roles') }}</h1>
    <a href="{{ route('admin.roles.create') }}" class="admin-btn-add">@include('partials.icon', ['name' => 'plus', 'class' => 'w-5 h-5']) {{ t('add_role') }}</a>
</div>

<form method="GET" class="admin-filter-bar flex flex-wrap gap-4 items-end">
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
            <a href="{{ route('admin.roles.index') }}" class="admin-btn-clear">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) {{ t('clear') }}</a>
        @endif
    </div>
</form>

<div class="admin-table-card overflow-x-auto">
    <table class="admin-table divide-y divide-slate-100 dark:divide-slate-700">
        <thead>
            <tr>
                <th class="w-12 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">{{ t('no_column') }}</th>
                <th>{{ t('name') }}</th>
                <th>{{ t('permissions_count') }}</th>
                <th class="text-right">{{ t('actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
                <tr>
                    <td class="text-slate-600 dark:text-slate-400">{{ $roles->firstItem() + $loop->index }}</td>
                    <td><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $role->name }}</span></td>
                    <td><span class="text-slate-600 dark:text-slate-400">{{ count($role->permissions ?? []) }} {{ t('permissions') }}</span></td>
                    <td class="text-right">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="admin-action-link admin-action-edit">@include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4']) {{ t('edit') }}</a>
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('{{ t('delete_role_confirm') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-action-link admin-action-delete" @disabled($role->users()->exists())>@include('partials.icon', ['name' => 'trash', 'class' => 'w-4 h-4']) {{ t('delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="admin-table-empty">{{ t('no_roles') }} <a href="{{ route('admin.roles.create') }}">{{ t('create_one') }}</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('admin.partials.table-pagination', ['paginator' => $roles, 'routeName' => 'admin.roles.index'])
@endsection
