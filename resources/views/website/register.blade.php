@extends('website.layout')

@section('title', 'Register')

@section('content')
@php
    $activeTab = request('type') === 'sangha' || old('form_type', 'monastery') === 'sangha' ? 'sangha' : 'monastery';
@endphp
<section class="relative py-10 sm:py-14 lg:py-16">
    <div class="max-w-5xl mx-auto">
        <div class="rounded-3xl border border-stone-200 dark:border-slate-700 bg-white/90 dark:bg-slate-900/80 shadow-xl shadow-stone-200/40 dark:shadow-none overflow-hidden">
            <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-4 border-b border-stone-200 dark:border-slate-700 bg-gradient-to-r from-amber-50/50 to-transparent dark:from-amber-900/10 dark:to-transparent">
                <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-stone-900 dark:text-slate-100">Create Account</h1>
                <p class="mt-2 text-sm sm:text-base text-stone-600 dark:text-slate-300">Choose account type and continue with registration or login.</p>
            </div>
            <div class="flex border-b border-stone-200 dark:border-slate-700">
                <button type="button" id="reg-tab-monastery" role="tab" aria-selected="{{ $activeTab === 'monastery' ? 'true' : 'false' }}"
                    class="register-tab flex-1 flex items-center justify-center gap-2 px-6 py-4 text-sm font-semibold transition-colors {{ $activeTab === 'monastery' ? 'text-amber-600 dark:text-amber-400 bg-amber-50/80 dark:bg-amber-900/20 border-b-2 border-amber-500' : 'text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 hover:bg-stone-50 dark:hover:bg-slate-700/50' }}">
                    @include('partials.icon', ['name' => 'home', 'class' => 'w-4 h-4'])
                    Monastery
                </button>
                <button type="button" id="reg-tab-sangha" role="tab" aria-selected="{{ $activeTab === 'sangha' ? 'true' : 'false' }}"
                    class="register-tab flex-1 flex items-center justify-center gap-2 px-6 py-4 text-sm font-semibold transition-colors {{ $activeTab === 'sangha' ? 'text-amber-600 dark:text-amber-400 bg-amber-50/80 dark:bg-amber-900/20 border-b-2 border-amber-500' : 'text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 hover:bg-stone-50 dark:hover:bg-slate-700/50' }}">
                    @include('partials.icon', ['name' => 'login', 'class' => 'w-4 h-4'])
                    Sangha
                </button>
            </div>

            <div class="p-6 sm:p-8">
                <div id="reg-panel-monastery" class="{{ $activeTab === 'monastery' ? '' : 'hidden' }}">
                    @if($errors->any() && old('form_type', 'monastery') === 'monastery')
                        <div class="mb-5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('website.register.monastery') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        <input type="hidden" name="form_type" value="monastery">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="monastery_name" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Name *</label>
                                <input type="text" name="name" id="monastery_name" value="{{ old('name') }}" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                            </div>
                            <div>
                                <label for="monastery_username" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Username *</label>
                                <input type="text" name="username" id="monastery_username" value="{{ old('username') }}" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="monastery_password" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Password *</label>
                                <div class="relative" data-password-toggle-ignore="1">
                                    <input type="password" name="password" id="monastery_password" required class="w-full px-4 py-3 pr-12 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 rounded transition-colors w-9 h-9 flex items-center justify-center z-10" aria-label="Show password" tabindex="-1">
                                        <span class="icon-eye absolute inset-0 flex items-center justify-center">@include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])</span>
                                        <span class="icon-eye-off absolute inset-0 flex items-center justify-center" style="display: none">@include('partials.icon', ['name' => 'eye-off', 'class' => 'w-5 h-5'])</span>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="monastery_password_confirmation" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Confirm Password *</label>
                                <div class="relative" data-password-toggle-ignore="1">
                                    <input type="password" name="password_confirmation" id="monastery_password_confirmation" required class="w-full px-4 py-3 pr-12 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 rounded transition-colors w-9 h-9 flex items-center justify-center z-10" aria-label="Show password" tabindex="-1">
                                        <span class="icon-eye absolute inset-0 flex items-center justify-center">@include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])</span>
                                        <span class="icon-eye-off absolute inset-0 flex items-center justify-center" style="display: none">@include('partials.icon', ['name' => 'eye-off', 'class' => 'w-5 h-5'])</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="monastery_region" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Region</label>
                                <input type="text" name="region" id="monastery_region" value="{{ old('region') }}" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                            </div>
                            <div>
                                <label for="monastery_city" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">City</label>
                                <input type="text" name="city" id="monastery_city" value="{{ old('city') }}" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                            </div>
                        </div>
                        <div>
                            <label for="monastery_address" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Address</label>
                            <input type="text" name="address" id="monastery_address" value="{{ old('address') }}" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                        </div>
                        <div>
                            <label for="monastery_phone" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Phone</label>
                            <input type="text" name="phone" id="monastery_phone" value="{{ old('phone') }}" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                        </div>
                        <div>
                            <label for="monastery_description" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Description</label>
                            <textarea name="description" id="monastery_description" rows="3" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">{{ old('description') }}</textarea>
                        </div>

                        @if($monasteryCustomFields->isNotEmpty())
                            <div class="pt-2 border-t border-stone-200 dark:border-slate-700">
                                <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-stone-600 dark:text-slate-300 mb-4">Custom Fields</h3>
                                <div class="space-y-5">
                                    @include('website.partials.custom-fields', ['customFields' => $monasteryCustomFields, 'oldPrefix' => '', 'idPrefix' => 'monastery'])
                                </div>
                            </div>
                        @endif

                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-amber-500 text-white font-semibold hover:bg-amber-600 focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 transition-colors">
                            Register Monastery
                        </button>
                    </form>
                </div>

                <div id="reg-panel-sangha" class="{{ $activeTab === 'sangha' ? '' : 'hidden' }}">
                    @if($errors->any() && old('form_type') === 'sangha')
                        <div class="mb-5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('website.register.sangha') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        <input type="hidden" name="form_type" value="sangha">
                        <div>
                            <label for="sangha_monastery_id" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Monastery *</label>
                            <select name="monastery_id" id="sangha_monastery_id" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                <option value="">Select monastery</option>
                                @foreach($monasteries as $monastery)
                                    <option value="{{ $monastery->id }}" {{ old('monastery_id') == $monastery->id ? 'selected' : '' }}>{{ $monastery->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="sangha_name" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Name *</label>
                                <input type="text" name="name" id="sangha_name" value="{{ old('name') }}" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                            </div>
                            <div>
                                <label for="sangha_username" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Username *</label>
                                <input type="text" name="username" id="sangha_username" value="{{ old('username') }}" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="sangha_password" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Password *</label>
                                <div class="relative" data-password-toggle-ignore="1">
                                    <input type="password" name="password" id="sangha_password" required class="w-full px-4 py-3 pr-12 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 rounded transition-colors w-9 h-9 flex items-center justify-center z-10" aria-label="Show password" tabindex="-1">
                                        <span class="icon-eye absolute inset-0 flex items-center justify-center">@include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])</span>
                                        <span class="icon-eye-off absolute inset-0 flex items-center justify-center" style="display: none">@include('partials.icon', ['name' => 'eye-off', 'class' => 'w-5 h-5'])</span>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="sangha_password_confirmation" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Confirm Password *</label>
                                <div class="relative" data-password-toggle-ignore="1">
                                    <input type="password" name="password_confirmation" id="sangha_password_confirmation" required class="w-full px-4 py-3 pr-12 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 rounded transition-colors w-9 h-9 flex items-center justify-center z-10" aria-label="Show password" tabindex="-1">
                                        <span class="icon-eye absolute inset-0 flex items-center justify-center">@include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])</span>
                                        <span class="icon-eye-off absolute inset-0 flex items-center justify-center" style="display: none">@include('partials.icon', ['name' => 'eye-off', 'class' => 'w-5 h-5'])</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="sangha_exam_id" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Exam (optional)</label>
                            <select name="exam_id" id="sangha_exam_id" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                <option value="">Select exam</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->name }}{{ $exam->exam_date ? ' (' . $exam->exam_date->format('M d, Y') . ')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="sangha_description" class="block text-sm font-medium text-stone-700 dark:text-slate-300 mb-1.5">Description</label>
                            <textarea name="description" id="sangha_description" rows="3" class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">{{ old('description') }}</textarea>
                        </div>

                        @if($sanghaCustomFields->isNotEmpty())
                            <div class="pt-2 border-t border-stone-200 dark:border-slate-700">
                                <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-stone-600 dark:text-slate-300 mb-4">Custom Fields</h3>
                                <div class="space-y-5">
                                    @include('website.partials.custom-fields', ['customFields' => $sanghaCustomFields, 'oldPrefix' => '', 'idPrefix' => 'sangha'])
                                </div>
                            </div>
                        @endif

                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-amber-500 text-white font-semibold hover:bg-amber-600 focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 transition-colors">
                            Register Sangha
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
(function() {
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
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
    });

    var tabs = document.querySelectorAll('.register-tab');
    var monasteryTab = document.getElementById('reg-tab-monastery');
    var sanghaTab = document.getElementById('reg-tab-sangha');
    var monasteryPanel = document.getElementById('reg-panel-monastery');
    var sanghaPanel = document.getElementById('reg-panel-sangha');

    if (!tabs.length || !monasteryPanel || !sanghaPanel) return;

    function activate(activeTab) {
        var isMonastery = activeTab === 'monastery';
        monasteryPanel.classList.toggle('hidden', !isMonastery);
        sanghaPanel.classList.toggle('hidden', isMonastery);

        tabs.forEach(function(tab) {
            var isActive = (isMonastery && tab === monasteryTab) || (!isMonastery && tab === sanghaTab);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            tab.classList.toggle('text-amber-600', isActive);
            tab.classList.toggle('dark:text-amber-400', isActive);
            tab.classList.toggle('bg-amber-50/80', isActive);
            tab.classList.toggle('dark:bg-amber-900/20', isActive);
            tab.classList.toggle('border-b-2', isActive);
            tab.classList.toggle('border-amber-500', isActive);

            tab.classList.toggle('text-stone-500', !isActive);
            tab.classList.toggle('dark:text-slate-400', !isActive);
        });
    }

    monasteryTab.addEventListener('click', function() { activate('monastery'); });
    sanghaTab.addEventListener('click', function() { activate('sangha'); });
})();
</script>
@endsection

