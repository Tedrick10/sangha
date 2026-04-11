{{-- Full-width language + theme controls for the mobile/tablet drawer only --}}
@php
    $currentLocale = app()->getLocale();
    $languages = \App\Models\Language::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();
    if (! $languages->contains(fn ($lang) => strtolower($lang->code) === 'en')) {
        $languages->prepend((object) ['code' => 'en', 'name' => 'English', 'flag' => 'GB']);
    }
    $fallbackCountry = [
        'en' => 'GB', 'my' => 'MM', 'mm' => 'MM', 'th' => 'TH', 'si' => 'LK', 'zh' => 'CN', 'pi' => 'IN',
    ];
    $flagFromValue = function (?string $value, string $code = '') use ($fallbackCountry) {
        $flag = trim((string) $value);
        if ($flag === '') {
            $flag = $fallbackCountry[strtolower($code)] ?? '';
        }
        if ($flag !== '' && preg_match('/^[A-Za-z]{2}$/', $flag)) {
            $upper = strtoupper($flag);
            if (function_exists('mb_chr')) {
                return mb_chr(127397 + ord($upper[0]), 'UTF-8') . mb_chr(127397 + ord($upper[1]), 'UTF-8');
            }
        }

        return $flag !== '' ? $flag : '🌐';
    };
    $theme = resolved_app_theme();
    $themeLabels = ['light' => t('theme_light'), 'dark' => t('theme_dark'), 'system' => t('theme_system')];
@endphp
<div class="space-y-5 border-b border-stone-200 dark:border-slate-800 pb-5 mb-1">
    <div>
        <p class="px-1 text-[11px] font-semibold uppercase tracking-wider text-stone-400 dark:text-slate-500 mb-2">{{ t('languages') }}</p>
        <div class="rounded-xl border border-stone-200/90 dark:border-slate-700 bg-stone-50/80 dark:bg-slate-900/50 divide-y divide-stone-200/80 dark:divide-slate-700 overflow-hidden">
            @foreach($languages as $lang)
                @php
                    $langCode = strtolower($lang->code);
                    $isActive = strtolower($currentLocale) === $langCode;
                    $langFlag = $flagFromValue($lang->flag, $lang->code);
                @endphp
                <button type="button" data-locale="{{ $lang->code }}" class="js-app-locale-choice flex w-full items-center justify-between gap-3 px-4 py-3.5 text-left text-sm font-medium transition-colors {{ $isActive ? 'bg-yellow-50 dark:bg-yellow-900/25 text-yellow-900 dark:text-yellow-300' : 'text-stone-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-800' }}">
                        <span class="flex items-center gap-3 min-w-0">
                            <span class="text-lg leading-none shrink-0">{{ $langFlag }}</span>
                            <span class="truncate">{{ $lang->name }}</span>
                        </span>
                        @if($isActive)
                            <svg class="w-5 h-5 shrink-0 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        @endif
                </button>
            @endforeach
        </div>
    </div>
    <div>
        <p class="px-1 text-[11px] font-semibold uppercase tracking-wider text-stone-400 dark:text-slate-500 mb-2">{{ t('drawer_theme_heading', 'Appearance') }}</p>
        <div class="rounded-xl border border-stone-200/90 dark:border-slate-700 bg-white dark:bg-slate-900 py-1 shadow-sm overflow-hidden divide-y divide-stone-100 dark:divide-slate-800">
            @foreach(['light', 'dark', 'system'] as $opt)
                <button type="button" data-website-theme="{{ $opt }}" class="website-theme-choice flex w-full min-h-[52px] items-center justify-between gap-2 px-4 py-3 text-left text-sm text-stone-700 dark:text-slate-200 hover:bg-stone-50 dark:hover:bg-slate-800/90 transition-colors {{ $theme === $opt ? 'bg-yellow-50/90 dark:bg-yellow-900/25 text-yellow-800 dark:text-yellow-300' : '' }}">
                    <span class="flex items-center gap-2.5">
                        @if($opt === 'light')
                            <svg class="w-5 h-5 shrink-0 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        @elseif($opt === 'dark')
                            <svg class="w-5 h-5 shrink-0 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        @else
                            <svg class="w-5 h-5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        @endif
                        <span class="website-theme-choice-label {{ $theme === $opt ? 'font-semibold' : 'font-normal' }}">{{ $themeLabels[$opt] }}</span>
                    </span>
                    <svg class="website-theme-check h-5 w-5 shrink-0 text-yellow-600 dark:text-yellow-400 {{ $theme === $opt ? '' : 'hidden' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </button>
            @endforeach
        </div>
    </div>
</div>
