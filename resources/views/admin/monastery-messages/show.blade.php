@extends('admin.layout')

@section('title', 'Monastery Request Thread')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.monastery-requests.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Monastery Requests</a>
    <h1 class="admin-page-title">{{ $monastery->name }} — Request Thread</h1>
</div>

<div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-5 sm:p-6 shadow-sm mb-6">
    <div class="space-y-3 max-h-[460px] overflow-y-auto pr-1">
        @forelse($messages as $message)
            <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[85%] rounded-2xl px-4 py-3 {{ $message->sender_type === 'admin' ? 'bg-amber-500/20 text-slate-900 dark:text-slate-100 border border-amber-400/30' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-600' }}">
                    <p class="text-xs font-semibold mb-1 {{ $message->sender_type === 'admin' ? 'text-amber-700 dark:text-amber-300' : 'text-slate-500 dark:text-slate-400' }}">
                        {{ $message->sender_type === 'admin' ? ($message->user->name ?? 'Admin') : ($monastery->name . ' (Monastery)') }}
                    </p>
                    @if(!empty($message->payload_json) && is_array($message->payload_json))
                        <div class="mb-2 rounded-lg border border-slate-200/80 dark:border-slate-600/70 bg-white/60 dark:bg-slate-900/40 px-2.5 py-2 space-y-1">
                            @foreach($message->payload_json as $item)
                                <p class="text-xs text-slate-600 dark:text-slate-300">
                                    <span class="font-semibold">{{ $item['label'] ?? 'Field' }}:</span>
                                    <span>
                                        @if(is_array($item['value'] ?? null))
                                            {{ implode(', ', $item['value']) }}
                                        @else
                                            {{ $item['value'] ?? '—' }}
                                        @endif
                                    </span>
                                </p>
                            @endforeach
                        </div>
                    @endif
                    <p class="text-sm whitespace-pre-wrap break-words">{{ $message->message }}</p>
                    <p class="text-[11px] mt-2 text-slate-500 dark:text-slate-400">{{ $message->created_at?->format('M d, Y H:i') }}</p>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500 dark:text-slate-400">No messages yet.</p>
        @endforelse
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

