@extends('admin.layout')

@section('title', 'Add Sangha')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.sanghas.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Sanghas</a>
    <h1 class="admin-page-title">Add Sangha</h1>
</div>

<form action="{{ route('admin.sanghas.store') }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="monastery_id" class="admin-form-label">Monastery *</label>
            <select name="monastery_id" id="monastery_id" required class="admin-select-input">
                <option value="">Select monastery</option>
                @foreach($monasteries as $m)
                    <option value="{{ $m->id }}" {{ old('monastery_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
            @error('monastery_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="admin-input" placeholder="e.g. U Sitagu">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="username" class="admin-form-label">Username *</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}" required class="admin-input" placeholder="e.g. sangha_username">
            @error('username')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="admin-form-group">
                <label for="password" class="admin-form-label">Password *</label>
                <input type="password" name="password" id="password" required class="admin-input" placeholder="Enter password">
                @error('password')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
            <div class="admin-form-group">
                <label for="password_confirmation" class="admin-form-label">Confirm Password *</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required class="admin-input" placeholder="Confirm password">
            </div>
        </div>
        <div class="admin-form-group">
            <label for="exam_id" class="admin-form-label">Exam</label>
            <select name="exam_id" id="exam_id" class="admin-select-input">
                <option value="">Select exam (optional)</option>
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
            <label for="description" class="admin-form-label">Description</label>
            <textarea name="description" id="description" rows="3" class="admin-textarea" placeholder="Brief description of the sangha">{{ old('description') }}</textarea>
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
        <button type="submit" class="admin-btn-primary">Create Sangha</button>
        <a href="{{ route('admin.sanghas.index') }}" class="admin-btn-secondary">Cancel</a>
    </div>
</form>
@endsection
