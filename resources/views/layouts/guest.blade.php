@php $guestTheme = resolved_app_theme(); @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $guestTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - {{ config('app.name', 'Sangha Exam') }}</title>
    @php $favicon = \App\Models\SiteSetting::imageUrl('favicon'); @endphp
    @if($favicon)
        <link rel="icon" href="{{ $favicon }}" type="image/x-icon">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    {{-- Same as public site: Tailwind `dark:` needs `.dark` on <html>; session theme sets data-theme. --}}
    <script>
        (function() {
            function applyGuestTheme() {
                var theme = document.documentElement.getAttribute('data-theme') || 'system';
                var dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
            }
            window.sanghaApplyWebsiteTheme = applyGuestTheme;
            window.sanghaSetWebsiteTheme = function(theme) {
                if (theme !== 'light' && theme !== 'dark' && theme !== 'system') return;
                document.documentElement.setAttribute('data-theme', theme);
                applyGuestTheme();
            };
            applyGuestTheme();
            try {
                var mq = window.matchMedia('(prefers-color-scheme: dark)');
                var onChange = function () {
                    var t = document.documentElement.getAttribute('data-theme') || 'system';
                    if (t === 'system') applyGuestTheme();
                };
                if (mq.addEventListener) mq.addEventListener('change', onChange);
                else if (mq.addListener) mq.addListener(onChange);
            } catch (e) {}
        })();
    </script>
</head>
<body class="min-h-screen flex flex-col bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 font-sans" data-app-locale-url="{{ route('app.set-locale') }}" data-app-theme-url="{{ route('app.set-theme') }}">
    <header class="shrink-0 border-b border-slate-200/90 dark:border-slate-700/90 bg-white/90 dark:bg-slate-950/90 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto w-full flex items-center justify-between gap-3 px-4 sm:px-6 py-3">
            <button type="button" id="guest-back-btn" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-colors">
                @include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0'])
                {{ t('back', 'Back') }}
            </button>
            <div class="shrink-0">
                @include('website.partials.appbar-theme')
            </div>
        </div>
    </header>
    <main class="flex-1 flex items-center justify-center p-4 w-full min-h-0">
        @yield('content')
    </main>
    <script>
        (function () {
            var btn = document.getElementById('guest-back-btn');
            if (!btn) return;
            var home = @json(url('/'));
            btn.addEventListener('click', function () {
                var ref = document.referrer;
                try {
                    if (ref && new URL(ref).origin === window.location.origin) {
                        window.history.back();
                        return;
                    }
                } catch (e) {}
                window.location.href = home;
            });
        })();
    </script>
</body>
</html>
