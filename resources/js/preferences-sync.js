const LS_THEME = 'sangha_sync_theme_v1';
const LS_LOCALE = 'sangha_sync_locale_v1';

function applySyncedTheme(theme) {
    if (!theme || !['light', 'dark', 'system'].includes(theme)) {
        return;
    }
    document.documentElement.setAttribute('data-theme', theme);
    if (typeof window.sanghaSetWebsiteTheme === 'function') {
        window.sanghaSetWebsiteTheme(theme);
        return;
    }
    if (typeof window.sanghaSetAppTheme === 'function') {
        window.sanghaSetAppTheme(theme);
        return;
    }
    const dark =
        theme === 'dark' ||
        (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.toggle('dark', dark);
}

export function broadcastAppTheme(theme) {
    try {
        localStorage.setItem(LS_THEME, theme);
    } catch (e) {
        /* ignore */
    }
}

export function broadcastAppLocale(code) {
    try {
        localStorage.setItem(LS_LOCALE, code);
    } catch (e) {
        /* ignore */
    }
}

export function initCrossTabPreferenceSync() {
    window.addEventListener('storage', (e) => {
        if (e.key === LS_THEME && e.newValue) {
            applySyncedTheme(e.newValue);
            document.dispatchEvent(new CustomEvent('sangha:app-theme-changed', { detail: { theme: e.newValue } }));
        }
        if (e.key === LS_LOCALE && e.newValue) {
            window.location.reload();
        }
    });
}
