@extends('admin.layout')

@section('title', 'Edit Sangha')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.sanghas.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Sanghas</a>
    <h1 class="admin-page-title">Edit Sangha</h1>
</div>

<form action="{{ route('admin.sanghas.update', $sangha) }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    @method('PUT')
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="monastery_id" class="admin-form-label">Monastery *</label>
            <select name="monastery_id" id="monastery_id" required class="admin-select-input">
                @foreach($monasteries as $m)
                    <option value="{{ $m->id }}" {{ old('monastery_id', $sangha->monastery_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
            @error('monastery_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $sangha->name) }}" required class="admin-input" placeholder="e.g. U Sitagu">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="username" class="admin-form-label">Username *</label>
            <input type="text" name="username" id="username" value="{{ old('username', $sangha->username) }}" required class="admin-input" placeholder="e.g. sangha_username">
            @error('username')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="admin-form-group">
                <label for="password" class="admin-form-label">Password</label>
                <input type="password" name="password" id="password" class="admin-input" placeholder="Leave blank to keep current">
                @error('password')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
            <div class="admin-form-group">
                <label for="password_confirmation" class="admin-form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="admin-input" placeholder="Confirm new password">
            </div>
        </div>
        <p class="text-sm text-slate-500 -mt-2">Leave password blank to keep the current one.</p>
        <div class="admin-form-group">
            <label for="exam_id" class="admin-form-label">Exam</label>
            <select name="exam_id" id="exam_id" class="admin-select-input">
                <option value="">Select exam (optional)</option>
                @foreach($exams as $exam)
                    <option value="{{ $exam->id }}" {{ old('exam_id', $sangha->exam_id) == $exam->id ? 'selected' : '' }}>{{ $exam->name }}{{ $exam->exam_date ? ' (' . $exam->exam_date->format('M d, Y') . ')' : '' }}</option>
                @endforeach
            </select>
            @error('exam_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        @if($customFields->isNotEmpty())
            <div class="admin-form-section">
                <h3 class="admin-form-section-title">Custom Fields</h3>
                <div class="space-y-5">
                    @include('admin.partials.custom-fields-form', ['customFields' => $customFields, 'values' => $customFieldValues])
                </div>
            </div>
        @endif
        <div class="admin-form-group">
            <label for="description" class="admin-form-label">Description</label>
            <textarea name="description" id="description" rows="3" class="admin-textarea" placeholder="Brief description of the sangha">{{ old('description', $sangha->description) }}</textarea>
            @error('description')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $sangha->is_active) ? 'checked' : '' }} class="admin-checkbox">
                <span class="text-sm font-medium text-slate-700">Active</span>
            </label>
        </div>
        <div class="admin-form-group">
            <label for="moderation_status" class="admin-form-label">Moderation Status</label>
            @php
                $moderationStatus = old('moderation_status', $sangha->moderationStatus());
            @endphp
            <select name="moderation_status" id="moderation_status" class="admin-select-input">
                <option value="pending" {{ $moderationStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $moderationStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $moderationStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            @error('moderation_status')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group" id="rejection-reason-wrap">
            <label for="rejection_reason" class="admin-form-label">Rejection Reason</label>
            <textarea name="rejection_reason" id="rejection_reason" rows="3" class="admin-textarea" placeholder="Explain why this registration was rejected">{{ old('rejection_reason', $sangha->rejection_reason) }}</textarea>
            @error('rejection_reason')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">Update Sangha</button>
        <a href="{{ route('admin.sanghas.index') }}" class="admin-btn-secondary">Cancel</a>
    </div>
</form>
<script>
(function() {
    var statusEl = document.getElementById('moderation_status');
    var reasonWrap = document.getElementById('rejection-reason-wrap');
    var reasonEl = document.getElementById('rejection_reason');
    if (!statusEl || !reasonWrap || !reasonEl) return;
    function refresh() {
        var rejected = statusEl.value === 'rejected';
        reasonWrap.classList.toggle('hidden', !rejected);
        reasonEl.required = rejected;
    }
    statusEl.addEventListener('change', refresh);
    refresh();
})();
</script>
@endsection
