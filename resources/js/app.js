import './bootstrap';
import './column-visibility';
import { initCrossTabPreferenceSync, broadcastAppTheme, broadcastAppLocale } from './preferences-sync';

initCrossTabPreferenceSync();
window.sanghaBroadcastAppTheme = broadcastAppTheme;
window.sanghaBroadcastAppLocale = broadcastAppLocale;

function csrfToken() {
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
}

function bodyDatasetUrl(camelKey) {
    return document.body?.dataset?.[camelKey] || '';
}

document.addEventListener(
    'click',
    (e) => {
        const btn = e.target.closest('.js-app-locale-choice');
        if (!btn) return;
        e.preventDefault();
        const url = bodyDatasetUrl('appLocaleUrl');
        if (!url) return;
        const code = btn.getAttribute('data-locale');
        if (!code) return;
        const body = new URLSearchParams();
        body.set('_token', csrfToken());
        body.set('locale', code);
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: body.toString(),
            credentials: 'same-origin',
        })
            .then(() => {
                if (window.sanghaBroadcastAppLocale) window.sanghaBroadcastAppLocale(code);
                window.location.reload();
            })
            .catch(() => window.location.reload());
    },
    true
);

function initPasswordVisibilityToggles() {
    const passwordInputs = document.querySelectorAll('input[type="password"]');

    passwordInputs.forEach((input) => {
        if (input.dataset.passwordToggleReady === '1') return;
        if (input.closest('[data-password-toggle-ignore="1"]')) return;

        const existingToggle = input.parentElement?.querySelector('.toggle-password');
        if (existingToggle) {
            input.dataset.passwordToggleReady = '1';
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'relative';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        input.classList.add('pr-12');

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'toggle-password absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 rounded transition-colors w-9 h-9 flex items-center justify-center';
        button.setAttribute('aria-label', 'Show password');
        button.setAttribute('tabindex', '-1');

        const eyeIcon = document.createElement('span');
        eyeIcon.className = 'icon-eye absolute inset-0 flex items-center justify-center';
        eyeIcon.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>';

        const eyeOffIcon = document.createElement('span');
        eyeOffIcon.className = 'icon-eye-off absolute inset-0 flex items-center justify-center hidden';
        eyeOffIcon.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>';

        button.appendChild(eyeIcon);
        button.appendChild(eyeOffIcon);
        wrapper.appendChild(button);

        button.addEventListener('click', () => {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            eyeIcon.classList.toggle('hidden', isHidden);
            eyeOffIcon.classList.toggle('hidden', !isHidden);
            button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        });

        input.dataset.passwordToggleReady = '1';
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPasswordVisibilityToggles);
} else {
    initPasswordVisibilityToggles();
}
