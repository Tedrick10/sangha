@php
    $currentLocale = app()->getLocale();
    $languages = \App\Models\Language::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();
    if (! $languages->contains(fn ($lang) => strtolower($lang->code) === 'en')) {
        $languages->prepend((object) ['code' => 'en', 'name' => 'English', 'flag' => 'GB']);
    }
    $currentLanguage = $languages->first(fn ($lang) => strtolower($lang->code) === strtolower($currentLocale));
    $currentLangName = $currentLanguage?->name ?? strtoupper($currentLocale);
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
    $currentFlag = $flagFromValue($currentLanguage?->flag, $currentLanguage?->code ?? $currentLocale);
@endphp
<div class="relative inline-block" id="website-language-dropdown">
    <button type="button" id="website-language-btn" aria-haspopup="true" aria-expanded="false" class="admin-dropdown-trigger flex items-center gap-2">
        <span class="text-base leading-none shrink-0">{{ $currentFlag }}</span>
        <span>{{ $currentLangName }}</span>
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
    </button>
    <div id="website-language-menu" class="admin-dropdown-panel w-48 hidden">
        @foreach($languages as $lang)
            @php
                $langCode = strtolower($lang->code);
                $isActive = strtolower($currentLocale) === $langCode;
                $langFlag = $flagFromValue($lang->flag, $lang->code);
            @endphp
            <form action="{{ route('website.set-locale') }}" method="POST">
                @csrf
                <input type="hidden" name="locale" value="{{ $lang->code }}">
                <button type="submit" class="flex items-center justify-between w-full px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 active:bg-slate-100 dark:active:bg-slate-600/50 transition-colors text-left {{ $isActive ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400' : '' }}">
                    <span class="flex items-center gap-2">
                        <span class="text-lg leading-none">{{ $langFlag }}</span>
                        <span class="{{ $isActive ? 'font-medium' : '' }}">{{ $lang->name }}</span>
                    </span>
                    @if($isActive)
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    @endif
                </button>
            </form>
        @endforeach
    </div>
</div>
<script>
(function() {
    var btn = document.getElementById('website-language-btn');
    var menu = document.getElementById('website-language-menu');
    if (!btn || !menu) return;
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        menu.classList.toggle('hidden');
        var other = document.getElementById('website-theme-menu');
        if (other) other.classList.add('hidden');
    });
    document.addEventListener('click', function() { menu.classList.add('hidden'); });
    menu.addEventListener('click', function(e) { e.stopPropagation(); });
})();
</script>
