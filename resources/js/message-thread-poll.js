const THREAD_POLL_MS = 10000;

function initMessageThreadRoot(root) {
    const url = root.dataset.messageThreadPollUrl;
    const list = root.querySelector('[data-message-thread-list]');
    if (!url || !list) return;

    let lastRevision = root.dataset.messageThreadRevision || '';
    let inFlight = false;

    async function refresh() {
        if (inFlight) return;
        inFlight = true;
        try {
            const res = await fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!res.ok) return;
            const data = await res.json();
            if (!data || typeof data.revision !== 'string' || typeof data.html !== 'string') return;
            if (data.revision === lastRevision) return;

            const atBottom = root.scrollHeight - root.scrollTop - root.clientHeight < 100;

            list.innerHTML = data.html;
            lastRevision = data.revision;
            root.dataset.messageThreadRevision = lastRevision;

            if (atBottom) {
                root.scrollTop = root.scrollHeight;
            }
        } catch {
            /* ignore */
        } finally {
            inFlight = false;
        }
    }

    window.addEventListener('sangha-notifications-refresh', refresh);
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') refresh();
    });
    window.addEventListener('focus', refresh);
    setTimeout(refresh, 1000);
    setInterval(refresh, THREAD_POLL_MS);
}

export function initMessageThreadPoll() {
    document.querySelectorAll('[data-message-thread-poll-url]').forEach(initMessageThreadRoot);
}
