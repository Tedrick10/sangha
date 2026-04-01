@extends('admin.layout')

@section('title', 'Edit Website Page')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.websites.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Website Content</a>
    <h1 class="admin-page-title">Edit: {{ $website->title }}</h1>
    <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">Update the text and content shown on the website for this page or section.</p>
</div>

<form action="{{ route('admin.websites.update', $website) }}" method="POST" class="admin-form-card">
    @csrf
    @method('PUT')
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="title" class="admin-form-label">Title *</label>
            <input type="text" name="title" id="title" value="{{ old('title', $website->title) }}" required class="admin-input" placeholder="e.g. Home, About Us">
            @error('title')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="slug" class="admin-form-label">Slug (URL path)</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $website->slug) }}" class="admin-input bg-slate-50 dark:bg-slate-700/50" placeholder="e.g. about, contact" title="Used in the page URL. Change with care.">
            @error('slug')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="type" class="admin-form-label">Type</label>
            <input type="text" name="type" id="type" value="{{ old('type', $website->type) }}" class="admin-input" placeholder="page, section, etc.">
            @error('type')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="content" class="admin-form-label">Content</label>
            <textarea name="content" id="content" rows="6" class="admin-textarea" placeholder="Page content (HTML or plain text)">{{ old('content', $website->content) }}</textarea>
            @error('content')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="admin-form-group">
                <label for="sort_order" class="admin-form-label">Sort order</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $website->sort_order) }}" class="admin-input" placeholder="0">
                @error('sort_order')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-3 pt-8">
                <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $website->is_published) ? 'checked' : '' }} class="admin-checkbox">
                <label for="is_published" class="text-sm font-medium text-slate-700 dark:text-slate-300">Published</label>
            </div>
        </div>
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">@include('partials.icon', ['name' => 'check', 'class' => 'w-4 h-4']) Save changes</button>
        <a href="{{ route('admin.websites.index') }}" class="admin-btn-secondary">Cancel</a>
    </div>
</form>
@endsection
