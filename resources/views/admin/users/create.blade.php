@extends('admin.layout')

@section('title', t('add_user'))

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.users.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) {{ t('users') }}</a>
    <h1 class="admin-page-title">{{ t('add_user') }}</h1>
</div>

<form action="{{ route('admin.users.store') }}" method="POST" class="admin-form-card">
    @csrf
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">{{ t('name') }} *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="admin-input" placeholder="User name">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="email" class="admin-form-label">{{ t('email') }} *</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="admin-input" placeholder="user@example.com">
            @error('email')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="password" class="admin-form-label">{{ t('password') }} *</label>
            <input type="password" name="password" id="password" required class="admin-input" placeholder="••••••••">
            @error('password')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="password_confirmation" class="admin-form-label">{{ t('confirm_password') }} *</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required class="admin-input" placeholder="••••••••">
        </div>
        <div class="admin-form-group">
            <label for="role_id" class="admin-form-label">{{ t('role') }}</label>
            <select name="role_id" id="role_id" class="admin-select-input">
                <option value="">{{ t('optional') }} — {{ t('no_role') }}</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            @error('role_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="admin-form-actions mt-6">
        <button type="submit" class="admin-btn-primary">{{ t('create_user') }}</button>
        <a href="{{ route('admin.users.index') }}" class="admin-btn-secondary">{{ t('cancel') }}</a>
    </div>
</form>
@endsection
