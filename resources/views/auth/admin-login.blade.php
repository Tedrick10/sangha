@extends('website.layout')

@section('title', t('admin_login'))

@section('content')
<div class="flex justify-center py-8 sm:py-12">
<div class="w-full max-w-md">
    <h1 class="mb-1 text-2xl font-bold text-stone-900 dark:text-slate-100">{{ t('admin_login') }}</h1>
    <p class="mb-6 text-sm text-stone-600 dark:text-slate-400">{{ t('admin_login_hint') }}</p>

    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-stone-700 dark:text-slate-300">{{ t('email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                   class="w-full rounded-xl border border-stone-200 bg-white px-4 py-3 text-stone-900 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
        </div>
        <div>
            <label for="password" class="mb-1 block text-sm font-medium text-stone-700 dark:text-slate-300">{{ t('password') }}</label>
            <input type="password" name="password" id="password" required
                   class="w-full rounded-xl border border-stone-200 bg-white px-4 py-3 text-stone-900 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember" class="rounded border-stone-300 text-yellow-600 focus:ring-yellow-500 dark:border-slate-500">
            <label for="remember" class="ml-2 text-sm text-stone-600 dark:text-slate-400">{{ t('remember_me') }}</label>
        </div>
        <button type="submit" class="w-full rounded-xl bg-yellow-500 px-4 py-3 font-semibold text-white transition-colors hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 dark:focus:ring-offset-slate-950">
            {{ t('login') }}
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-stone-500 dark:text-slate-400">
        <a href="{{ url('/') }}" class="font-medium text-yellow-700 hover:underline dark:text-yellow-400">{{ t('back_to_home') }}</a>
    </p>

    <div class="mt-6 rounded-xl border border-stone-200 bg-stone-50 p-4 dark:border-slate-600 dark:bg-slate-800/50">
        <p class="mb-2 text-xs font-medium uppercase tracking-wide text-stone-500 dark:text-slate-400">{{ t('demo_credentials') }}</p>
        <button type="button" id="use-credentials" class="group w-full cursor-pointer rounded-lg border border-stone-200 bg-white p-3 text-left dark:border-slate-600 dark:bg-slate-800">
            <p class="font-mono text-sm text-stone-700 dark:text-slate-300"><span class="text-stone-500 dark:text-slate-400">{{ t('email') }}:</span> admin@sanghaexam.org</p>
            <p class="mt-1 font-mono text-sm text-stone-700 dark:text-slate-300"><span class="text-stone-500 dark:text-slate-400">{{ t('password') }}:</span> password123</p>
            <p class="mt-2 text-xs text-yellow-700 opacity-0 transition-opacity group-hover:opacity-100 dark:text-yellow-400">{{ t('click_to_use_credentials') }}</p>
        </button>
    </div>
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
