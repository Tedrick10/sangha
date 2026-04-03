@php
    $theme = resolved_app_theme();
    $themeLabels = ['light' => t('theme_light'), 'dark' => t('theme_dark'), 'system' => t('theme_system')];
    $currentLabel = $themeLabels[$theme] ?? t('theme_system');
@endphp
<div class="relative inline-block" id="admin-theme-dropdown">
    <button type="button" id="admin-theme-btn" aria-haspopup="true" aria-expanded="false" class="admin-dropdown-trigger whitespace-nowrap">
        <span id="admin-theme-icon-slot" class="shrink-0 inline-flex" aria-hidden="true">
            @if($theme === 'light')
                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            @elseif($theme === 'dark')
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
            @else
                <svg class="w-4 h-4 text-slate-600 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            @endif
        </span>
        <span id="admin-theme-label">{{ $currentLabel }}</span>
        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
    </button>
    <div id="admin-theme-menu" class="admin-dropdown-panel w-44 hidden" role="menu">
        @foreach(['light', 'dark', 'system'] as $opt)
            <button type="button" role="menuitem" data-admin-theme="{{ $opt }}" class="admin-theme-choice flex w-full items-center justify-between gap-2 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-colors text-left {{ $theme === $opt ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400' : '' }}">
                <span class="flex items-center gap-2">
                    @if($opt === 'light')
                        <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    @elseif($opt === 'dark')
                        <svg class="w-4 h-4 shrink-0 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    @else
                        <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    @endif
                    <span class="admin-theme-choice-label {{ $theme === $opt ? 'font-medium' : '' }}">{{ $themeLabels[$opt] }}</span>
                </span>
                <svg class="admin-theme-check h-4 w-4 shrink-0 text-amber-500 {{ $theme === $opt ? '' : 'hidden' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
            </button>
        @endforeach
    </div>
</div>
<script>
(function() {
    var btn = document.getElementById('admin-theme-btn');
    var menu = document.getElementById('admin-theme-menu');
    if (!btn || !menu) return;

    var themeUrl = @json(route('app.set-theme'));
    var labels = @json($themeLabels);
    var icons = {
        light: '<svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
        dark: '<svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>',
        system: '<svg class="w-4 h-4 text-slate-600 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>'
    };

    function csrfToken() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    function syncAdminThemeUi(theme) {
        var labelEl = document.getElementById('admin-theme-label');
        var iconSlot = document.getElementById('admin-theme-icon-slot');
        if (labelEl && labels[theme]) labelEl.textContent = labels[theme];
        if (iconSlot && icons[theme]) iconSlot.innerHTML = icons[theme];
        menu.querySelectorAll('.admin-theme-choice').forEach(function (el) {
            var t = el.getAttribute('data-admin-theme');
            var active = t === theme;
            el.classList.toggle('bg-amber-50', active);
            el.classList.toggle('dark:bg-amber-900/20', active);
            el.classList.toggle('text-amber-700', active);
            el.classList.toggle('dark:text-amber-400', active);
            var chk = el.querySelector('.admin-theme-check');
            if (chk) chk.classList.toggle('hidden', !active);
            var lbl = el.querySelector('.admin-theme-choice-label');
            if (lbl) lbl.classList.toggle('font-medium', active);
        });
    }

    function persistTheme(theme) {
        var token = csrfToken();
        var body = new URLSearchParams();
        body.set('_token', token);
        body.set('theme', theme);
        fetch(themeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
            },
            body: body.toString(),
            credentials: 'same-origin'
        })
            .then(function () {
                if (window.sanghaBroadcastAppTheme) window.sanghaBroadcastAppTheme(theme);
            })
            .catch(function () {});
    }

    function applyChoice(theme) {
        if (typeof window.sanghaSetAppTheme === 'function') window.sanghaSetAppTheme(theme);
        syncAdminThemeUi(theme);
        persistTheme(theme);
    }

    menu.querySelectorAll('[data-admin-theme]').forEach(function (opt) {
        opt.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var theme = opt.getAttribute('data-admin-theme');
            applyChoice(theme);
            menu.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        });
    });

    document.addEventListener('sangha:app-theme-changed', function (e) {
        var theme = e.detail && e.detail.theme;
        if (theme) syncAdminThemeUi(theme);
    });

    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        var open = !menu.classList.contains('hidden');
        var other = document.getElementById('admin-language-menu');
        if (other) other.classList.add('hidden');
        other = document.getElementById('admin-avatar-menu');
        if (other) other.classList.add('hidden');
        menu.classList.toggle('hidden', open);
        btn.setAttribute('aria-expanded', open ? 'false' : 'true');
    });
    document.addEventListener('click', function() {
        menu.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
    });
    menu.addEventListener('click', function(e) { e.stopPropagation(); });
})();
</script>
