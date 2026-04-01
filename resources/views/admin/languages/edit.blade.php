@extends('admin.layout')

@section('title', t('edit_language'))

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.languages.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) {{ t('languages') }}</a>
    <h1 class="admin-page-title">{{ t('edit_language') }}</h1>
</div>

<form action="{{ route('admin.languages.update', $language) }}" method="POST" class="admin-form-card">
    @csrf
    @method('PUT')
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">{{ t('language_name') }} *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $language->name) }}" required class="admin-input" placeholder="e.g. Myanmar, Pali">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="code" class="admin-form-label">{{ t('language_code') }} *</label>
            <input type="text" name="code" id="code" value="{{ old('code', $language->code) }}" required class="admin-input" placeholder="e.g. my, pi" maxlength="10">
            @error('code')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="flag" class="admin-form-label">{{ t('language_flag') }} {{ t('optional') }}</label>
            <input type="text" name="flag" id="flag" value="{{ old('flag', $language->flag) }}" class="admin-input" placeholder="e.g. 🇲🇲 or MM" maxlength="20">
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ t('language_flag_hint') }}</p>
            @error('flag')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-3 pt-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $language->is_active) ? 'checked' : '' }} class="admin-checkbox">
            <label for="is_active" class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ t('active') }}</label>
        </div>
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">{{ t('update_language') }}</button>
        <a href="{{ route('admin.languages.index') }}" class="admin-btn-secondary">{{ t('cancel') }}</a>
        <a href="{{ route('admin.translations.edit', $language) }}" class="admin-btn-secondary">{{ t('edit_translations') }}</a>
    </div>
</form>
@endsection
