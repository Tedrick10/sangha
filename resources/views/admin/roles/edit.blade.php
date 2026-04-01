@extends('admin.layout')

@section('title', t('edit_role'))

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.roles.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) {{ t('roles') }}</a>
    <h1 class="admin-page-title">{{ t('edit_role') }}: {{ $role->name }}</h1>
</div>

<form action="{{ route('admin.roles.update', $role) }}" method="POST" class="admin-form-card">
    @csrf
    @method('PUT')
    <div class="space-y-6">
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">{{ t('role_name') }} *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required class="admin-input" placeholder="e.g. Admin, Editor">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label class="admin-form-label">{{ t('permissions') }}</label>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">{{ t('permissions_hint') }}</p>
            <div class="border border-slate-200 dark:border-slate-600 rounded-xl overflow-hidden">
                <div class="overflow-x-auto max-h-[50vh] overflow-y-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ t('resource') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300">{{ t('create') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300">{{ t('read') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300">{{ t('update') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300">{{ t('delete') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                            @php $current = old('permissions', $role->permissions ?? []); @endphp
                            @foreach($permissions as $resource => $label)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                    <td class="px-4 py-2 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $label }}</td>
                                    @foreach(['create','read','update','delete'] as $action)
                                        <td class="px-4 py-2 text-center">
                                            <input type="checkbox" name="permissions[]" value="{{ $resource }}.{{ $action }}" {{ in_array($resource.'.'.$action, $current) ? 'checked' : '' }} class="admin-checkbox">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="admin-form-actions mt-6">
        <button type="submit" class="admin-btn-primary">{{ t('update_role') }}</button>
        <a href="{{ route('admin.roles.index') }}" class="admin-btn-secondary">{{ t('cancel') }}</a>
    </div>
</form>
@endsection
