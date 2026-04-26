@extends('layouts.monastery')

@section('title', t('monastery_account_settings', 'Account settings'))

@section('content')
<div class="mx-auto max-w-2xl pb-8">
    <div class="mb-6">
        <a href="{{ route('monastery.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 transition hover:text-amber-600 dark:text-slate-400 dark:hover:text-amber-400">
            @include('partials.icon', ['name' => 'arrow-left', 'class' => 'h-4 w-4'])
            {{ t('monastery_account_back', 'Back to portal') }}
        </a>
    </div>

    <header class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 sm:text-3xl">{{ t('monastery_account_settings', 'Account settings') }}</h1>
        <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ t('monastery_account_intro', 'View and update your monastery account details. Password is optional—leave blank to keep your current password.') }}</p>
        <div class="mt-4 flex flex-wrap items-center gap-2">
            @if($monastery->moderationStatus() === 'approved')
                <span class="inline-flex rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-800 ring-1 ring-emerald-500/25 dark:text-emerald-200">{{ t('status_approved', 'Approved') }}</span>
            @elseif($monastery->moderationStatus() === 'rejected')
                <span class="inline-flex rounded-full bg-rose-500/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-rose-800 ring-1 ring-rose-500/25 dark:text-rose-200">{{ t('status_rejected', 'Rejected') }}</span>
            @else
                <span class="inline-flex rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-800 ring-1 ring-amber-500/25 dark:text-amber-200">{{ t('status_pending', 'Pending') }}</span>
            @endif
            @if($monastery->created_at)
                <span class="text-xs text-slate-500 dark:text-slate-400">{{ t('created_at', 'Created') }}: {{ $monastery->created_at->format('M j, Y') }}</span>
            @endif
        </div>
    </header>

    <section class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-sm dark:border-slate-700/60 dark:bg-slate-900/60 dark:shadow-none">
        <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-700/80">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ t('monastery_account_details', 'Account details') }}</h2>
            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ t('monastery_account_readonly_hint', 'Approval status is managed by administrators.') }}</p>
        </div>
        <form action="{{ route('monastery.account.update') }}" method="POST" class="divide-y divide-slate-100 dark:divide-slate-700/70">
            @csrf
            @method('PUT')

            <div class="space-y-4 px-5 py-5 sm:px-6">
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('name', 'Name') }} *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $monastery->name) }}" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    @error('name')<p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="username" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('username', 'Username') }} *</label>
                    <input type="text" name="username" id="username" value="{{ old('username', $monastery->username) }}" required autocomplete="username" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    @error('username')<p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('phone', 'Phone') }}</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $monastery->phone) }}" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                        @error('phone')<p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="region" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('region', 'Region') }}</label>
                        <input type="text" name="region" id="region" value="{{ old('region', $monastery->region) }}" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                        @error('region')<p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="city" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('city', 'City') }}</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $monastery->city) }}" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    @error('city')<p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="address" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('address', 'Address') }}</label>
                    <textarea name="address" id="address" rows="3" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('address', $monastery->address) }}</textarea>
                    @error('address')<p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('description', 'Description') }}</label>
                    <textarea name="description" id="description" rows="3" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('description', $monastery->description) }}</textarea>
                    @error('description')<p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="space-y-4 px-5 py-5 sm:px-6">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('change_password', 'Change password') }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ t('leave_blank_unchanged', 'Leave blank to keep current') }}</p>
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('password', 'Password') }}</label>
                    <input type="password" name="password" id="password" value="" autocomplete="new-password" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    @error('password')<p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('confirm_password', 'Confirm Password') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" value="" autocomplete="new-password" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 px-5 py-4 sm:px-6">
                <a href="{{ route('monastery.dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">{{ t('cancel', 'Cancel') }}</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-600">{{ t('save', 'Save') }}</button>
            </div>
        </form>
    </section>
</div>
@endsection
