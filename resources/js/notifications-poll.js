const POLL_MS = 12000;

function buildNotificationItem(item) {
    const a = document.createElement('a');
    a.href = item.go_url;
    a.className = [
        'block border-b border-slate-100 px-3 py-2.5 text-left transition-colors last:border-b-0 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/80',
        item.read ? '' : 'bg-amber-50/80 dark:bg-amber-950/20',
    ]
        .filter(Boolean)
        .join(' ');

    const pTitle = document.createElement('p');
    pTitle.className = 'text-sm font-semibold text-slate-900 dark:text-slate-100';
    pTitle.textContent = item.title ?? '';

    const pBody = document.createElement('p');
    pBody.className = 'mt-0.5 line-clamp-2 text-xs text-slate-600 dark:text-slate-400';
    pBody.textContent = item.body ?? '';

    const pTime = document.createElement('p');
    pTime.className = 'mt-1 text-[10px] text-slate-400 dark:text-slate-500';
    pTime.textContent = item.created_human ?? '';

    a.appendChild(pTitle);
    a.appendChild(pBody);
    a.appendChild(pTime);
    return a;
}

function applyNotificationPayload(root, data) {
    const badge = root.querySelector('.js-notif-badge');
    const list = root.querySelector('.js-notif-list');
    const markWrap = root.querySelector('.js-notif-markall-wrap');
    const emptyText = root.dataset.emptyText || '';

    if (badge) {
        const n = Number(data.unread_count) || 0;
        if (n > 0) {
            badge.textContent = n > 99 ? '99+' : String(n);
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    if (markWrap) {
        markWrap.classList.toggle('hidden', (Number(data.unread_count) || 0) === 0);
    }

    if (!list) return;

    list.replaceChildren();
    const items = Array.isArray(data.items) ? data.items : [];
    if (items.length === 0) {
        const p = document.createElement('p');
        p.className = 'js-notif-empty px-3 py-8 text-center text-sm text-slate-500 dark:text-slate-400';
        p.textContent = emptyText;
        list.appendChild(p);
        return;
    }
    items.forEach((item) => list.appendChild(buildNotificationItem(item)));
}

function initNotificationsBellRoot(root) {
    const url = root.dataset.jsonUrl;
    if (!url) return;

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
            applyNotificationPayload(root, data);
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
    setTimeout(refresh, 800);
    setInterval(refresh, POLL_MS);
}

export function initNotificationsPoll() {
    document.querySelectorAll('.js-notifications-bell-root').forEach(initNotificationsBellRoot);
}
