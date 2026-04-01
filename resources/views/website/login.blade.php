@extends('website.layout')

@section('title', t('login'))

@section('content')
@php
    $requestedType = request('type') === 'sangha' ? 'sangha' : 'monastery';
    $requestedMode = request('mode') === 'register' ? 'register' : 'login';
    $activeTab = old('form_type') === 'sangha' || session('login_form') === 'sangha' ? 'sangha' : $requestedType;
    $monasteryMode = old('form_type') === 'monastery' ? 'register' : ($activeTab === 'monastery' ? $requestedMode : 'login');
    $sanghaMode = old('form_type') === 'sangha' ? 'register' : ($activeTab === 'sangha' ? $requestedMode : 'login');
@endphp

<section class="relative py-10 sm:py-14 lg:py-16">
    <div class="max-w-5xl mx-auto">
        <div class="rounded-3xl border border-stone-200 dark:border-slate-700 bg-white/90 dark:bg-slate-900/80 shadow-xl shadow-stone-200/40 dark:shadow-none overflow-hidden">
            <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-4 border-b border-stone-200 dark:border-slate-700 bg-gradient-to-r from-amber-50/50 to-transparent dark:from-amber-900/10 dark:to-transparent">
                <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-stone-900 dark:text-slate-100">{{ $activeTab === 'sangha' ? 'Sangha' : 'Monastery' }}</h1>
                <p class="mt-2 text-sm sm:text-base text-stone-600 dark:text-slate-300">{{ t('login_switch_hint', 'Switch between Register and Login.') }}</p>
            </div>

            @if(session('success'))
                <div class="mx-6 sm:mx-8 mt-6 rounded-xl border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="p-6 sm:p-8">
                <div id="panel-monastery" class="entity-panel {{ $activeTab === 'monastery' ? '' : 'hidden' }}" data-entity="monastery" data-mode="{{ $monasteryMode }}">
                    <div class="mb-6 flex rounded-xl border border-stone-200 dark:border-slate-700 p-1 bg-stone-50/70 dark:bg-slate-800/60">
                        <button type="button" class="mode-tab flex-1 px-4 py-2 rounded-lg text-sm font-semibold" data-entity="monastery" data-mode="register">{{ t('register', 'Register') }}</button>
                        <button type="button" class="mode-tab flex-1 px-4 py-2 rounded-lg text-sm font-semibold" data-entity="monastery" data-mode="login">{{ t('login', 'Login') }}</button>
                    </div>

                    <div class="mode-panel" data-entity="monastery" data-mode="register">
                        @if($errors->any() && old('form_type') === 'monastery')
                            <div class="mb-5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 text-sm">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('website.register.monastery') }}" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <input type="hidden" name="form_type" value="monastery">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="reg_monastery_name" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('name') }} *</label>
                                    <input type="text" id="reg_monastery_name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                </div>
                                <div>
                                    <label for="reg_monastery_username" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('username') }} *</label>
                                    <input type="text" id="reg_monastery_username" name="username" value="{{ old('username') }}" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="reg_monastery_password" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('password') }} *</label>
                                    <div class="relative" data-password-toggle-ignore="1">
                                        <input type="password" id="reg_monastery_password" name="password" required class="w-full px-4 py-3 pr-12 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                        <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 rounded transition-colors w-9 h-9 flex items-center justify-center z-10" aria-label="Show password" tabindex="-1">
                                            <span class="icon-eye absolute inset-0 flex items-center justify-center">@include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])</span>
                                            <span class="icon-eye-off absolute inset-0 flex items-center justify-center hidden">@include('partials.icon', ['name' => 'eye-off', 'class' => 'w-5 h-5'])</span>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label for="reg_monastery_password_confirmation" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('confirm_password') }} *</label>
                                    <div class="relative" data-password-toggle-ignore="1">
                                        <input type="password" id="reg_monastery_password_confirmation" name="password_confirmation" required class="w-full px-4 py-3 pr-12 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                        <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 rounded transition-colors w-9 h-9 flex items-center justify-center z-10" aria-label="Show password" tabindex="-1">
                                            <span class="icon-eye absolute inset-0 flex items-center justify-center">@include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])</span>
                                            <span class="icon-eye-off absolute inset-0 flex items-center justify-center hidden">@include('partials.icon', ['name' => 'eye-off', 'class' => 'w-5 h-5'])</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="reg_monastery_region" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('region') }}</label>
                                    <input type="text" id="reg_monastery_region" name="region" value="{{ old('region') }}" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                </div>
                                <div>
                                    <label for="reg_monastery_city" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('city') }}</label>
                                    <input type="text" id="reg_monastery_city" name="city" value="{{ old('city') }}" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                </div>
                            </div>
                            <div>
                                <label for="reg_monastery_address" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('address') }}</label>
                                <input type="text" id="reg_monastery_address" name="address" value="{{ old('address') }}" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                            </div>
                            <div>
                                <label for="reg_monastery_phone" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('phone') }}</label>
                                <input type="text" id="reg_monastery_phone" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                            </div>
                            <div>
                                <label for="reg_monastery_description" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('description') }}</label>
                                <textarea id="reg_monastery_description" name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">{{ old('description') }}</textarea>
                            </div>
                            @if($monasteryCustomFields->isNotEmpty())
                                <div class="pt-2 border-t border-stone-200 dark:border-slate-700">
                                    <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-stone-600 dark:text-slate-300 mb-4">{{ t('custom_fields') }}</h3>
                                    <div class="space-y-5">
                                        @include('website.partials.custom-fields', ['customFields' => $monasteryCustomFields, 'oldPrefix' => '', 'idPrefix' => 'monastery'])
                                    </div>
                                </div>
                            @endif
                            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-amber-500 text-white font-semibold hover:bg-amber-600 focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 transition-colors">{{ t('register_monastery', 'Register Monastery') }}</button>
                        </form>
                    </div>

                    <div class="mode-panel" data-entity="monastery" data-mode="login">
                        @if($errors->any() && session('login_form') === 'monastery')
                            <div class="mb-5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 text-sm">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('monastery.login') }}" class="space-y-5">
                            @csrf
                            <div>
                                <label for="monastery_username" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('username') }}</label>
                                <input type="text" id="monastery_username" name="username" value="{{ old('username') }}" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/50 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 placeholder-stone-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                            </div>
                            <div>
                                <label for="monastery_password" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('password') }}</label>
                                <div class="relative" data-password-toggle-ignore="1">
                                    <input type="password" id="monastery_password" name="password" required class="w-full px-4 py-3 pr-12 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/50 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 rounded transition-colors w-9 h-9 flex items-center justify-center z-10" aria-label="Show password" tabindex="-1">
                                        <span class="icon-eye absolute inset-0 flex items-center justify-center">@include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])</span>
                                        <span class="icon-eye-off absolute inset-0 flex items-center justify-center hidden">@include('partials.icon', ['name' => 'eye-off', 'class' => 'w-5 h-5'])</span>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="monastery_remember" name="remember" class="rounded border-stone-300 text-amber-500 focus:ring-amber-500">
                                <label for="monastery_remember" class="ml-2 text-sm text-stone-600 dark:text-slate-400">{{ t('remember_me') }}</label>
                            </div>
                            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-amber-500 text-white font-semibold hover:bg-amber-600 focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 transition-colors">{{ t('login') }}</button>
                        </form>
                    </div>
                </div>

                <div id="panel-sangha" class="entity-panel {{ $activeTab === 'sangha' ? '' : 'hidden' }}" data-entity="sangha" data-mode="{{ $sanghaMode }}">
                    <div class="mb-6 flex rounded-xl border border-stone-200 dark:border-slate-700 p-1 bg-stone-50/70 dark:bg-slate-800/60">
                        <button type="button" class="mode-tab flex-1 px-4 py-2 rounded-lg text-sm font-semibold" data-entity="sangha" data-mode="register">{{ t('register', 'Register') }}</button>
                        <button type="button" class="mode-tab flex-1 px-4 py-2 rounded-lg text-sm font-semibold" data-entity="sangha" data-mode="login">{{ t('login', 'Login') }}</button>
                    </div>

                    <div class="mode-panel" data-entity="sangha" data-mode="register">
                        @if($errors->any() && old('form_type') === 'sangha')
                            <div class="mb-5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 text-sm">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('website.register.sangha') }}" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <input type="hidden" name="form_type" value="sangha">
                            <div>
                                <label for="reg_sangha_monastery_id" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('monastery') }} *</label>
                                <select id="reg_sangha_monastery_id" name="monastery_id" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                    <option value="">{{ t('select_monastery') }}</option>
                                    @foreach($monasteries as $monastery)
                                        <option value="{{ $monastery->id }}" {{ old('monastery_id') == $monastery->id ? 'selected' : '' }}>{{ $monastery->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="reg_sangha_name" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('name') }} *</label>
                                    <input type="text" id="reg_sangha_name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                </div>
                                <div>
                                    <label for="reg_sangha_username" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('username') }} *</label>
                                    <input type="text" id="reg_sangha_username" name="username" value="{{ old('username') }}" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="reg_sangha_password" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('password') }} *</label>
                                    <div class="relative" data-password-toggle-ignore="1">
                                        <input type="password" id="reg_sangha_password" name="password" required class="w-full px-4 py-3 pr-12 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                        <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 rounded transition-colors w-9 h-9 flex items-center justify-center z-10" aria-label="Show password" tabindex="-1">
                                            <span class="icon-eye absolute inset-0 flex items-center justify-center">@include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])</span>
                                            <span class="icon-eye-off absolute inset-0 flex items-center justify-center hidden">@include('partials.icon', ['name' => 'eye-off', 'class' => 'w-5 h-5'])</span>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label for="reg_sangha_password_confirmation" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('confirm_password') }} *</label>
                                    <div class="relative" data-password-toggle-ignore="1">
                                        <input type="password" id="reg_sangha_password_confirmation" name="password_confirmation" required class="w-full px-4 py-3 pr-12 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                        <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 rounded transition-colors w-9 h-9 flex items-center justify-center z-10" aria-label="Show password" tabindex="-1">
                                            <span class="icon-eye absolute inset-0 flex items-center justify-center">@include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])</span>
                                            <span class="icon-eye-off absolute inset-0 flex items-center justify-center hidden">@include('partials.icon', ['name' => 'eye-off', 'class' => 'w-5 h-5'])</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="reg_sangha_exam_id" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('exam') }} {{ t('optional') }}</label>
                                <select id="reg_sangha_exam_id" name="exam_id" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                    <option value="">{{ t('select_exam') }}</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->name }}{{ $exam->exam_date ? ' (' . $exam->exam_date->format('M d, Y') . ')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="reg_sangha_description" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('description') }}</label>
                                <textarea id="reg_sangha_description" name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">{{ old('description') }}</textarea>
                            </div>
                            @if($sanghaCustomFields->isNotEmpty())
                                <div class="pt-2 border-t border-stone-200 dark:border-slate-700">
                                    <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-stone-600 dark:text-slate-300 mb-4">{{ t('custom_fields') }}</h3>
                                    <div class="space-y-5">
                                        @include('website.partials.custom-fields', ['customFields' => $sanghaCustomFields, 'oldPrefix' => '', 'idPrefix' => 'sangha'])
                                    </div>
                                </div>
                            @endif
                            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-amber-500 text-white font-semibold hover:bg-amber-600 focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 transition-colors">{{ t('register_sangha', 'Register Sangha') }}</button>
                        </form>
                    </div>

                    <div class="mode-panel" data-entity="sangha" data-mode="login">
                        @if($errors->any() && session('login_form') === 'sangha')
                            <div class="mb-5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 text-sm">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('sangha.login') }}" class="space-y-5">
                            @csrf
                            <div>
                                <label for="sangha_username" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('username') }}</label>
                                <input type="text" id="sangha_username" name="username" value="{{ old('username') }}" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/50 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 placeholder-stone-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                            </div>
                            <div>
                                <label for="sangha_password" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">{{ t('password') }}</label>
                                <div class="relative" data-password-toggle-ignore="1">
                                    <input type="password" id="sangha_password" name="password" required class="w-full px-4 py-3 pr-12 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/50 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 rounded transition-colors w-9 h-9 flex items-center justify-center z-10" aria-label="Show password" tabindex="-1">
                                        <span class="icon-eye absolute inset-0 flex items-center justify-center">@include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])</span>
                                        <span class="icon-eye-off absolute inset-0 flex items-center justify-center hidden">@include('partials.icon', ['name' => 'eye-off', 'class' => 'w-5 h-5'])</span>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="sangha_remember" name="remember" class="rounded border-stone-300 text-amber-500 focus:ring-amber-500">
                                <label for="sangha_remember" class="ml-2 text-sm text-stone-600 dark:text-slate-400">{{ t('remember_me') }}</label>
                            </div>
                            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-amber-500 text-white font-semibold hover:bg-amber-600 focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 transition-colors">{{ t('login') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function() {
    function setEyeState(button, showText) {
        var eye = button.querySelector('.icon-eye');
        var eyeOff = button.querySelector('.icon-eye-off');
        if (!eye || !eyeOff) return;
        eye.style.display = showText ? 'none' : 'flex';
        eyeOff.style.display = showText ? 'flex' : 'none';
    }

    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        setEyeState(btn, false);
        btn.addEventListener('click', function() {
            var wrap = this.closest('div.relative');
            var input = wrap ? wrap.querySelector('input[type="password"], input[type="text"]') : null;
            if (!input) return;
            var showText = input.type === 'password';
            input.type = showText ? 'text' : 'password';
            this.setAttribute('aria-label', showText ? 'Hide password' : 'Show password');
            setEyeState(this, showText);
        });
    });

    var entityPanels = document.querySelectorAll('.entity-panel');
    var modeTabs = document.querySelectorAll('.mode-tab');

    function setMode(entity, mode) {
        modeTabs.forEach(function(tab) {
            if (tab.getAttribute('data-entity') !== entity) return;
            var active = tab.getAttribute('data-mode') === mode;
            tab.classList.toggle('bg-white', active);
            tab.classList.toggle('dark:bg-slate-700', active);
            tab.classList.toggle('text-amber-700', active);
            tab.classList.toggle('dark:text-amber-400', active);
            tab.classList.toggle('shadow-sm', active);
            tab.classList.toggle('text-stone-500', !active);
            tab.classList.toggle('dark:text-slate-400', !active);
        });

        document.querySelectorAll('.mode-panel').forEach(function(panel) {
            if (panel.getAttribute('data-entity') !== entity) return;
            panel.classList.toggle('hidden', panel.getAttribute('data-mode') !== mode);
        });
    }

    modeTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            setMode(tab.getAttribute('data-entity'), tab.getAttribute('data-mode'));
        });
    });

    entityPanels.forEach(function(panel) {
        setMode(panel.getAttribute('data-entity'), panel.getAttribute('data-mode') || 'login');
    });
})();
</script>
@endsection
