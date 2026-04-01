@php
    $theme = session('admin_theme', 'system');
    $themeLabels = ['light' => t('theme_light'), 'dark' => t('theme_dark'), 'system' => t('theme_system')];
    $currentLabel = $themeLabels[$theme] ?? t('theme_system');
@endphp
<div class="relative inline-block" id="admin-theme-dropdown">
    <button type="button" id="admin-theme-btn" aria-haspopup="true" aria-expanded="false" class="admin-dropdown-trigger whitespace-nowrap">
        {{-- Sun icon for light, moon for dark, both for system --}}
        @if($theme === 'light')
            <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        @elseif($theme === 'dark')
            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        @else
            <svg class="w-4 h-4 text-slate-600 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
        @endif
        <span>{{ $currentLabel }}</span>
        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
    </button>
    <div id="admin-theme-menu" class="admin-dropdown-panel w-44 hidden" role="menu">
        <form action="{{ route('admin.set-theme') }}" method="POST">
            @csrf
            <input type="hidden" name="theme" value="light">
            <button type="submit" class="flex items-center justify-between gap-2 w-full px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 active:bg-slate-100 dark:active:bg-slate-600/50 transition-colors text-left {{ $theme === 'light' ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400' : '' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span class="{{ $theme === 'light' ? 'font-medium' : '' }}">{{ t('theme_light') }}</span>
                </span>
                @if($theme === 'light')<svg class="w-4 h-4 text-amber-500 shrink-0 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>@endif
            </button>
        </form>
        <form action="{{ route('admin.set-theme') }}" method="POST">
            @csrf
            <input type="hidden" name="theme" value="dark">
            <button type="submit" class="flex items-center justify-between gap-2 w-full px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 active:bg-slate-100 dark:active:bg-slate-600/50 transition-colors text-left {{ $theme === 'dark' ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400' : '' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    <span class="{{ $theme === 'dark' ? 'font-medium' : '' }}">{{ t('theme_dark') }}</span>
                </span>
                @if($theme === 'dark')<svg class="w-4 h-4 text-amber-500 shrink-0 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>@endif
            </button>
        </form>
        <form action="{{ route('admin.set-theme') }}" method="POST">
            @csrf
            <input type="hidden" name="theme" value="system">
            <button type="submit" class="flex items-center justify-between gap-2 w-full px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 active:bg-slate-100 dark:active:bg-slate-600/50 transition-colors text-left {{ $theme === 'system' ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400' : '' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    <span class="{{ $theme === 'system' ? 'font-medium' : '' }}">{{ t('theme_system') }}</span>
                </span>
                @if($theme === 'system')<svg class="w-4 h-4 text-amber-500 shrink-0 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>@endif
            </button>
        </form>
    </div>
</div>
<script>
(function() {
    var btn = document.getElementById('admin-theme-btn');
    var menu = document.getElementById('admin-theme-menu');
    if (!btn || !menu) return;
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
