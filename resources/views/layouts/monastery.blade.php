<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', t('monastery_portal')) - {{ config('app.name', 'Sangha Exam') }}</title>
    @php $favicon = \App\Models\SiteSetting::imageUrl('favicon'); @endphp
    @if($favicon)
        <link rel="icon" href="{{ $favicon }}" type="image/x-icon">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 font-sans">
    <header class="sticky top-0 z-40 border-b border-slate-200/80 dark:border-slate-700/80 bg-white/90 dark:bg-slate-900/90 backdrop-blur">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ auth()->guard('monastery')->user()->name ?? t('monastery') }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ t('monastery_portal') }}</p>
            </div>
            <div class="flex items-center gap-2">
                @include('website.partials.appbar-language')
                <form action="{{ route('monastery.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">{{ t('exit') }}</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto w-full px-4 sm:px-6 py-5 sm:py-6 pb-24">
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
</body>
</html>
