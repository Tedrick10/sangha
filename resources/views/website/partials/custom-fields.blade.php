{{-- Dark-mode calendar/clock icon: inline CSS so production servers without Node/npm still get the fix (Vite build optional). --}}
@once
<style>
    html.dark .cf-native-datetime::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 1;
        width: 1.125rem;
        height: 1.125rem;
        padding: 0;
        background: transparent
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23e2e8f0' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/%3E%3C/svg%3E")
            center / contain no-repeat;
    }
</style>
@endonce
@php
    $variant = $variant ?? null;
    $isMonastery = $variant === 'monastery';
    $fieldWrap = $isMonastery
        ? 'space-y-2 rounded-2xl border border-slate-200/60 bg-white/90 p-4 shadow-sm ring-1 ring-slate-900/[0.04] transition-colors dark:border-slate-700/50 dark:bg-slate-900/50 dark:ring-white/[0.05] dark:shadow-none'
        : 'space-y-1.5';
    $labelClass = $isMonastery
        ? 'block text-[13px] font-semibold tracking-tight text-slate-800 dark:text-slate-100'
        : 'block text-sm font-medium text-stone-700 dark:text-slate-300';
    $reqMark = $isMonastery ? 'ml-0.5 text-[11px] font-bold text-yellow-600 dark:text-yellow-400' : 'text-red-500';
    $classInput = $isMonastery
        ? 'w-full rounded-xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-900 shadow-[inset_0_1px_2px_rgba(15,23,42,0.04)] placeholder:text-slate-400 transition-all focus:border-yellow-400/80 focus:outline-none focus:ring-[3px] focus:ring-yellow-500/18 dark:border-slate-600/70 dark:bg-slate-950/45 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-yellow-500/55 dark:focus:ring-yellow-400/14'
        : 'w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 placeholder-stone-400 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors';
    $classTextarea = $isMonastery
        ? $classInput . ' min-h-[6.25rem] resize-y leading-relaxed'
        : 'w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 placeholder-stone-400 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors';
    $classSelect = $isMonastery ? $classInput : 'w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors';
    $classFile = $isMonastery
        ? 'block w-full cursor-pointer text-sm text-slate-600 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-yellow-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white file:shadow-sm file:shadow-yellow-900/10 file:transition-colors file:hover:bg-yellow-500 dark:text-slate-400 dark:file:bg-yellow-600 dark:file:shadow-yellow-950/25 dark:file:hover:bg-yellow-500'
        : 'block w-full text-sm text-stone-600 dark:text-slate-300 file:mr-4 file:rounded-lg file:border-0 file:bg-yellow-50 file:px-4 file:py-2.5 file:text-yellow-700 file:font-medium hover:file:bg-yellow-100 file:transition-colors';
    $fileDropWrap = $isMonastery ? 'rounded-lg border border-dashed border-slate-300/80 bg-slate-50/50 px-2.5 py-2 dark:border-slate-600/80 dark:bg-slate-950/30' : '';
    $checkboxRow = $isMonastery
        ? 'flex items-center gap-3 rounded-xl border border-slate-200/80 bg-white px-4 py-3.5 dark:border-slate-600/60 dark:bg-slate-950/35'
        : 'flex items-center gap-2.5 pt-1';
    $oldPrefix = $oldPrefix ?? '';
    $customFieldValueDefaults = $customFieldValueDefaults ?? [];
    $monasteryTransferFromName = $monasteryTransferFromName ?? null;
