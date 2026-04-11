@extends('admin.layout')

@section('title', 'Edit Monastery')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.monasteries.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Monasteries</a>
    <h1 class="admin-page-title">Edit Monastery</h1>
</div>

<form action="{{ route('admin.monasteries.update', $monastery) }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    @method('PUT')
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $monastery->name) }}" required class="admin-input" placeholder="e.g. Shwe Dagon Monastery">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="username" class="admin-form-label">Username *</label>
            <input type="text" name="username" id="username" value="{{ old('username', $monastery->username) }}" required class="admin-input" placeholder="e.g. monastery_username">
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
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="admin-form-group">
                <label for="region" class="admin-form-label">Region</label>
                <input type="text" name="region" id="region" value="{{ old('region', $monastery->region) }}" class="admin-input" placeholder="e.g. Yangon, Mandalay">
                @error('region')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
            <div class="admin-form-group">
                <label for="city" class="admin-form-label">City</label>
                <input type="text" name="city" id="city" value="{{ old('city', $monastery->city) }}" class="admin-input" placeholder="e.g. Yangon">
                @error('city')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="admin-form-group">
            <label for="address" class="admin-form-label">Address</label>
            <input type="text" name="address" id="address" value="{{ old('address', $monastery->address) }}" class="admin-input" placeholder="e.g. 123 Main Street">
            @error('address')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="phone" class="admin-form-label">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $monastery->phone) }}" class="admin-input" placeholder="e.g. 09-123456789">
            @error('phone')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="description" class="admin-form-label">Description</label>
            <textarea name="description" id="description" rows="3" class="admin-textarea" placeholder="Brief description of the monastery">{{ old('description', $monastery->description) }}</textarea>
            @error('description')<p class="admin-form-error">{{ $message }}</p>@enderror
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
            <label for="moderation_status" class="admin-form-label">Moderation Status</label>
            @php
                $moderationStatus = old('moderation_status', $monastery->moderationStatus());
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
            <textarea name="rejection_reason" id="rejection_reason" rows="3" class="admin-textarea" placeholder="Explain why this registration was rejected">{{ old('rejection_reason', $monastery->rejection_reason) }}</textarea>
            @error('rejection_reason')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="admin-form-actions flex flex-wrap items-center gap-2">
        <button type="submit" class="admin-btn-primary inline-flex items-center gap-2">@include('partials.icon', ['name' => 'check', 'class' => 'w-5 h-5']) Update Monastery</button>
        <a href="{{ route('admin.monasteries.chat', $monastery) }}" class="admin-btn-secondary inline-flex items-center gap-2">@include('partials.icon', ['name' => 'view', 'class' => 'w-4 h-4']) {{ t('monastery_chat_open', 'Open chat') }}</a>
        <a href="{{ route('admin.monasteries.index') }}" class="admin-btn-secondary inline-flex items-center gap-2">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) Cancel</a>
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
