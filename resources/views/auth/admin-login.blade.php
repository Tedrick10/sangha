@extends('layouts.guest')

@section('title', t('admin_login'))

@section('content')
<div class="w-full max-w-md">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100 mb-1">{{ t('admin_login') }}</h1>
    <p class="text-slate-600 dark:text-slate-400 text-sm mb-6">{{ t('admin_login_hint') }}</p>

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('password') }}</label>
            <input type="password" name="password" id="password" required
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
            <label for="remember" class="ml-2 text-sm text-slate-600 dark:text-slate-400">{{ t('remember_me') }}</label>
        </div>
        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-amber-500 text-white font-semibold hover:bg-amber-600 focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors">
            {{ t('login') }}
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ url('/') }}" class="text-amber-600 dark:text-amber-400 hover:underline">{{ t('back_to_home') }}</a>
    </p>

    <div class="mt-6 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/50 p-4">
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">{{ t('demo_credentials') }}</p>
        <button type="button" id="use-credentials" class="w-full text-left rounded-lg p-3 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:border-amber-300 dark:hover:border-amber-700 transition-colors group cursor-pointer">
            <p class="text-sm font-mono text-slate-700 dark:text-slate-300"><span class="text-slate-500 dark:text-slate-400">{{ t('email') }}:</span> admin@sanghaexam.org</p>
            <p class="text-sm font-mono text-slate-700 dark:text-slate-300 mt-1"><span class="text-slate-500 dark:text-slate-400">{{ t('password') }}:</span> password123</p>
            <p class="text-xs text-amber-600 dark:text-amber-400 mt-2 opacity-0 group-hover:opacity-100 transition-opacity">{{ t('click_to_use_credentials') }}</p>
        </button>
    </div>
</div>
<script>
(function() {
    document.getElementById('use-credentials').addEventListener('click', function() {
        document.getElementById('email').value = 'admin@sanghaexam.org';
        document.getElementById('password').value = 'password123';
        document.getElementById('remember').checked = true;
    });
})();
</script>
@endsection
