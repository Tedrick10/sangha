@extends('admin.layout')

@section('title', 'Edit Score')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.scores.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Scores</a>
    <h1 class="admin-page-title">Edit Score</h1>
</div>

<form action="{{ route('admin.scores.update', $score) }}" method="POST" class="admin-form-card">
    @csrf
    @method('PUT')
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="sangha_id" class="admin-form-label">Sangha *</label>
            <select name="sangha_id" id="sangha_id" required class="admin-select-input">
                <option value="">Select sangha</option>
                @foreach($sanghas as $s)
                    <option value="{{ $s->id }}" {{ old('sangha_id', $score->sangha_id) == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->monastery->name ?? '—' }})</option>
                @endforeach
            </select>
            @error('sangha_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="subject_id" class="admin-form-label">Subject *</label>
            <select name="subject_id" id="subject_id" required class="admin-select-input">
                <option value="">Select subject</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ old('subject_id', $score->subject_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
            @error('subject_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="exam_id" class="admin-form-label">Exam</label>
            <select name="exam_id" id="exam_id" class="admin-select-input">
                <option value="">Select exam (optional)</option>
                @foreach($exams as $e)
                    <option value="{{ $e->id }}" {{ old('exam_id', $score->exam_id) == $e->id ? 'selected' : '' }}>{{ $e->name }}{{ $e->exam_date ? ' (' . $e->exam_date->format('M d, Y') . ')' : '' }}</option>
                @endforeach
            </select>
            @error('exam_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="value" class="admin-form-label">Score Value *</label>
            <input type="number" name="value" id="value" value="{{ old('value', $score->value) }}" step="0.01" min="0" required class="admin-input" placeholder="e.g. 85.5">
            @error('value')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="moderation_decision" class="admin-form-label">Moderation Decision</label>
            <select name="moderation_decision" id="moderation_decision" class="admin-select-input">
                <option value="">Pending (No decision)</option>
                <option value="pass" {{ old('moderation_decision', $score->moderation_decision) === 'pass' ? 'selected' : '' }}>Pass</option>
                <option value="fail" {{ old('moderation_decision', $score->moderation_decision) === 'fail' ? 'selected' : '' }}>Fail</option>
            </select>
            @error('moderation_decision')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">Update Score</button>
        <a href="{{ route('admin.scores.index') }}" class="admin-btn-secondary">Cancel</a>
    </div>
</form>
@endsection
