@extends('layouts.guest')

@section('title', t('student_login'))

@section('content')
<div class="w-full max-w-md">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100 mb-1">{{ t('student_login') }}</h1>
    <p class="text-slate-600 dark:text-slate-400 text-sm mb-6">{{ t('student_login_hint') }}</p>

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('sangha.login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="username" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('username') }}</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('password') }}</label>
            <div class="relative">
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-3 pr-12 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                <button type="button" class="toggle-password absolute left-auto right-3 top-1/2 -translate-y-1/2 p-1 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded transition-colors relative w-9 h-9 flex items-center justify-center" aria-label="Show password" tabindex="-1">
                    <span class="icon-eye absolute inset-0 flex items-center justify-center">@include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])</span>
                    <span class="icon-eye-off absolute inset-0 flex items-center justify-center" style="display: none">@include('partials.icon', ['name' => 'eye-off', 'class' => 'w-5 h-5'])</span>
                </button>
            </div>
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
</div>
<script>
(function() {
    document.querySelector('.toggle-password').addEventListener('click', function() {
        var wrap = this.closest('div.relative');
        var input = wrap.querySelector('input[type="password"], input[type="text"]');
        var eye = wrap.querySelector('.icon-eye');
        var eyeOff = wrap.querySelector('.icon-eye-off');
        if (input.type === 'password') {
            input.type = 'text';
            this.setAttribute('aria-label', 'Hide password');
            eye.style.display = 'none';
            eyeOff.style.display = 'flex';
        } else {
            input.type = 'password';
            this.setAttribute('aria-label', 'Show password');
            eye.style.display = 'flex';
            eyeOff.style.display = 'none';
        }
    });
})();
</script>
@endsection
