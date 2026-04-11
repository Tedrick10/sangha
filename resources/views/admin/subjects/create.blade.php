@extends('admin.layout')

@section('title', 'Add Subject')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.subjects.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Subjects</a>
    <h1 class="admin-page-title">Add Subject</h1>
</div>

<form action="{{ route('admin.subjects.store') }}" method="POST" class="admin-form-card" data-admin-submit-once>
    @csrf
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="admin-input" placeholder="e.g. Abhidhamma, Vinaya">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="description" class="admin-form-label">Description</label>
            <textarea name="description" id="description" rows="3" class="admin-textarea" placeholder="Brief description of the subject">{{ old('description') }}</textarea>
            @error('description')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="moderation_mark" class="admin-form-label">Moderation Mark</label>
            <input type="number" name="moderation_mark" id="moderation_mark" value="{{ old('moderation_mark') }}" step="0.01" min="0" placeholder="e.g. 40" class="admin-input">
            @error('moderation_mark')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="admin-form-group">
                <label for="full_mark" class="admin-form-label">Full Mark</label>
                <input type="number" name="full_mark" id="full_mark" value="{{ old('full_mark') }}" step="0.01" min="0" placeholder="e.g. 100" class="admin-input">
                @error('full_mark')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
            <div class="admin-form-group">
                <label for="pass_mark" class="admin-form-label">Pass Mark</label>
                <input type="number" name="pass_mark" id="pass_mark" value="{{ old('pass_mark') }}" step="0.01" min="0" placeholder="e.g. 40" class="admin-input">
                @error('pass_mark')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="admin-checkbox">
            <label for="is_active" class="text-sm font-medium text-slate-700">Active</label>
        </div>
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">Create Subject</button>
        <a href="{{ route('admin.subjects.index') }}" class="admin-btn-secondary">Cancel</a>
    </div>
</form>
@endsection
