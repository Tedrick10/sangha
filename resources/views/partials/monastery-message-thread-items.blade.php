@php
    $variant = $variant ?? 'portal';
    $isPortal = $variant === 'portal';
    $emptyText = $emptyText ?? ($isPortal
        ? t('no_messages_send_first_request', 'No messages yet. Send your first request.')
        : 'No messages yet.');
@endphp
@forelse($messages as $message)
    @php
        $fromAdmin = $message->sender_type === 'admin';
        if ($isPortal) {
            $rowClass = $fromAdmin ? 'justify-start' : 'justify-end';
            $bubbleClass = $fromAdmin
                ? 'max-w-[90%] rounded-xl px-3 py-2 text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-600'
                : 'max-w-[90%] rounded-xl px-3 py-2 text-sm bg-amber-500/20 text-slate-900 dark:text-slate-100 border border-amber-400/40';
            $labelClass = $fromAdmin ? 'text-slate-500 dark:text-slate-400' : 'text-amber-700 dark:text-amber-300';
            $label = $fromAdmin ? ($message->user->name ?? t('super_admin', 'Super Admin')) : t('you', 'You');
        } else {
            $rowClass = $fromAdmin ? 'justify-end' : 'justify-start';
            $bubbleClass = $fromAdmin
                ? 'max-w-[85%] rounded-2xl px-4 py-3 bg-amber-500/20 text-slate-900 dark:text-slate-100 border border-amber-400/30'
                : 'max-w-[85%] rounded-2xl px-4 py-3 bg-slate-100 dark:bg-slate-700/60 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-600';
            $labelClass = $fromAdmin ? 'text-amber-700 dark:text-amber-300' : 'text-slate-500 dark:text-slate-400';
            $label = $fromAdmin ? ($message->user->name ?? 'Admin') : ($monastery->name . ' (Monastery)');
        }
    @endphp
    <div class="flex {{ $rowClass }}">
        <div class="{{ $bubbleClass }}">
            <p class="text-[11px] font-semibold mb-1 {{ $labelClass }}">{{ $label }}</p>
            @if(! empty($message->payload_json) && is_array($message->payload_json))
                <div class="mb-2 rounded-lg border border-slate-200/80 dark:border-slate-600/70 {{ $isPortal ? 'bg-white/70 dark:bg-slate-900/40' : 'bg-white/60 dark:bg-slate-900/40' }} px-2.5 py-2 space-y-1">
                    @foreach($message->payload_json as $item)
                        <p class="text-xs text-slate-600 dark:text-slate-300">
                            <span class="font-semibold">{{ $item['label'] ?? t('field', 'Field') }}:</span>
                            <span>
                                @if(is_array($item['value'] ?? null))
                                    {{ implode(', ', $item['value']) }}
                                @else
                                    {{ $item['value'] ?? t('empty_dash', '—') }}
                                @endif
                            </span>
                        </p>
                    @endforeach
                </div>
            @endif
            <p class="{{ $isPortal ? '' : 'text-sm' }} whitespace-pre-wrap break-words">{{ $message->message }}</p>
            <p class="text-[10px] mt-1 text-slate-500 dark:text-slate-400">{{ $message->created_at?->format($isPortal ? 'M d, H:i' : 'M d, Y H:i') }}</p>
        </div>
    </div>
@empty
    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $emptyText }}</p>
@endforelse
