@extends('admin.layout')

@section('title', 'Add Score')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.scores.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Scores</a>
    <h1 class="admin-page-title">Add Score</h1>
</div>

<form action="{{ route('admin.scores.store') }}" method="POST" class="admin-form-card">
    @csrf
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="sangha_id" class="admin-form-label">Sangha *</label>
            <select name="sangha_id" id="sangha_id" required class="admin-select-input">
                <option value="">Select sangha</option>
                @foreach($sanghas as $s)
                    <option value="{{ $s->id }}" {{ old('sangha_id') == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->monastery->name ?? '—' }})</option>
                @endforeach
            </select>
            @error('sangha_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="subject_id" class="admin-form-label">Subject *</label>
            <select name="subject_id" id="subject_id" required class="admin-select-input">
                <option value="">Select subject</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
            @error('subject_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="exam_id" class="admin-form-label">Exam</label>
            <select name="exam_id" id="exam_id" class="admin-select-input">
                <option value="">Select exam (optional)</option>
                @foreach($exams as $e)
                    <option value="{{ $e->id }}" {{ old('exam_id') == $e->id ? 'selected' : '' }}>{{ $e->name }}{{ $e->exam_date ? ' (' . $e->exam_date->format('M d, Y') . ')' : '' }}</option>
                @endforeach
            </select>
            @error('exam_id')<p class="admin-form-error">{{ $message }}</p>@enderror
            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ t('score_triplet_unique_hint', 'Only one score row is allowed per sangha, subject, and exam.') }}</p>
        </div>
        <div class="admin-form-group">
            <label for="desk_number" class="admin-form-label">{{ t('desk_number', 'Desk No.') }}</label>
            <input type="text" name="desk_number" id="desk_number" value="{{ old('desk_number') }}" maxlength="120" class="admin-input" placeholder="{{ t('score_optional_placeholder', 'Optional') }}" autocomplete="off">
            @error('desk_number')<p class="admin-form-error">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ t('score_desk_hint', 'Optional. Stored on this score row for lists and records.') }}</p>
        </div>
        <div class="admin-form-group">
            <label for="value" class="admin-form-label">Score Value *</label>
            <input type="number" name="value" id="value" value="{{ old('value', 0) }}" step="0.01" min="0" required class="admin-input" placeholder="e.g. 85.5">
            @error('value')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="father_name" class="admin-form-label">{{ t('score_father_name_label', 'Father name') }}</label>
            <input type="text" name="father_name" id="father_name" value="{{ old('father_name') }}" maxlength="255" class="admin-input" placeholder="Optional">
            @error('father_name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="nrc_number" class="admin-form-label">{{ t('score_nrc_label', 'NRC number') }}</label>
            <input type="text" name="nrc_number" id="nrc_number" value="{{ old('nrc_number') }}" maxlength="100" class="admin-input" placeholder="{{ t('score_optional_placeholder', 'Optional') }}">
            @error('nrc_number')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="candidate_ref" class="admin-form-label">{{ t('score_candidate_ref_label', 'Student Id') }}</label>
            <input type="text" name="candidate_ref" id="candidate_ref" value="{{ old('candidate_ref') }}" maxlength="120" class="admin-input" placeholder="{{ t('score_candidate_ref_placeholder', 'e.g. ရသမဏ၁') }}" dir="auto">
            @error('candidate_ref')<p class="admin-form-error">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ t('score_candidate_ref_hint', 'Shown on lists and exports; optional.') }}</p>
        </div>
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">Create Score</button>
        <a href="{{ route('admin.scores.index') }}" class="admin-btn-secondary">Cancel</a>
    </div>
</form>
@endsection
