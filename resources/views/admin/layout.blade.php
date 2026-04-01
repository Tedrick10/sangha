@php $adminTheme = session('admin_theme', 'system'); @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $adminTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Sangha Exam</title>
    @php $favicon = \App\Models\SiteSetting::imageUrl('favicon'); @endphp
    @if($favicon)
        <link rel="icon" href="{{ $favicon }}" type="image/x-icon">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <script>
        (function() {
            var theme = document.documentElement.getAttribute('data-theme') || 'system';
            var dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (dark) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        })();
    </script>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 font-sans">
    {{-- Mobile overlay --}}
    <div id="admin-sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 lg:hidden hidden" aria-hidden="true"></div>
    <div class="flex">
        {{-- Sidebar: hidden on mobile, shown in drawer; always visible on lg+ --}}
        <aside id="admin-sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 min-h-screen bg-slate-800 text-white shadow-lg flex flex-col -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-out">
            <div class="h-14 shrink-0 flex items-center px-4 border-b border-slate-700/80">
                @php $logo = \App\Models\SiteSetting::imageUrl('logo'); @endphp
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ t('sangha_exam') }}" class="h-8 max-w-[140px] object-contain">
                    @else
                        <span class="font-semibold text-white">{{ t('sangha_exam') }}</span>
                    @endif
                </a>
            </div>
            @include('admin.partials.sidebar-nav')
            <div class="p-4 border-t border-slate-700">
                <form action="{{ route('admin.logout') }}" method="POST" class="block">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 whitespace-nowrap w-full px-4 py-2 text-sm text-slate-400 hover:text-white text-left">@include('partials.icon', ['name' => 'logout', 'class' => 'w-5 h-5 shrink-0']) {{ t('exit') }}</button>
                </form>
            </div>
        </aside>

        {{-- Header + Main content --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="admin-header relative z-50 flex items-center justify-between gap-2 sm:gap-3 px-4 sm:px-6 lg:px-8 h-14 border-b border-slate-200/80 dark:border-slate-700/80 bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm shrink-0">
                <button type="button" id="admin-sidebar-toggle" aria-label="{{ t('menu') }}" aria-expanded="false" class="lg:hidden p-2 -ml-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <svg id="menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <div class="flex items-center gap-2 sm:gap-3 ml-auto">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 whitespace-nowrap px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-700' }}">@include('partials.icon', ['name' => 'home', 'class' => 'w-4 h-4']) {{ t('dashboard') }}</a>
                <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 whitespace-nowrap px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">@include('partials.icon', ['name' => 'external-link', 'class' => 'w-4 h-4']) {{ t('view_site') }}</a>
                @include('admin.partials.appbar-language')
                @include('admin.partials.appbar-theme')
                @include('admin.partials.appbar-avatar')
                </div>
            </header>
        <main class="flex-1 w-full min-w-0 p-4 sm:p-6 lg:p-8">
            @if(session('success'))
                <div class="mb-6 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200/80 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-200 px-5 py-4 font-medium shadow-sm">
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
    <script>
        (function() {
            var toggle = document.getElementById('admin-sidebar-toggle');
            var sidebar = document.getElementById('admin-sidebar');
            var overlay = document.getElementById('admin-sidebar-overlay');
            var menuIcon = document.getElementById('menu-icon');
            var closeIcon = document.getElementById('close-icon');
            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                overlay.setAttribute('aria-hidden', 'false');
                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
                toggle.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }
            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                overlay.setAttribute('aria-hidden', 'true');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
            if (toggle) toggle.addEventListener('click', function() { sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar(); });
            if (overlay) overlay.addEventListener('click', closeSidebar);
            window.matchMedia('(min-width: 1024px)').addEventListener('change', function(e) { if (e.matches) closeSidebar(); });
        })();
    </script>
</body>
</html>
