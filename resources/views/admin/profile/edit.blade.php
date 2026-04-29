@extends('admin.layout')

@section('title', t('profile'))

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.monasteries.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) {{ t('admin_panel') }}</a>
    <h1 class="admin-page-title">{{ t('profile') }}</h1>
</div>

<form action="{{ route('admin.profile.update') }}" method="POST" class="admin-form-card">
    @csrf
    @method('PUT')
    <div class="space-y-6">
        <div class="admin-form-section first:border-t-0 first:pt-0 first:mt-0">
            <h3 class="admin-form-section-title">{{ t('information') }}</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ t('update_personal_info_hint') }}</p>
            <div class="space-y-5">
                <div class="admin-form-group">
                    <label for="name" class="admin-form-label">{{ t('name') }} *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="admin-input" placeholder="{{ t('name') }}">
                    @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="admin-form-group">
                    <label for="email" class="admin-form-label">{{ t('email') }} *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="admin-input" placeholder="email@example.com">
                    @error('email')<p class="admin-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="admin-form-group">
                    <label for="role_display" class="admin-form-label">{{ t('role_name', 'Role') }}</label>
                    <input type="text" id="role_display" value="{{ $user->role?->name ?? t('not_assigned', 'Not assigned') }}" class="admin-input" readonly>
                </div>
            </div>
        </div>
        <div class="admin-form-section">
            <h3 class="admin-form-section-title">{{ t('change_password') }}</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ t('change_password_hint') }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="admin-form-group">
                    <label for="password" class="admin-form-label">{{ t('password') }}</label>
                    <input type="password" name="password" id="password" class="admin-input" placeholder="{{ t('leave_blank_unchanged') }}">
                    @error('password')<p class="admin-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="admin-form-group">
                    <label for="password_confirmation" class="admin-form-label">{{ t('confirm_password') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="admin-input" placeholder="{{ t('confirm_password') }}">
                </div>
            </div>
        </div>
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">{{ t('update') }}</button>
        <a href="{{ route('admin.monasteries.index') }}" class="admin-btn-secondary">{{ t('cancel') }}</a>
    </div>
</form>
@endsection
