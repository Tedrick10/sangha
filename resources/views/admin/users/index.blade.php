@extends('admin.layout')

@section('title', t('users'))

@section('content')
<div class="admin-page-header">
    <h1>{{ t('users') }}</h1>
    <a href="{{ route('admin.users.create') }}" class="admin-btn-add">@include('partials.icon', ['name' => 'plus', 'class' => 'w-5 h-5']) {{ t('add_user') }}</a>
</div>

<form method="GET" class="admin-filter-bar flex flex-wrap gap-4 items-end">
    <div class="admin-search-wrap">
        <label for="search" class="admin-filter-label">{{ t('search') }}</label>
        <div class="relative mt-1">
            <svg class="admin-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ t('search') }}..." class="admin-search-input">
        </div>
    </div>
    <div class="admin-filter-group">
        <label for="role" class="admin-filter-label">{{ t('filter_by_role') }}</label>
        <select name="role" id="role" class="admin-select mt-1">
            <option value="">{{ t('all') }}</option>
            @foreach($roles as $r)
                <option value="{{ $r->id }}" {{ request('role') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="admin-btn-filter">@include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4']) {{ t('filter') }}</button>
        @if(request('search') || request('role'))
            <a href="{{ route('admin.users.index') }}" class="admin-btn-clear">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) {{ t('clear') }}</a>
        @endif
    </div>
</form>

<div class="admin-table-card overflow-x-auto">
    <table class="admin-table divide-y divide-slate-100 dark:divide-slate-700">
        <thead>
            <tr>
                <th class="w-12 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">{{ t('no_column') }}</th>
                <th>{{ t('name') }}</th>
                <th>{{ t('email') }}</th>
                <th>{{ t('role') }}</th>
                <th class="text-right">{{ t('actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td class="text-slate-600 dark:text-slate-400">{{ $users->firstItem() + $loop->index }}</td>
                    <td><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $user->name }}</span></td>
                    <td><span class="text-slate-600 dark:text-slate-400">{{ $user->email }}</span></td>
                    <td>
                        @if($user->role)
                            <span class="admin-badge-active">{{ $user->role->name }}</span>
                        @else
                            <span class="admin-badge-inactive">—</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.users.edit', $user) }}" class="admin-action-link admin-action-edit">@include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4']) {{ t('edit') }}</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('{{ t('delete_user_confirm') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-action-link admin-action-delete">@include('partials.icon', ['name' => 'trash', 'class' => 'w-4 h-4']) {{ t('delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="admin-table-empty">{{ t('no_users') }} <a href="{{ route('admin.users.create') }}">{{ t('create_one') }}</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('admin.partials.table-pagination', ['paginator' => $users, 'routeName' => 'admin.users.index'])
@endsection
