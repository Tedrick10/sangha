@php $adminTheme = resolved_app_theme(); @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $adminTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
    <script>
        try {
            if (localStorage.getItem('admin_sidebar_collapsed') === '1' && window.matchMedia('(min-width: 1024px)').matches) {
                document.documentElement.classList.add('admin-sidebar-collapsed');
            }
        } catch (e) {}
    </script>
    @include('partials.favicon')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <script>
        (function() {
            function applyAdminTheme() {
            var theme = document.documentElement.getAttribute('data-theme') || 'system';
            var dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
            }
            window.sanghaSetAppTheme = function (theme) {
                if (theme !== 'light' && theme !== 'dark' && theme !== 'system') return;
                document.documentElement.setAttribute('data-theme', theme);
                applyAdminTheme();
            };
            applyAdminTheme();
            try {
                var mq = window.matchMedia('(prefers-color-scheme: dark)');
                var onChange = function () {
                    var t = document.documentElement.getAttribute('data-theme') || 'system';
                    if (t === 'system') applyAdminTheme();
                };
                if (mq.addEventListener) mq.addEventListener('change', onChange);
                else if (mq.addListener) mq.addListener(onChange);
            } catch (e) {}
        })();
    </script>
</head>
@php $__ap = appearance_portal_body_attrs('admin'); @endphp
<body class="min-h-screen overflow-x-hidden bg-stone-50 text-stone-900 dark:bg-slate-950 dark:text-slate-200 font-sans antialiased {{ $__ap['class'] }}" @if($__ap['style'] !== '') style="{{ $__ap['style'] }}" @endif data-app-locale-url="{{ route('app.set-locale') }}" data-app-theme-url="{{ route('app.set-theme') }}">
    {{-- Mobile overlay: opacity transition (no display:none) for smooth fade --}}
    <div id="admin-sidebar-overlay" class="admin-sidebar-overlay fixed inset-0 z-30 pointer-events-none opacity-0 lg:hidden" aria-hidden="true"></div>
    {{-- Sidebar is fixed (mobile + desktop) so width animation does not flex-reflow the main column (major jank source). Main uses matching padding-left transition. --}}
    <div class="relative min-h-screen min-w-0">
        <aside id="admin-sidebar" class="admin-sidebar-drawer fixed inset-y-0 left-0 z-40 flex min-h-screen w-64 max-w-[85vw] flex-col border-r border-stone-200/90 bg-gradient-to-b from-stone-50 via-white to-stone-50/95 text-stone-800 shadow-xl shadow-stone-300/20 dark:border-slate-700/80 dark:from-slate-900 dark:via-slate-900 dark:to-slate-950 dark:text-slate-200 dark:shadow-black/40 lg:max-w-none lg:shadow-none">
            <div class="admin-sidebar-top flex min-h-[3.25rem] shrink-0 items-start gap-2 border-b border-stone-200/80 px-3 py-2 dark:border-slate-700/50">
                <button type="button" class="js-admin-sidebar-toggle mt-0.5 hidden h-9 w-9 shrink-0 items-center justify-center self-start rounded-lg text-stone-600 transition-colors hover:bg-stone-100 hover:text-stone-900 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white lg:inline-flex" aria-label="{{ t('toggle_sidebar', 'Toggle sidebar') }}" aria-expanded="true">
                    <svg class="js-as-menu h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    <svg class="js-as-close hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    <svg class="js-as-collapse hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
                </button>
                @php $logo = \App\Models\SiteSetting::imageUrl('logo'); @endphp
                <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-brand-full flex min-w-0 flex-1 items-start gap-2 rounded-md outline-none ring-amber-500/0 focus-visible:ring-2 focus-visible:ring-amber-500/40" title="{{ config('app.name') }}">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ config('app.name') }}" class="h-7 max-w-[7.5rem] rounded-md bg-white object-contain p-1 ring-1 ring-stone-200/90 dark:bg-white/10 dark:ring-white/10">
                    @else
                        @include('partials.app-brand-title', [
                            'outerClass' => 'admin-sidebar-brand-title text-left text-xs font-semibold tracking-tight text-amber-900 dark:text-white sm:text-sm',
                            'lineClass' => 'leading-snug',
                        ])
                    @endif
                </a>
            </div>
            @php $adminUser = auth()->user(); @endphp
            @include('admin.partials.sidebar-nav')
            <div class="mt-auto border-t border-stone-200/80 p-2 dark:border-slate-700/50">
                <form action="{{ route('admin.logout') }}" method="POST" class="block">
                    @csrf
                    <button type="submit" class="admin-sidebar-logout-btn flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-stone-500 transition-colors hover:bg-red-50 hover:text-red-700 dark:text-slate-400 dark:hover:bg-red-500/10 dark:hover:text-red-200">
                        @include('partials.icon', ['name' => 'logout', 'class' => 'h-5 w-5 shrink-0 opacity-90'])
                        <span class="admin-sidebar-logout-label">{{ t('exit') }}</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Header + Main: lg padding tracks sidebar width (see app.css .admin-main) --}}
        {{-- min-h-screen only stretches empty space below short pages; sidebar is already full-height fixed. --}}
        <div class="admin-main flex min-h-0 w-full max-w-full min-w-0 flex-col lg:pl-64">
            {{-- w-full/min-w-0/flex-1 toolbar: nowrap cluster must not widen the column past the viewport (mobile). --}}
            <header class="admin-header relative z-50 flex w-full min-w-0 max-w-full min-h-12 shrink-0 items-center gap-1.5 border-b border-stone-200/90 bg-white/95 px-2.5 backdrop-blur-md shadow-sm shadow-stone-900/5 dark:border-slate-700/80 dark:bg-slate-900/95 dark:shadow-none sm:min-h-14 sm:gap-3 sm:px-6 lg:px-8">
                <button type="button" id="admin-sidebar-toggle" aria-label="{{ t('toggle_sidebar', 'Toggle sidebar') }}" aria-expanded="false" class="js-admin-sidebar-toggle relative -ml-2 inline-flex shrink-0 items-center justify-center rounded-lg p-2 text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-100 lg:hidden">
                    <svg class="js-as-menu h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    <svg class="js-as-close hidden h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    <svg class="js-as-collapse hidden h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
                </button>
                {{-- overflow-x only on text links — absolute dropdowns must not sit inside an overflow clip or menus never show. --}}
                <div class="flex min-w-0 flex-1 items-center justify-end gap-1 sm:gap-2 md:gap-3">
                    <div class="flex min-w-0 flex-1 flex-nowrap items-center justify-end gap-1 overflow-x-auto [-webkit-overflow-scrolling:touch] sm:gap-2 no-scrollbar">
                        <a href="{{ route('admin.dashboard') }}" aria-label="{{ t('dashboard') }}" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-lg px-2 py-2 text-sm font-medium transition-colors sm:gap-2 sm:px-3 {{ request()->routeIs('admin.dashboard') ? 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-700' }}">@include('partials.icon', ['name' => 'home', 'class' => 'w-4 h-4']) <span class="hidden sm:inline">{{ t('dashboard') }}</span></a>
                        <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" aria-label="{{ t('view_site') }}" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-lg px-2 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-slate-100 sm:gap-2 sm:px-3">@include('partials.icon', ['name' => 'external-link', 'class' => 'w-4 h-4']) <span class="hidden sm:inline">{{ t('view_site') }}</span></a>
                    </div>
                    <div class="flex shrink-0 flex-nowrap items-center gap-1 sm:gap-2 md:gap-3">
                        @include('admin.partials.appbar-language')
                        @include('admin.partials.appbar-theme')
                        @if($adminUser)
                            @include('partials.notifications-bell', ['notifiable' => $adminUser, 'goRouteName' => 'admin.notifications.go', 'readAllRouteName' => 'admin.notifications.read-all', 'jsonRouteName' => 'admin.notifications.recent'])
                        @endif
                        @include('admin.partials.appbar-avatar')
                    </div>
                </div>
            </header>
        <main class="w-full min-w-0 p-3 sm:p-6 lg:p-8 pb-8 sm:pb-10">
            @if(session('success'))
                <div class="mb-6 rounded-xl bg-amber-50 dark:bg-amber-900/30 border border-amber-200/80 dark:border-amber-800/50 text-amber-900 dark:text-amber-100 px-5 py-4 font-medium shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200/80 dark:border-red-800/50 text-red-800 dark:text-red-200 px-5 py-4 font-medium shadow-sm">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
        </div>
    </div>
    @stack('scripts')
    @if(session('success'))
        <script>
            (function () {
                function fire() {
                    setTimeout(function () {
                        window.dispatchEvent(new CustomEvent('sangha-notifications-refresh'));
                    }, 400);
                }
                if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fire);
                else fire();
            })();
        </script>
    @endif
    <script>
        (function() {
            var toggles = function () { return document.querySelectorAll('.js-admin-sidebar-toggle'); };
            var sidebar = document.getElementById('admin-sidebar');
            var overlay = document.getElementById('admin-sidebar-overlay');
            var mqLg = window.matchMedia('(min-width: 1024px)');
            var drawerBodyUnlockTimer = null;
            var mobileDrawerCloseHandlerRef = null;

            function clearMobileDrawerCloseListener() {
                if (sidebar && mobileDrawerCloseHandlerRef) {
                    sidebar.removeEventListener('transitionend', mobileDrawerCloseHandlerRef);
                    mobileDrawerCloseHandlerRef = null;
                }
            }

            function expandDesktopSidebar() {
                document.documentElement.classList.remove('admin-sidebar-collapsed');
            }

            function isLg() {
                return mqLg.matches;
            }

            function syncToggleIcons() {
                toggles().forEach(function (toggle) {
                    var iconMenu = toggle.querySelector('.js-as-menu');
                    var iconClose = toggle.querySelector('.js-as-close');
                    var iconCollapse = toggle.querySelector('.js-as-collapse');
                    if (!iconMenu || !iconClose || !iconCollapse) return;
                    if (isLg()) {
                        var collapsed = document.documentElement.classList.contains('admin-sidebar-collapsed');
                        iconMenu.classList.toggle('hidden', !collapsed);
                        iconCollapse.classList.toggle('hidden', collapsed);
                        iconClose.classList.add('hidden');
                        toggle.setAttribute('aria-expanded', (!collapsed).toString());
                    } else {
                        var drawerOpen = sidebar && sidebar.classList.contains('admin-mobile-drawer-open');
                        iconMenu.classList.toggle('hidden', drawerOpen);
                        iconClose.classList.toggle('hidden', !drawerOpen);
                        iconCollapse.classList.add('hidden');
                        toggle.setAttribute('aria-expanded', drawerOpen ? 'true' : 'false');
                    }
                });
            }

            function openSidebar() {
                if (!sidebar) return;
                clearMobileDrawerCloseListener();
                if (drawerBodyUnlockTimer) {
                    clearTimeout(drawerBodyUnlockTimer);
                    drawerBodyUnlockTimer = null;
                }
                sidebar.classList.add('admin-mobile-drawer-open');
                if (overlay) {
                    overlay.classList.add('is-open');
                overlay.setAttribute('aria-hidden', 'false');
                }
                document.body.style.overflow = 'hidden';
                syncToggleIcons();
            }

            function closeSidebar() {
                if (!sidebar) return;
                clearMobileDrawerCloseListener();
                sidebar.classList.remove('admin-mobile-drawer-open');
                if (overlay) {
                    overlay.classList.remove('is-open');
                overlay.setAttribute('aria-hidden', 'true');
                }
                if (drawerBodyUnlockTimer) {
                    clearTimeout(drawerBodyUnlockTimer);
                    drawerBodyUnlockTimer = null;
                }
                var delayMs = 0;
                try {
                    delayMs = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 420;
                } catch (e) {
                    delayMs = 420;
                }
                var mobileCloseDone = false;
                function finishMobileCloseOnce() {
                    if (mobileCloseDone) return;
                    mobileCloseDone = true;
                    clearMobileDrawerCloseListener();
                    if (drawerBodyUnlockTimer) {
                        clearTimeout(drawerBodyUnlockTimer);
                        drawerBodyUnlockTimer = null;
                    }
                    document.body.style.overflow = '';
                    syncToggleIcons();
                }
                function onMobileDrawerTransitionEnd(e) {
                    if (e.target !== sidebar || e.propertyName !== 'transform') return;
                    finishMobileCloseOnce();
                }
                mobileDrawerCloseHandlerRef = onMobileDrawerTransitionEnd;
                sidebar.addEventListener('transitionend', onMobileDrawerTransitionEnd);
                drawerBodyUnlockTimer = setTimeout(finishMobileCloseOnce, delayMs + 80);
            }

            function resetMobileDrawerForDesktop() {
                clearMobileDrawerCloseListener();
                if (drawerBodyUnlockTimer) {
                    clearTimeout(drawerBodyUnlockTimer);
                    drawerBodyUnlockTimer = null;
                }
                document.body.style.overflow = '';
                if (sidebar) sidebar.classList.remove('admin-mobile-drawer-open');
                if (overlay) {
                    overlay.classList.remove('is-open');
                    overlay.setAttribute('aria-hidden', 'true');
                }
            }

            function onToggleClick() {
                if (isLg()) {
                    var root = document.documentElement;
                    var nowCollapsed = root.classList.contains('admin-sidebar-collapsed');
                    if (nowCollapsed) {
                        expandDesktopSidebar();
                    } else {
                        root.classList.add('admin-sidebar-collapsed');
                    }
                    try {
                        localStorage.setItem('admin_sidebar_collapsed', root.classList.contains('admin-sidebar-collapsed') ? '1' : '0');
                    } catch (e) {}
                    syncToggleIcons();
                } else if (sidebar) {
                    sidebar.classList.contains('admin-mobile-drawer-open') ? closeSidebar() : openSidebar();
                }
            }

            toggles().forEach(function (btn) {
                btn.addEventListener('click', onToggleClick);
            });
            if (overlay) overlay.addEventListener('click', closeSidebar);
            window.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !isLg()) closeSidebar();
            });
            mqLg.addEventListener('change', function (e) {
                if (e.matches) {
                    resetMobileDrawerForDesktop();
                    try {
                        if (localStorage.getItem('admin_sidebar_collapsed') === '1') {
                            document.documentElement.classList.add('admin-sidebar-collapsed');
                        }
                    } catch (err) {}
                } else {
                    expandDesktopSidebar();
                }
                syncToggleIcons();
            });
            document.querySelectorAll('.sidebar-group-btn').forEach(function (btn) {
                btn.addEventListener(
                    'click',
                    function () {
                        if (!isLg() || !document.documentElement.classList.contains('admin-sidebar-collapsed')) return;
                        expandDesktopSidebar();
                        try {
                            localStorage.setItem('admin_sidebar_collapsed', '0');
                        } catch (e) {}
                        syncToggleIcons();
                    },
                    true
                );
            });
            syncToggleIcons();
        })();
    </script>
    <script>
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || form.tagName !== 'FORM' || !form.hasAttribute('data-admin-submit-once')) return;
            form.querySelectorAll('button[type="submit"]').forEach(function (btn) {
                btn.disabled = true;
            });
        }, true);
    </script>
</body>
</html>
