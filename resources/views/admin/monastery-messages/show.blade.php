@extends('admin.layout')

@section('title', 'Monastery Request Thread')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.monastery-requests.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Monastery Requests</a>
    <h1 class="admin-page-title">{{ $monastery->name }} — Request Thread</h1>
</div>

@php
    $threadRevision = $messages->isEmpty()
        ? '0-0-0'
        : (($messages->max('id') ?? 0).'-'.$messages->count().'-'.(optional($messages->last())->updated_at?->timestamp ?? 0));
@endphp
<div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-5 sm:p-6 shadow-sm mb-6">
    <div
        class="max-h-[460px] overflow-y-auto pr-1"
        data-message-thread-poll-url="{{ route('admin.monastery-requests.thread-messages', $monastery) }}"
        data-message-thread-revision="{{ $threadRevision }}"
    >
        <div data-message-thread-list class="space-y-3">
            @include('partials.monastery-message-thread-items', ['messages' => $messages, 'monastery' => $monastery, 'variant' => 'admin'])
        </div>
    </div>
</div>

<form action="{{ route('admin.monastery-requests.reply', $monastery) }}" method="POST" class="admin-form-card">
    @csrf
    <div class="mb-3 flex flex-wrap gap-2">
        <button type="button" class="admin-btn-secondary text-xs py-1.5 px-3" data-reply-template="Your request is received. We will review and update soon.">Use: Request received</button>
        <button type="button" class="admin-btn-secondary text-xs py-1.5 px-3" data-reply-template="Please update the required fields and submit again.">Use: Need more information</button>
        <button type="button" class="admin-btn-secondary text-xs py-1.5 px-3" data-reply-template="Approved. You can proceed with the next step.">Use: Approved</button>
    </div>
    <div class="admin-form-group">
        <label for="message" class="admin-form-label">Reply Message *</label>
        <textarea name="message" id="message" rows="5" required class="admin-textarea" placeholder="Reply to this monastery...">{{ old('message') }}</textarea>
        @error('message')<p class="admin-form-error">{{ $message }}</p>@enderror
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">Send Reply</button>
    </div>
</form>
<script>
(function() {
    var textarea = document.getElementById('message');
    if (!textarea) return;
    document.querySelectorAll('[data-reply-template]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            textarea.value = btn.getAttribute('data-reply-template') || '';
            textarea.focus();
        });
    });
})();
</script>
@endsection

