@extends('admin.layout')

@section('title', 'Add Sangha')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.sanghas.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Sanghas</a>
    <h1 class="admin-page-title">Add Sangha</h1>
</div>

@php
    $metaMon = $sanghaFieldMeta->get('monastery_id');
    $metaName = $sanghaFieldMeta->get('name');
    $metaFather = $sanghaFieldMeta->get('father_name');
    $metaNrc = $sanghaFieldMeta->get('nrc_number');
    $metaUser = $sanghaFieldMeta->get('username');
    $metaExam = $sanghaFieldMeta->get('exam_id');
    $metaDesc = $sanghaFieldMeta->get('description');
@endphp
<form action="{{ route('admin.sanghas.store') }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="monastery_id" class="admin-form-label">{{ $metaMon?->name ?? 'Monastery' }}{{ ($metaMon?->required ?? true) ? ' *' : '' }}</label>
            <select name="monastery_id" id="monastery_id" class="admin-select-input" @if($metaMon?->required ?? true) required @endif>
                <option value="">{{ $metaMon?->placeholder ?: t('select_monastery', 'Select monastery') }}</option>
                @foreach($monasteries as $m)
                    <option value="{{ $m->id }}" {{ old('monastery_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
            @error('monastery_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">{{ $metaName?->name ?? t('name') }}{{ ($metaName?->required ?? true) ? ' *' : '' }}</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="admin-input" placeholder="{{ $metaName?->placeholder ?: 'e.g. U Sitagu' }}" @if($metaName?->required ?? true) required @endif>
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="admin-form-group mb-0">
                <label for="father_name" class="admin-form-label">{{ $metaFather?->name ?? t('score_father_name_label', 'Father name') }}{{ ($metaFather?->required ?? false) ? ' *' : '' }}</label>
                <input type="text" name="father_name" id="father_name" value="{{ old('father_name') }}" maxlength="255" class="admin-input" placeholder="{{ $metaFather?->placeholder ?? t('score_optional_placeholder', 'Optional') }}" @if($metaFather?->required ?? false) required @endif>
                @error('father_name')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
            <div class="admin-form-group mb-0">
                <label for="nrc_number" class="admin-form-label">{{ $metaNrc?->name ?? t('score_nrc_label', 'NRC number') }}{{ ($metaNrc?->required ?? false) ? ' *' : '' }}</label>
                <input type="text" name="nrc_number" id="nrc_number" value="{{ old('nrc_number') }}" maxlength="100" class="admin-input" placeholder="{{ $metaNrc?->placeholder ?? t('score_optional_placeholder', 'Optional') }}" @if($metaNrc?->required ?? false) required @endif>
                @error('nrc_number')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="admin-form-group">
            <label for="username" class="admin-form-label">{{ $metaUser?->name ?? t('user_id', 'Student Id') }}{{ ($metaUser?->required ?? false) ? ' *' : '' }}</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}" class="admin-input" placeholder="{{ $metaUser?->placeholder ?? t('sangha_student_id_placeholder', 'Assign for login (optional until set)') }}" autocomplete="off" @if($metaUser?->required ?? false) required @endif>
            @error('username')<p class="admin-form-error">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ t('sangha_student_id_edit_hint', 'Required for the candidate to log in. Leave blank only if login is not needed yet.') }}</p>
        </div>
        <div class="admin-form-group">
            <label for="exam_id" class="admin-form-label">{{ $metaExam?->name ?? t('exam') }}{{ ($metaExam?->required ?? false) ? ' *' : '' }}</label>
            <select name="exam_id" id="exam_id" class="admin-select-input" @if($metaExam?->required ?? false) required @endif>
                <option value="">{{ $metaExam?->placeholder ?: (($metaExam?->required ?? false) ? t('select_exam', 'Select exam') : t('select_exam_optional', 'Select exam (optional)')) }}</option>
                @foreach($exams as $exam)
                    <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->name }}{{ $exam->exam_date ? ' (' . $exam->exam_date->format('M d, Y') . ')' : '' }}</option>
                @endforeach
            </select>
            @error('exam_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        @if($customFields->isNotEmpty())
            <div class="admin-form-section">
                <h3 class="admin-form-section-title">Custom Fields</h3>
                <div class="space-y-5">
                    @include('admin.partials.custom-fields-form', ['customFields' => $customFields, 'values' => []])
                </div>
            </div>
        @endif
        <div class="admin-form-group">
            <label for="description" class="admin-form-label">{{ $metaDesc?->name ?? t('description') }}{{ ($metaDesc?->required ?? false) ? ' *' : '' }}</label>
            <textarea name="description" id="description" rows="3" class="admin-textarea" placeholder="{{ $metaDesc?->placeholder ?? t('sangha_description_placeholder', 'Brief description of the sangha') }}" @if($metaDesc?->required ?? false) required @endif>{{ old('description') }}</textarea>
            @error('description')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ t('sangha_create_note', 'New sanghas are created as active and pending until you change status on the edit screen.') }}</p>
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">Create Sangha</button>
        <a href="{{ route('admin.sanghas.index') }}" class="admin-btn-secondary">Cancel</a>
    </div>
</form>
@endsection