@endphp
@foreach($customFields as $field)
    @php
        $placeholder = $field->placeholder ?: 'Enter ' . $field->name;
        $inputKey = ($oldPrefix ? $oldPrefix . '.' : '') . 'custom_fields.' . $field->slug;
        $defaultVal = array_key_exists($field->slug, $customFieldValueDefaults)
            ? $customFieldValueDefaults[$field->slug]
            : null;
        $value = old($inputKey, $defaultVal);
        if ($isMonastery && ($field->entity_type ?? '') === 'request' && $field->slug === 'transfer_from') {
            $value = old($inputKey, ($defaultVal !== null && $defaultVal !== '') ? $defaultVal : ($monasteryTransferFromName ?? ''));
        }
    @endphp
    <div class="{{ $fieldWrap }}">
        <label for="{{ $idPrefix }}_custom_{{ $field->slug }}" class="{{ $labelClass }}">
            {{ $field->name }}
            @if($field->required)
                <span class="{{ $reqMark }}">*</span>
            @endif
        </label>
        @switch($field->type)
            @case('text')
                @if($isMonastery && ($field->entity_type ?? '') === 'request' && $field->slug === 'transfer_from')
                    <input type="text"
                        name="custom_fields[{{ $field->slug }}]"
                        id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                        value="{{ $value }}"
                        readonly
                        tabindex="-1"
                        class="{{ $classInput }} cursor-default bg-slate-100/80 dark:bg-slate-800/60"
                        aria-readonly="true">
                    <p class="mt-1.5 text-xs leading-relaxed text-slate-500 dark:text-slate-400">{{ t('transfer_from_portal_hint', 'This is your current monastery as shown on your account. Administrators use it as the transfer origin.') }}</p>
                @else
                    <input type="text"
                        name="custom_fields[{{ $field->slug }}]"
                        id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                        value="{{ $value }}"
                        {{ $field->required ? 'required' : '' }}
                        placeholder="{{ $placeholder }}"
                        class="{{ $classInput }}">
                @endif
                @break

            @case('textarea')
                <textarea
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    rows="3"
                    {{ $field->required ? 'required' : '' }}
                    placeholder="{{ $placeholder }}"
                    class="{{ $classTextarea }}"
                >{{ $value }}</textarea>
                @break

            @case('number')
                <input type="number"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    value="{{ $value }}"
                    {{ $field->required ? 'required' : '' }}
                    placeholder="{{ $placeholder }}"
                    class="{{ $classInput }}">
                @break

            @case('date')
                <input type="date"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    value="{{ $value }}"
                    {{ $field->required ? 'required' : '' }}
                    class="cf-native-datetime {{ $classInput }}">
                @break

            @case('time')
                <input type="time"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    value="{{ $value }}"
                    {{ $field->required ? 'required' : '' }}
                    class="cf-native-datetime {{ $classInput }}">
                @break

            @case('datetime')
                <input type="datetime-local"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    value="{{ $value }}"
                    {{ $field->required ? 'required' : '' }}
                    class="cf-native-datetime {{ $classInput }}">
                @break

            @case('select')
                @php
                    $isExamYearField = $isMonastery
                        && ($field->entity_type ?? null) === 'monastery_exam'
                        && $field->slug === 'exam_year';
                    $catalogYears = $monasteryExamCatalogYears ?? [];
                @endphp
                <select
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    @if($isExamYearField) data-no-select-search="1" @endif
                    {{ $field->required ? 'required' : '' }}
                    class="{{ $classSelect }}">
                    <option value="">{{ $field->placeholder ?: 'Select ' . $field->name }}</option>
                    @if($isExamYearField)
                        @foreach($catalogYears as $yr)
                            <option value="{{ $yr }}" {{ (string) $value === (string) $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    @else
                        @foreach($field->options ?? [] as $option)
                            <option value="{{ $option }}" {{ (string) $value === (string) $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    @endif
                </select>
                @if($isExamYearField && $catalogYears === [])
                    <p class="mt-2 text-xs text-amber-800 dark:text-amber-200/90">{{ t('monastery_exam_catalog_empty', 'No active exams with dates are configured for this programme in the admin panel yet.') }}</p>
                @endif
                @break

            @case('dependent_select')
                @php
                    $parentSlug = \App\Models\CustomField::dependentSelectParentSlug($field);
                    $parentId = $parentSlug ? ($idPrefix.'_custom_'.$parentSlug) : '';
                    $isExamSessionField = $isMonastery
                        && ($field->entity_type ?? null) === 'monastery_exam'
                        && $field->slug === 'exam_session';
                    $catalogByYear = $monasteryExamCatalogByYear ?? [];
                    $depMap = $isExamSessionField
                        ? $catalogByYear
                        : (is_array($field->options) ? $field->options : []);
                    $depPlaceholder = $field->placeholder ?: t('select_exam', 'Select exam');
                    $initialDep = is_scalar($value) || $value === null ? (string) $value : '';
                    $depUseIds = $isExamSessionField;
                @endphp
                @if($parentSlug)
                    <select
                        name="custom_fields[{{ $field->slug }}]"
                        id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                        class="{{ $classSelect }}"
                        data-no-select-search="1"
                        data-dependent-select="1"
                        data-parent-id="{{ $parentId }}"
                        data-options-map='@json($depMap, JSON_UNESCAPED_UNICODE)'
                        data-options-use-ids="{{ $depUseIds ? '1' : '0' }}"
                        data-placeholder-label="{{ e($depPlaceholder) }}"
                        data-initial-value="{{ e($initialDep) }}">
                        <option value="">{{ $depPlaceholder }}</option>
                    </select>
                    @if($isExamSessionField && $catalogByYear === [])
                        <p class="mt-2 text-xs text-amber-800 dark:text-amber-200/90">{{ t('monastery_exam_catalog_empty', 'No active exams with dates are configured for this programme in the admin panel yet.') }}</p>
                    @endif
                @endif
                @break

            @case('approved_sangha')
                @php
                    $approvedList = $monasteryApprovedSanghasForExam ?? collect();
                @endphp
                <select
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    {{ $field->required ? 'required' : '' }}
                    class="{{ $classSelect }}">
                    <option value="">{{ $field->placeholder ?: t('select_approved_student', 'Select an approved student') }}</option>
                    @foreach($approvedList as $sanghaRow)
                        @php
                            $optLabel = $sanghaRow->name;
                            if (filled($sanghaRow->username ?? null)) {
                                $optLabel .= ' ('.$sanghaRow->username.')';
                            }
                            if ($sanghaRow->relationLoaded('exam') && $sanghaRow->exam) {
                                $optLabel .= ' — '.$sanghaRow->exam->name;
                            }
                        @endphp
                        <option value="{{ $sanghaRow->id }}" {{ (string) $value === (string) $sanghaRow->id ? 'selected' : '' }}>{{ $optLabel }}</option>
                    @endforeach
                </select>
                @if($approvedList->isEmpty())
                    <p class="mt-2 text-xs text-amber-800 dark:text-amber-200/90">
                        @if(($field->entity_type ?? '') === 'request')
                            {{ t('no_approved_sanghas_for_transfer', 'No approved sangha members yet. Register sanghas in this portal and wait for administrator approval before choosing who to transfer.') }}
                        @else
                            {{ t('no_approved_students_for_exam_form', 'No approved students are registered for this monastery yet. Register and wait for approval first.') }}
                        @endif
                    </p>
                @endif
                @break

            @case('checkbox')
                @php
                    $cbChecked = $value === true || $value === 1 || $value === '1' || $value === 'true';
                @endphp
                <div class="{{ $checkboxRow }}">
                    <input type="hidden" name="custom_fields[{{ $field->slug }}]" value="0">
                    <input type="checkbox"
                        name="custom_fields[{{ $field->slug }}]"
                        id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                        value="1"
                        {{ $cbChecked ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-slate-300 text-yellow-600 focus:ring-yellow-500/30 dark:border-slate-500 dark:text-yellow-500">
                    <span class="text-sm text-stone-600 dark:text-slate-300">Yes</span>
                </div>
                @break

            @case('media')
                @if($isMonastery)
                    <div class="{{ $fileDropWrap }}">
                        <input type="file"
                            name="custom_fields[{{ $field->slug }}]"
                            id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                            accept="image/*"
                            {{ $field->required ? 'required' : '' }}
                            class="{{ $classFile }}">
                    </div>
                @else
                    <input type="file"
                        name="custom_fields[{{ $field->slug }}]"
                        id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                        accept="image/*"
                        {{ $field->required ? 'required' : '' }}
                        class="{{ $classFile }}">
                @endif
                @break

            @case('document')
                @if($isMonastery)
                    <div class="{{ $fileDropWrap }}">
                        <input type="file"
                            name="custom_fields[{{ $field->slug }}]"
                            id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.txt"
                            {{ $field->required ? 'required' : '' }}
                            class="{{ $classFile }}">
                    </div>
                @else
                    <input type="file"
                        name="custom_fields[{{ $field->slug }}]"
                        id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.txt"
                        {{ $field->required ? 'required' : '' }}
                        class="{{ $classFile }}">
                @endif
                @break

            @case('video')
                @if($isMonastery)
                    <div class="{{ $fileDropWrap }}">
                        <input type="file"
                            name="custom_fields[{{ $field->slug }}]"
                            id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                            accept="video/*"
                            {{ $field->required ? 'required' : '' }}
                            class="{{ $classFile }}">
                    </div>
                @else
                    <input type="file"
                        name="custom_fields[{{ $field->slug }}]"
                        id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                        accept="video/*"
                        {{ $field->required ? 'required' : '' }}
                        class="{{ $classFile }}">
                @endif
                @break

            @default
                <input type="text"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    value="{{ $value }}"
                    {{ $field->required ? 'required' : '' }}
                    placeholder="{{ $placeholder }}"
                    class="{{ $classInput }}">
        @endswitch

        @error($inputKey)
            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
@endforeach

@if($customFields->contains(fn ($f) => $f->type === 'dependent_select'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('select[data-dependent-select]').forEach(function(child) {
        var parentId = child.getAttribute('data-parent-id');
        var parent = parentId ? document.getElementById(parentId) : null;
        var map = {};
        try {
            map = JSON.parse(child.getAttribute('data-options-map') || '{}');
        } catch (e) {
            map = {};
        }
        var ph = child.getAttribute('data-placeholder-label') || 'Select';
        var initial = child.getAttribute('data-initial-value') || '';

        function stripTomSelect(selectEl) {
            if (selectEl && selectEl.tomselect) {
                try {
                    selectEl.tomselect.destroy();
                } catch (e) {}
                delete selectEl.tomselect;
            }
        }

        function sync() {
            stripTomSelect(child);
            child.innerHTML = '';
            var opt0 = document.createElement('option');
            opt0.value = '';
            opt0.textContent = ph;
            child.appendChild(opt0);
            var y = parent && parent.value ? String(parent.value) : '';
            var list = (y && Object.prototype.hasOwnProperty.call(map, y)) ? map[y] : [];
            if (!Array.isArray(list)) {
                list = [];
            }
            var useIds = child.getAttribute('data-options-use-ids') === '1';
            list.forEach(function(item) {
                var val, text;
                if (useIds && item && typeof item === 'object' && ('id' in item)) {
                    val = String(item.id);
                    text = item.name != null ? String(item.name) : val;
                } else {
                    val = String(item);
                    text = String(item);
                }
                var o = document.createElement('option');
                o.value = val;
                o.textContent = text;
                child.appendChild(o);
            });
            if (initial) {
                var ok = list.some(function(x) {
                    if (useIds && x && typeof x === 'object' && ('id' in x)) {
                        return String(x.id) === String(initial);
                    }
                    return String(x) === String(initial);
                });
                if (ok) {
                    child.value = initial;
                }
            }
        }

        if (parent) {
            parent.addEventListener('change', sync);
        }
        sync();
    });
});
</script>
@endif
