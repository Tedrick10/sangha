<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', t('student_portal')) - {{ config('app.name') }}</title>
    @include('partials.favicon')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 font-sans">
    <div class="flex">
        <aside class="w-64 min-h-screen bg-slate-800 text-white flex flex-col shrink-0">
            <div class="p-4 pt-6 border-b border-slate-700">
                <h2 class="font-semibold text-white">{{ auth()->guard('student')->user()->name ?? t('student') }}</h2>
                <p class="text-xs text-slate-400 mt-0.5">{{ t('student_portal') }}</p>
            </div>
            <nav class="p-4 flex-1 space-y-0.5">
                <a href="{{ route('sangha.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm {{ request()->routeIs('sangha.dashboard') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    {{ t('my_scores') }}
                </a>
            </nav>
            <div class="p-4 border-t border-slate-700">
                <form action="{{ route('sangha.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-slate-400 hover:text-white text-left">{{ t('exit') }}</button>
                </form>
            </div>
        </aside>
        <main class="flex-1 min-w-0 p-6 lg:p-8">
            @if(session('success'))
                <div class="mb-6 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200/80 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-200 px-5 py-4 font-medium">
                    {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
