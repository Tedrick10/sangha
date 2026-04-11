@php $monasteryTheme = resolved_app_theme(); @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $monasteryTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', t('monastery_portal')) - {{ config('app.name') }}</title>
    @include('partials.favicon')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <script>
        (function() {
            function applyMonasteryTheme() {
                var theme = document.documentElement.getAttribute('data-theme') || 'system';
                var dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
            }
            window.sanghaSetWebsiteTheme = function(theme) {
                if (theme !== 'light' && theme !== 'dark' && theme !== 'system') return;
                document.documentElement.setAttribute('data-theme', theme);
                applyMonasteryTheme();
            };
            applyMonasteryTheme();
            try {
                var mq = window.matchMedia('(prefers-color-scheme: dark)');
                var onChange = function () {
                    var t = document.documentElement.getAttribute('data-theme') || 'system';
                    if (t === 'system') applyMonasteryTheme();
                };
                if (mq.addEventListener) mq.addEventListener('change', onChange);
                else if (mq.addListener) mq.addListener(onChange);
            } catch (e) {}
        })();
    </script>
</head>
@php $__ap = appearance_portal_body_attrs('monastery'); @endphp
<body class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-900 dark:text-slate-100 font-sans {{ $__ap['class'] }}" @if($__ap['style'] !== '') style="{{ $__ap['style'] }}" @endif data-app-locale-url="{{ route('app.set-locale') }}" data-app-theme-url="{{ route('app.set-theme') }}">
    <header class="monastery-header sticky top-0 z-40 border-b border-slate-200/80 dark:border-slate-700/80 bg-white/90 dark:bg-slate-900/90 backdrop-blur">
        {{-- Title wraps instead of ellipsis; toolbar scrolls horizontally if needed. --}}
        <div class="mx-auto flex max-w-5xl min-w-0 flex-nowrap items-center justify-between gap-2 px-3 py-2.5 sm:gap-3 sm:px-6 sm:py-3">
            <div class="min-w-0 flex-1 basis-0">
                <p class="break-words text-sm font-semibold leading-snug text-slate-900 dark:text-slate-100 [overflow-wrap:anywhere]">
                    {{ auth()->guard('monastery')->user()->name ?? t('monastery') }}<span class="monastery-header-kicker font-medium text-slate-500 dark:text-amber-200"> · {{ t('monastery_portal') }}</span>
                </p>
            </div>
            <div class="monastery-header-toolbar flex shrink-0 flex-nowrap items-center justify-end gap-1 overflow-x-auto [-webkit-overflow-scrolling:touch] sm:gap-2 no-scrollbar">
                @include('partials.notifications-bell', ['notifiable' => auth()->guard('monastery')->user(), 'goRouteName' => 'monastery.notifications.go', 'readAllRouteName' => 'monastery.notifications.read-all', 'jsonRouteName' => 'monastery.notifications.recent'])
                @include('website.partials.appbar-language')
                @include('website.partials.appbar-theme')
                <form action="{{ route('monastery.logout') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" class="monastery-header-exit inline-flex items-center rounded-lg border border-slate-300 px-2 py-1.5 text-[11px] font-semibold text-slate-700 transition-colors hover:bg-slate-100 dark:border-amber-500/35 dark:text-amber-100 dark:hover:bg-slate-800 dark:hover:border-amber-400/50 sm:px-3 sm:py-2 sm:text-xs">{{ t('exit') }}</button>
                </form>
            </div>
        </div>
    </header>

    {{-- Bottom padding ≈ fixed nav (bottom-5 + bar height) + safe area — avoid duplicating large pb inside tab content. --}}
    <main class="mx-auto w-full max-w-5xl px-4 py-5 sm:px-6 sm:py-6 pb-[calc(7rem+env(safe-area-inset-bottom,0px))] max-[480px]:pb-[calc(7.25rem+env(safe-area-inset-bottom,0px))]">
        @if(session('success'))
            <div class="mb-5 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200/80 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-200 px-5 py-3.5 font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200/80 dark:border-red-800/50 text-red-800 dark:text-red-200 px-5 py-3.5 font-medium">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>
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
    @stack('scripts')
</body>
</html>
