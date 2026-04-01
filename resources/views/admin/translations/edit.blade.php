@extends('admin.layout')

@section('title', t('translations_for') . ' ' . $language->name)

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.languages.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) {{ t('languages') }}</a>
    <h1 class="admin-page-title">{{ t('translations_for') }} {{ $language->name }}</h1>
    <p class="text-slate-600 dark:text-slate-400 mt-1">{{ t('edit_translations') }} — {{ count($keys) }} {{ t('key') }}s</p>
</div>

<form action="{{ route('admin.translations.update', $language) }}" method="POST" class="!max-w-none">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/80">
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ t('translations_edit_hint') }}</p>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <label for="trans-filter" class="sr-only">{{ t('search') }}</label>
                <div class="relative flex-1 min-w-[200px] max-w-sm">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    <input type="text" id="trans-filter" placeholder="{{ t('search') }} {{ t('key') }}s…"
                           class="admin-input pl-9 py-2 w-full text-sm"
                           autocomplete="off">
                </div>
                <span id="trans-filter-count" class="text-xs text-slate-500 dark:text-slate-400 hidden"></span>
            </div>
        </div>

        <div class="max-h-[calc(100vh-16rem)] overflow-y-auto p-4 sm:p-5">
            <div id="translations-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($keys as $key => $defaultValue)
                    <div class="translation-card group rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800/50 p-4 hover:border-amber-500/40 dark:hover:border-amber-500/40 hover:shadow-md transition-all duration-200"
                         data-key="{{ $key }}"
                         data-key-lower="{{ strtolower($key) }}"
                         data-default-lower="{{ strtolower($defaultValue) }}">
                        <label for="trans_{{ $key }}" class="block">
                            <p class="text-sm text-slate-600 dark:text-slate-300 mb-2 line-clamp-2" title="{{ $defaultValue }}">{{ Str::limit($defaultValue, 80) }}</p>
                            <input type="text"
                                   name="translations[{{ $key }}]"
                                   id="trans_{{ $key }}"
                                   value="{{ $translations[$key] ?? '' }}"
                                   placeholder="{{ Str::limit($defaultValue, 50) }}"
                                   class="admin-input text-sm w-full py-2">
                        </label>
                        @if(!empty($translationsByLanguage))
                            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-600">
                                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1.5">{{ t('in_other_languages') }}</p>
                                <ul class="space-y-1 text-xs text-slate-600 dark:text-slate-300">
                                    @foreach($translationsByLanguage as $data)
                                        @php $val = $data['values'][$key] ?? null; @endphp
                                        @if($val !== null && $val !== '')
                                            <li class="flex items-start gap-2">
                                                <span class="shrink-0 flex items-center gap-1.5">
                                                    @if($data['language']->flag)
                                                        @if($data['language']->isFlagCountryCode())
                                                            <img src="https://flagcdn.com/w40/{{ strtolower($data['language']->flag) }}.png" alt="" class="w-4 h-3 object-cover rounded-sm" width="16" height="12">
                                                        @else
                                                            <span class="text-base leading-none">{{ $data['language']->flag }}</span>
                                                        @endif
                                                    @endif
                                                    <span class="text-slate-500 dark:text-slate-400 font-medium">{{ $data['language']->name }}:</span>
                                                </span>
                                                <span class="line-clamp-2 break-words text-slate-600 dark:text-slate-300">{{ Str::limit($val, 80) }}</span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                                @php
                                    $hasAny = collect($translationsByLanguage)->contains(fn ($d) => !empty($d['values'][$key] ?? ''));
                                @endphp
                                @if(!$hasAny)
                                    <p class="text-xs text-slate-400 dark:text-slate-500 italic">—</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="admin-form-actions sticky bottom-0 flex flex-wrap items-center gap-3 px-5 py-4 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/95 backdrop-blur-sm">
            <button type="submit" class="admin-btn-primary">@include('partials.icon', ['name' => 'check', 'class' => 'w-4 h-4']) {{ t('save') }}</button>
            <a href="{{ route('admin.languages.index') }}" class="admin-btn-secondary">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) {{ t('cancel') }}</a>
        </div>
    </div>
</form>

@push('scripts')
<script>
(function() {
    var filter = document.getElementById('trans-filter');
    var grid = document.getElementById('translations-grid');
    var cards = grid ? grid.querySelectorAll('.translation-card') : [];
    var countEl = document.getElementById('trans-filter-count');

    function updateFilter() {
        var q = (filter && filter.value || '').trim().toLowerCase();
        var visible = 0;
        cards.forEach(function(card) {
            var show = !q || (card.getAttribute('data-key-lower') || '').indexOf(q) !== -1 || (card.getAttribute('data-default-lower') || '').indexOf(q) !== -1;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        if (countEl) {
            if (q) {
                countEl.classList.remove('hidden');
                countEl.textContent = visible + ' / ' + cards.length;
            } else {
                countEl.classList.add('hidden');
                countEl.textContent = '';
            }
        }
    }

    if (filter) {
        filter.addEventListener('input', updateFilter);
        filter.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { filter.value = ''; updateFilter(); filter.blur(); }
        });
    }
})();
</script>
@endpush
@endsection
