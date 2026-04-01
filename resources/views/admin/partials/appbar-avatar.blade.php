{{-- Avatar dropdown for profile and logout --}}
@php $user = auth()->user(); @endphp
<div class="relative inline-block" id="admin-avatar-dropdown">
    <button type="button" id="admin-avatar-btn" aria-haspopup="true" aria-expanded="false" class="flex items-center gap-2 p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 hover:ring-2 hover:ring-slate-200 dark:hover:ring-slate-600 focus:outline-none focus:ring-2 focus:ring-amber-400/50 transition-all duration-200">
        <span class="w-9 h-9 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-semibold shrink-0">
            @if($user)
                {{ strtoupper(mb_substr($user->name ?? $user->email ?? 'A', 0, 1)) }}
            @else
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            @endif
        </span>
    </button>
    <div id="admin-avatar-menu" class="admin-dropdown-panel w-56 py-2 hidden" role="menu">
        @if($user)
            <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700">
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">{{ $user->name }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $user->email }}</p>
            </div>
        @endif
        <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 active:bg-slate-100 dark:active:bg-slate-600/50 transition-colors rounded-lg mx-1" role="menuitem">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            {{ t('change_info') }}
        </a>
        <form method="POST" action="{{ route('admin.logout') }}" class="block">
            @csrf
            <button type="submit" class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 active:bg-slate-100 dark:active:bg-slate-600/50 transition-colors text-left rounded-lg mx-1" role="menuitem">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                {{ t('exit') }}
            </button>
        </form>
    </div>
</div>
<script>
(function() {
    var btn = document.getElementById('admin-avatar-btn');
    var menu = document.getElementById('admin-avatar-menu');
    if (!btn || !menu) return;
    function toggle() {
        var open = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden', open);
        btn.setAttribute('aria-expanded', open ? 'false' : 'true');
    }
    function close() {
        menu.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
    }
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('admin-language-menu')?.classList.add('hidden');
        document.getElementById('admin-theme-menu')?.classList.add('hidden');
        toggle();
    });
    document.addEventListener('click', function() { close(); });
    menu.addEventListener('click', function(e) { e.stopPropagation(); });
})();
</script>
