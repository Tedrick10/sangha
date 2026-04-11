@extends('admin.layout')

@section('title', t('appearance_colors_title', 'Appearance colors'))

@section('content')
@php
    $portalLabels = [
        'website' => t('appearance_portal_website', 'Public website'),
        'admin' => t('appearance_portal_admin', 'Admin dashboard'),
        'monastery' => t('appearance_portal_monastery', 'Monastery dashboard'),
    ];
    $fieldLabels = [
        'button_bg' => t('appearance_button_bg', 'Primary button background'),
        'button_text' => t('appearance_button_text', 'Primary button text'),
        'body_text' => t('appearance_body_text', 'Main text (light)'),
        'muted_text' => t('appearance_muted_text', 'Secondary text (light)'),
        'page_bg' => t('appearance_page_bg', 'Page background'),
        'surface_bg' => t('appearance_surface_bg', 'Panels & header background'),
        'body_text_dark' => t('appearance_body_text_dark', 'Main text (dark mode)'),
        'muted_text_dark' => t('appearance_muted_text_dark', 'Secondary text (dark mode)'),
    ];
    $presetLabels = [
        'website' => [
            'sun' => t('appearance_preset_sun', 'Sun gold'),
            'forest' => t('appearance_preset_forest', 'Forest'),
            'ocean' => t('appearance_preset_ocean', 'Ocean'),
        ],
        'admin' => [
            'ember' => t('appearance_preset_ember', 'Ember'),
            'slate' => t('appearance_preset_slate', 'Slate pro'),
            'violet' => t('appearance_preset_violet', 'Violet'),
        ],
        'monastery' => [
            'amber' => t('appearance_preset_amber', 'Amber'),
            'teal' => t('appearance_preset_teal', 'Teal'),
            'indigo' => t('appearance_preset_indigo', 'Indigo'),
        ],
    ];
    $portalAccent = [
        'website' => 'from-amber-400/90 via-yellow-500/80 to-orange-400/70',
        'admin' => 'from-amber-500/90 via-orange-500/75 to-rose-500/70',
        'monastery' => 'from-sky-500/85 via-cyan-500/75 to-teal-500/70',
    ];
@endphp

