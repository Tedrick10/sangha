@extends('admin.layout')

@section('title', 'Add Monastery')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.monasteries.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Monasteries</a>
    <h1 class="admin-page-title">Add Monastery</h1>
</div>

<form action="{{ route('admin.monasteries.store') }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="admin-input" placeholder="e.g. Shwe Dagon Monastery">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="username" class="admin-form-label">Username *</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}" required class="admin-input" placeholder="e.g. monastery_username">
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
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="admin-form-group">
                <label for="region" class="admin-form-label">Region</label>
                <input type="text" name="region" id="region" value="{{ old('region') }}" class="admin-input" placeholder="e.g. Yangon, Mandalay">
                @error('region')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
            <div class="admin-form-group">
                <label for="city" class="admin-form-label">City</label>
                <input type="text" name="city" id="city" value="{{ old('city') }}" class="admin-input" placeholder="e.g. Yangon">
                @error('city')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="admin-form-group">
            <label for="address" class="admin-form-label">Address</label>
            <input type="text" name="address" id="address" value="{{ old('address') }}" class="admin-input" placeholder="e.g. 123 Main Street">
            @error('address')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="phone" class="admin-form-label">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="admin-input" placeholder="e.g. 09-123456789">
            @error('phone')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="description" class="admin-form-label">Description</label>
            <textarea name="description" id="description" rows="3" class="admin-textarea" placeholder="Brief description of the monastery">{{ old('description') }}</textarea>
            @error('description')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        @if($customFields->isNotEmpty())
            <div class="admin-form-section">
                <h3 class="admin-form-section-title">Custom Fields</h3>
                <div class="space-y-5">
                    @include('admin.partials.custom-fields-form', ['customFields' => $customFields, 'values' => []])
                </div>
            </div>
        @endif
        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="admin-checkbox">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Active</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="approved" id="approved" value="1" {{ old('approved') ? 'checked' : '' }} class="admin-checkbox">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Approved</span>
            </label>
        </div>
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary inline-flex items-center gap-2">@include('partials.icon', ['name' => 'check', 'class' => 'w-5 h-5']) Create Monastery</button>
        <a href="{{ route('admin.monasteries.index') }}" class="admin-btn-secondary inline-flex items-center gap-2">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) Cancel</a>
    </div>
</form>
@endsection
