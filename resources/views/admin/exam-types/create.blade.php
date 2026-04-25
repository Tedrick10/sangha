@extends('admin.layout')

@section('title', 'Add Exam Type')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.exam-types.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Exam Types</a>
    <h1 class="admin-page-title">Add Exam Type</h1>
</div>

<form action="{{ route('admin.exam-types.store') }}" method="POST" enctype="multipart/form-data" class="admin-form-card" data-admin-submit-once>
    @csrf
    <div class="space-y-5">
        @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('exam_type', 'name'))
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="admin-input" placeholder="e.g. Pathamabyan, Dhammacariya">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        @endif
        @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('exam_type', 'description'))
        <div class="admin-form-group">
            <label for="description" class="admin-form-label">Description</label>
            <textarea name="description" id="description" rows="3" class="admin-textarea" placeholder="Brief description of the exam type">{{ old('description') }}</textarea>
            @error('description')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        @endif
        @if($customFields->isNotEmpty())
            <div class="admin-form-section">
                <h3 class="admin-form-section-title">Custom Fields</h3>
                <div class="space-y-5">
                    @include('admin.partials.custom-fields-form', ['customFields' => $customFields, 'values' => []])
                </div>
            </div>
        @endif
        @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('exam_type', 'is_active'))
        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="admin-checkbox">
                <span class="text-sm font-medium text-slate-700">Active</span>
            </label>
        </div>
        @endif
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">Create Exam Type</button>
        <a href="{{ route('admin.exam-types.index') }}" class="admin-btn-secondary">Cancel</a>
    </div>
</form>
@endsection