<div class="appearance-settings-root relative isolate pb-4">
    <div class="pointer-events-none absolute inset-x-0 -top-8 h-48 bg-gradient-to-b from-amber-500/10 via-transparent to-transparent blur-2xl dark:from-amber-400/15" aria-hidden="true"></div>

    <div class="appearance-settings-hero relative overflow-hidden rounded-2xl border border-amber-200/60 bg-gradient-to-br from-amber-50/95 via-white to-sky-50/90 p-6 shadow-lg shadow-amber-900/5 dark:border-slate-600/60 dark:from-slate-900/95 dark:via-slate-900 dark:to-slate-950 sm:p-8">
        <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-gradient-to-br from-amber-300/40 to-orange-400/20 blur-3xl dark:from-amber-500/20 dark:to-orange-600/10" aria-hidden="true"></div>
        <div class="absolute -bottom-20 -left-10 h-40 w-40 rounded-full bg-gradient-to-tr from-sky-300/30 to-transparent blur-2xl dark:from-sky-500/15" aria-hidden="true"></div>
        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-800/80 dark:text-amber-300/90">{{ t('appearance_hero_kicker', 'Brand & portals') }}</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-stone-900 dark:text-white sm:text-3xl">{{ t('appearance_colors_title', 'Appearance colors') }}</h1>
                <p class="mt-3 text-sm leading-relaxed text-stone-600 dark:text-slate-400">{{ t('appearance_colors_intro_long', 'Tune buttons, typography, backgrounds, and dark-mode text for each surface. Use presets for quick demos, then refine hex values. Leave all fields in a section empty to restore the built-in theme for that area.') }}</p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-2 sm:justify-end">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200/80 bg-white/80 px-3 py-1.5 text-xs font-medium text-amber-900 shadow-sm dark:border-amber-500/30 dark:bg-slate-800/80 dark:text-amber-200">
                    <span class="h-2 w-2 rounded-full bg-amber-500 shadow shadow-amber-500/50" aria-hidden="true"></span>
                    {{ t('appearance_badge_live', 'Live to visitors after save') }}
                </span>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.appearance.update') }}" method="POST" class="mt-8 space-y-8">
        @csrf
        @method('PUT')

        @foreach(['website', 'admin', 'monastery'] as $portalKey)
            @php
                $defs = \App\Support\AppearancePortals::defaults($portalKey);
                $vals = $portals[$portalKey] ?? [];
                $preview = \App\Support\AppearancePortals::mergedPalette($portalKey);
                $presetRow = $appearancePresets[$portalKey] ?? [];
            @endphp
            <div class="appearance-portal-card group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-white/95 shadow-md shadow-slate-200/40 ring-1 ring-slate-900/[0.03] dark:border-slate-700/80 dark:bg-slate-900/60 dark:shadow-black/40 dark:ring-white/[0.04]">
                <div class="pointer-events-none absolute inset-y-0 left-0 w-1 bg-gradient-to-b {{ $portalAccent[$portalKey] }} opacity-90" aria-hidden="true"></div>
                <div class="relative border-b border-slate-100/90 bg-gradient-to-r from-slate-50/80 to-transparent px-5 py-4 dark:border-slate-700/60 dark:from-slate-800/50 sm:px-6 sm:py-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $portalLabels[$portalKey] }}</h2>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ t('appearance_portal_hint', 'Preset fills all slots for this portal; you can still edit each field.') }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($presetRow as $presetKey => $presetColors)
                                <button type="button" class="appearance-preset-btn inline-flex items-center gap-1.5 rounded-full border border-slate-200/90 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-amber-300/80 hover:bg-amber-50/90 hover:text-amber-950 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-amber-500/40 dark:hover:bg-slate-700 dark:hover:text-amber-200"
                                    data-appearance-preset="{{ $portalKey }}"
                                    data-preset-key="{{ $presetKey }}">
                                    <span class="flex -space-x-1" aria-hidden="true">
                                        @foreach(array_slice($presetColors, 0, 3, true) as $c)
                                            <span class="inline-block h-4 w-4 rounded-full border border-white/80 ring-1 ring-black/5 dark:ring-white/10" style="background: {{ $c }}"></span>
                                        @endforeach
                                    </span>
                                    {{ $presetLabels[$portalKey][$presetKey] ?? $presetKey }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 p-5 sm:p-6 lg:grid-cols-12 lg:gap-8">
                    <div class="flex flex-col justify-between rounded-xl border border-slate-200/80 bg-slate-50/50 p-4 dark:border-slate-600/60 dark:bg-slate-950/40 sm:col-span-2 lg:col-span-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ t('appearance_preview_label', 'Preview') }}</p>
                        <div class="mt-3 overflow-hidden rounded-xl border border-slate-200/80 shadow-inner dark:border-slate-600/60" style="background: {{ $preview['page_bg'] }}">
                            <div class="border-b px-3 py-2.5 text-[10px] font-semibold uppercase tracking-wider" style="background: {{ $preview['surface_bg'] }}; color: {{ $preview['muted_text'] }}">{{ t('appearance_preview_sample_bar', 'Sample bar') }}</div>
                            <div class="space-y-2 p-3">
                                <p class="text-sm font-semibold leading-snug" style="color: {{ $preview['body_text'] }}">{{ t('appearance_preview_sample_title', 'Welcome to your portal') }}</p>
                                <p class="text-xs leading-relaxed" style="color: {{ $preview['muted_text'] }}">{{ t('appearance_preview_sample_body', 'Muted supporting text and secondary labels read like this.') }}</p>
                                <button type="button" class="mt-1 inline-flex w-full items-center justify-center rounded-lg px-3 py-2 text-xs font-bold shadow-sm" style="background: {{ $preview['button_bg'] }}; color: {{ $preview['button_text'] }}">{{ t('appearance_preview_sample_cta', 'Primary action') }}</button>
                            </div>
                        </div>
                        <div class="mt-3 overflow-hidden rounded-xl border border-slate-200/80 dark:border-slate-600/60" style="background: color-mix(in srgb, {{ $preview['page_bg'] }} 12%, #020617)">
                            <div class="border-b border-slate-700/40 px-3 py-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ t('appearance_preview_dark_strip', 'Dark preview') }}</div>
                            <div class="space-y-2 p-3">
                                <p class="text-sm font-semibold leading-snug" style="color: {{ $preview['body_text_dark'] }}">{{ t('appearance_preview_sample_title', 'Welcome to your portal') }}</p>
                                <p class="text-xs leading-relaxed" style="color: {{ $preview['muted_text_dark'] }}">{{ t('appearance_preview_sample_body', 'Muted supporting text and secondary labels read like this.') }}</p>
                                <button type="button" class="mt-1 inline-flex w-full items-center justify-center rounded-lg px-3 py-2 text-xs font-bold shadow-sm" style="background: {{ $preview['button_bg'] }}; color: {{ $preview['button_text'] }}">{{ t('appearance_preview_sample_cta', 'Primary action') }}</button>
                            </div>
                        </div>
                        <p class="mt-3 text-[10px] leading-relaxed text-slate-400 dark:text-slate-500">{{ t('appearance_preview_dark_note', 'Dark mode uses the “dark” text colors in the form.') }}</p>
                    </div>

                    <div class="space-y-5 sm:col-span-2 lg:col-span-8">
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach(\App\Support\AppearancePortals::fields() as $fieldKey)
                                @php
                                    $id = "ap_{$portalKey}_{$fieldKey}";
                                    $name = "portals[{$portalKey}][{$fieldKey}]";
                                    $oldKey = 'portals.'.$portalKey.'.'.$fieldKey;
                                    $saved = $vals[$fieldKey] ?? '';
                                    $swatch = $saved !== '' ? $saved : $defs[$fieldKey];
                                @endphp
                                <div class="appearance-field rounded-xl border border-slate-200/70 bg-white/90 p-3 transition group-hover:border-slate-200 dark:border-slate-600/50 dark:bg-slate-800/40">
                                    <label for="{{ $id }}" class="mb-2 block text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $fieldLabels[$fieldKey] }}</label>
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        <input type="color" id="{{ $id }}_pick" class="h-10 w-12 shrink-0 cursor-pointer rounded-lg border border-slate-200 bg-white p-0.5 shadow-sm dark:border-slate-500 dark:bg-slate-700" value="{{ e($swatch) }}" aria-hidden="true" tabindex="-1" data-appearance-sync="{{ $id }}">
                                        <input type="text" name="{{ $name }}" id="{{ $id }}" value="{{ old($oldKey, $saved) }}" placeholder="{{ $defs[$fieldKey] }}" maxlength="7" autocomplete="off"
                                            class="admin-input min-w-0 flex-1 font-mono text-xs uppercase sm:max-w-[9.5rem]"
                                            pattern="#[0-9A-Fa-f]{6}" inputmode="text">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ t('appearance_hex_hint', 'Example: #d97706. Clear a field to use the default for that slot only.') }}</p>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="appearance-settings-footer flex flex-wrap items-center justify-between gap-4 rounded-xl border border-slate-200/80 bg-slate-50/80 px-4 py-4 dark:border-slate-700/60 dark:bg-slate-800/40 sm:px-6">
            <p class="max-w-xl text-xs text-slate-600 dark:text-slate-400">{{ t('appearance_footer_tip', 'Tip: pick readable contrast for buttons and for dark-mode main text on navy backgrounds.') }}</p>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="admin-btn-primary shadow-md shadow-amber-900/10 dark:shadow-black/30">{{ t('save', 'Save') }}</button>
                <a href="{{ route('admin.dashboard') }}" class="admin-btn-secondary">{{ t('cancel') }}</a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    var presets = @json($appearancePresets, JSON_UNESCAPED_SLASHES);

    document.querySelectorAll('[data-appearance-sync]').forEach(function (picker) {
        var targetId = picker.getAttribute('data-appearance-sync');
        var text = document.getElementById(targetId);
        if (!text) return;
        picker.addEventListener('input', function () {
            text.value = picker.value.toLowerCase();
        });
        text.addEventListener('input', function () {
            if (/^#[0-9a-f]{6}$/i.test(text.value)) {
                picker.value = text.value.toLowerCase();
            }
        });
        text.addEventListener('change', function () {
            if (/^#[0-9a-f]{6}$/i.test(text.value)) {
                picker.value = text.value.toLowerCase();
            }
        });
    });

    document.querySelectorAll('[data-appearance-preset]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var portal = btn.getAttribute('data-appearance-preset');
            var key = btn.getAttribute('data-preset-key');
            if (!portal || !key || !presets[portal] || !presets[portal][key]) return;
            var colors = presets[portal][key];
            Object.keys(colors).forEach(function (field) {
                var id = 'ap_' + portal + '_' + field;
                var text = document.getElementById(id);
                var pick = document.getElementById(id + '_pick');
                if (text) text.value = colors[field];
                if (pick && /^#[0-9a-f]{6}$/i.test(colors[field])) pick.value = colors[field].toLowerCase();
            });
        });
    });
})();
</script>
@endpush
@endsection
