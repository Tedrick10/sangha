@extends('admin.layout')

@section('title', 'Add Exam')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.exams.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Exams</a>
    <h1 class="admin-page-title">Add Exam</h1>
</div>

<form action="{{ route('admin.exams.store') }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="admin-input" placeholder="e.g. Pathamabyan Exam 2025">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="exam_date" class="admin-form-label">Exam Date</label>
            <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date') }}" class="admin-input">
            @error('exam_date')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="exam_type_id" class="admin-form-label">Exam Type</label>
            <select name="exam_type_id" id="exam_type_id" class="admin-select-input">
                <option value="">Select exam type (optional)</option>
                @foreach($examTypes as $et)
                    <option value="{{ $et->id }}" {{ old('exam_type_id') == $et->id ? 'selected' : '' }}>{{ $et->name }}</option>
                @endforeach
            </select>
            @error('exam_type_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        @if($subjects->isNotEmpty())
        <div class="admin-form-group">
            <label class="admin-form-label mb-2 block">Subjects</label>
            <div class="space-y-2 max-h-44 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                @foreach($subjects as $s)
                    <label class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-white/80 transition-colors">
                        <input type="checkbox" name="subjects[]" value="{{ $s->id }}" {{ in_array($s->id, old('subjects', [])) ? 'checked' : '' }} class="admin-checkbox">
                        <span class="text-sm text-slate-700">{{ $s->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        @endif
        <div class="admin-form-group">
            <label for="location" class="admin-form-label">Location</label>
            <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="If not using monastery" class="admin-input">
            @error('location')<p class="admin-form-error">{{ $message }}</p>@enderror
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
            <label for="description" class="admin-form-label">Description</label>
            <textarea name="description" id="description" rows="3" class="admin-textarea" placeholder="Brief description of the exam">{{ old('description') }}</textarea>
            @error('description')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="admin-checkbox">
                <span class="text-sm font-medium text-slate-700">Active</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="approved" id="approved" value="1" {{ old('approved') ? 'checked' : '' }} class="admin-checkbox">
                <span class="text-sm font-medium text-slate-700">Approved</span>
            </label>
        </div>
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">Create Exam</button>
        <a href="{{ route('admin.exams.index') }}" class="admin-btn-secondary">Cancel</a>
    </div>
</form>
@endsection
