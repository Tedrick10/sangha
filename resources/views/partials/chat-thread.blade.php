{{--
  Real-time chat UI (polling). Expects:
  - $messages: iterable of MonasteryMessage (with user + monastery loaded where needed)
  - $fetchUrl, $sendUrl: string
  - $isAdminViewer: bool
  - $compactViewport (optional bool): monastery portal + fixed bottom nav — tighter max-height
--}}
@php
    $lastId = $messages->isNotEmpty() ? (int) $messages->last()->id : 0;
    $youLabel = t('chat_you', 'You');
    $compactViewport = !empty($compactViewport);
    $sectionMaxHClass = $compactViewport
        ? 'max-h-[min(32rem,calc(100svh-15rem))]'
        : 'max-h-[min(42rem,calc(100dvh-11rem))]';
@endphp
<div class="w-full max-w-3xl mx-auto" id="realtime-chat-wrap">
<section
    class="flex {{ $sectionMaxHClass }} flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-amber-500/[0.06] dark:border-slate-800/90 dark:bg-slate-900 dark:ring-amber-400/[0.06]"
    id="realtime-chat-root"
    data-fetch-url="{{ $fetchUrl }}"
    data-send-url="{{ $sendUrl }}"
    data-is-admin-viewer="{{ $isAdminViewer ? '1' : '0' }}"
    data-last-id="{{ $lastId }}"
>
    <div
        class="js-realtime-chat-scroll min-h-0 flex-1 touch-pan-y space-y-6 overflow-y-scroll overscroll-y-contain bg-amber-50/50 px-3 py-6 sm:px-6 sm:py-8 dark:bg-slate-950 [scrollbar-gutter:stable] [scrollbar-width:thin] [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-amber-200/90 dark:[&::-webkit-scrollbar-thumb]:bg-amber-800/40"
        role="log"
        aria-live="polite"
        aria-relevant="additions"
    >
        @foreach($messages as $m)
            @php
                $mine = $isAdminViewer ? ($m->sender_type === \App\Models\MonasteryMessage::SENDER_ADMIN) : ($m->sender_type === \App\Models\MonasteryMessage::SENDER_MONASTERY);
                $pl = $m->toChatPayload($m->relationLoaded('monastery') ? $m->monastery?->name : null);
                $bubbleMine = 'rounded-2xl rounded-tr-md border border-amber-600/40 bg-amber-600 px-4 py-3 text-[15px] leading-relaxed text-white shadow-sm dark:border-amber-400/30 dark:bg-amber-600 [&_p]:text-white';
                $bubbleTheirs = 'rounded-2xl rounded-tl-md border border-amber-100/90 bg-white px-4 py-3 text-[15px] leading-relaxed text-slate-800 shadow-sm dark:border-amber-700/45 dark:bg-slate-800 dark:text-slate-100 [&_p]:text-slate-800 dark:[&_p]:text-slate-100';
            @endphp
            @if($mine)
                <div class="flex w-full justify-end" data-message-id="{{ $m->id }}">
                    <div class="min-w-0 max-w-full flex flex-col items-end pb-0.5">
                        <p class="mb-1.5 max-w-full text-right text-xs leading-none">
                            <span class="font-medium text-slate-700 dark:text-slate-200">{{ $youLabel }}</span>
                            <span class="text-slate-400 dark:text-slate-500"> · {{ $m->created_at?->format('M j, Y · H:i') }}</span>
                        </p>
                        <div class="{{ $bubbleMine }} max-w-[min(100%,28rem)] break-words">
                            <p class="whitespace-pre-wrap">{{ $m->message }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex w-full justify-start" data-message-id="{{ $m->id }}">
                    <div class="min-w-0 max-w-full flex flex-col items-start pb-0.5">
                        <p class="mb-1.5 max-w-full text-xs leading-none">
                            <span class="font-medium text-slate-700 dark:text-slate-200">{{ $pl['sender_label'] }}</span>
                            <span class="text-slate-400 dark:text-slate-500"> · {{ $m->created_at?->format('M j, Y · H:i') }}</span>
                        </p>
                        <div class="{{ $bubbleTheirs }} inline-block max-w-[min(100%,28rem)] break-words">
                            <p class="whitespace-pre-wrap">{{ $m->message }}</p>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <div class="relative z-10 shrink-0 border-t border-amber-100/80 bg-white p-2.5 sm:p-4 dark:border-slate-800 dark:bg-slate-900">
        <form class="js-realtime-chat-form flex flex-col gap-2">
            @csrf
            <label class="sr-only" for="realtime-chat-input">{{ t('chat_message_label', 'Message') }}</label>
            <div class="flex flex-col gap-1.5 rounded-2xl border border-amber-200/60 bg-amber-50/40 p-1.5 shadow-inner transition-colors focus-within:border-amber-400/70 focus-within:ring-2 focus-within:ring-amber-400/20 dark:border-slate-700 dark:bg-slate-950 dark:focus-within:border-amber-600/55 dark:focus-within:ring-amber-500/15 sm:flex-row sm:items-center sm:gap-2 sm:p-2">
                <textarea
                    id="realtime-chat-input"
                    name="message"
                    rows="2"
                    required
                    maxlength="10000"
                    class="js-realtime-chat-input max-h-36 min-h-[2.5rem] w-full flex-1 resize-y overflow-y-auto overscroll-y-contain rounded-lg border-0 bg-transparent px-2.5 py-1.5 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-0 dark:text-slate-100 dark:placeholder:text-slate-500 sm:min-h-[2.25rem]"
                    placeholder="{{ t('chat_type_message', 'Type a message…') }}"
                ></textarea>
                <button type="submit" class="inline-flex h-8 w-full shrink-0 items-center justify-center rounded-md bg-amber-600 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-amber-500 active:scale-[0.99] dark:bg-amber-600 dark:hover:bg-amber-500 sm:h-8 sm:w-auto sm:min-w-[4.25rem] sm:px-3 sm:text-sm">
                    {{ t('chat_send', 'Send') }}
                </button>
            </div>
        </form>
        <p class="js-realtime-chat-error mt-2 hidden text-xs text-red-600 dark:text-red-400" role="alert"></p>
    </div>
</section>
</div>

@push('scripts')
<script>
(function () {
    var MSG_SEND_FAIL = @json(t('chat_send_failed', 'Could not send. Please try again.'));
    var YOU_LABEL = @json(t('chat_you', 'You'));

    var CLS_BUBBLE_MINE =
        'rounded-2xl rounded-tr-md border border-amber-600/40 bg-amber-600 px-4 py-3 text-[15px] leading-relaxed text-white shadow-sm dark:border-amber-400/30 dark:bg-amber-600 max-w-[min(100%,28rem)] break-words [&_p]:text-white';
    var CLS_BUBBLE_THEIRS =
        'rounded-2xl rounded-tl-md border border-amber-100/90 bg-white px-4 py-3 text-[15px] leading-relaxed text-slate-800 shadow-sm dark:border-amber-700/45 dark:bg-slate-800 dark:text-slate-100 [&_p]:text-slate-800 dark:[&_p]:text-slate-100 inline-block max-w-[min(100%,28rem)] break-words';

    function csrfToken() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    function appendMessage(scroll, row, isAdminViewer) {
        var mine = isAdminViewer ? row.sender_type === 'admin' : row.sender_type === 'monastery';
        var wrap = document.createElement('div');
        wrap.className = mine ? 'flex w-full justify-end' : 'flex w-full justify-start';
        wrap.setAttribute('data-message-id', String(row.id));

        var col = document.createElement('div');
        col.className = mine ? 'min-w-0 max-w-full flex flex-col items-end pb-0.5' : 'min-w-0 max-w-full flex flex-col items-start pb-0.5';

        var meta = document.createElement('p');
        meta.className = mine ? 'mb-1.5 max-w-full text-right text-xs leading-none' : 'mb-1.5 max-w-full text-xs leading-none';
        var nameSpan = document.createElement('span');
        nameSpan.className = 'font-medium text-slate-700 dark:text-slate-200';
        nameSpan.textContent = mine ? YOU_LABEL : row.sender_label || '';
        var timeSpan = document.createElement('span');
        timeSpan.className = 'text-slate-400 dark:text-slate-500';
        timeSpan.textContent = ' · ' + (row.created_human || '');
        meta.appendChild(nameSpan);
        meta.appendChild(timeSpan);

        var bubble = document.createElement('div');
        bubble.className = mine ? CLS_BUBBLE_MINE : CLS_BUBBLE_THEIRS;
        var body = document.createElement('p');
        body.className = 'whitespace-pre-wrap';
        body.textContent = row.message || '';
        bubble.appendChild(body);

        col.appendChild(meta);
        col.appendChild(bubble);
        wrap.appendChild(col);
        scroll.appendChild(wrap);
        scroll.scrollTop = scroll.scrollHeight;
    }

    function bumpNotifications() {
        try {
            window.dispatchEvent(new CustomEvent('sangha-notifications-refresh'));
        } catch (e) {}
    }

    function initRealtimeChat(root) {
        if (!root || root.dataset.chatInit === '1') return;
        root.dataset.chatInit = '1';
        var fetchUrl = root.getAttribute('data-fetch-url');
        var sendUrl = root.getAttribute('data-send-url');
        var isAdminViewer = root.getAttribute('data-is-admin-viewer') === '1';
        var scroll = root.querySelector('.js-realtime-chat-scroll');
        var form = root.querySelector('.js-realtime-chat-form');
        var input = root.querySelector('.js-realtime-chat-input');
        var errEl = root.querySelector('.js-realtime-chat-error');
        if (!fetchUrl || !sendUrl || !scroll || !form || !input) return;

        var lastId = parseInt(root.getAttribute('data-last-id') || '0', 10) || 0;
        var pollMs = 3500;
        var sending = false;

        function setError(msg) {
            if (!errEl) return;
            if (msg) {
                errEl.textContent = msg;
                errEl.classList.remove('hidden');
            } else {
                errEl.textContent = '';
                errEl.classList.add('hidden');
            }
        }

        function poll() {
            var sep = fetchUrl.indexOf('?') >= 0 ? '&' : '?';
            fetch(fetchUrl + sep + 'since_id=' + encodeURIComponent(String(lastId)), {
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(function (r) {
                    if (!r.ok) throw new Error('poll');
                    return r.json();
                })
                .then(function (data) {
                    var list = (data && data.messages) || [];
                    var hadNew = false;
                    list.forEach(function (row) {
                        if (!row || !row.id) return;
                        if (scroll.querySelector('[data-message-id="' + row.id + '"]')) return;
                        appendMessage(scroll, row, isAdminViewer);
                        lastId = Math.max(lastId, row.id);
                        hadNew = true;
                    });
                    if (hadNew) bumpNotifications();
                })
                .catch(function () {});
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var text = (input.value || '').trim();
            if (!text || sending) return;
            sending = true;
            setError('');
            fetch(sendUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({ message: text }),
            })
                .then(function (r) {
                    if (!r.ok) throw new Error('send');
                    return r.json();
                })
                .then(function (data) {
                    if (data && data.message) {
                        var row = data.message;
                        if (!scroll.querySelector('[data-message-id="' + row.id + '"]')) {
                            appendMessage(scroll, row, isAdminViewer);
                            lastId = Math.max(lastId, row.id);
                        }
                        input.value = '';
                        bumpNotifications();
                    }
                })
                .catch(function () {
                    setError(MSG_SEND_FAIL);
                })
                .finally(function () {
                    sending = false;
                });
        });

        setInterval(poll, pollMs);
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') poll();
        });
        setTimeout(poll, 600);
        setTimeout(function () {
            scroll.scrollTop = scroll.scrollHeight;
        }, 0);
    }

    document.querySelectorAll('#realtime-chat-root').forEach(initRealtimeChat);
})();
</script>
@endpush
