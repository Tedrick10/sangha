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
@endphp
@foreach($customFields as $field)
    @php
        $placeholder = $field->placeholder ?: 'Enter ' . $field->name;
        $inputKey = ($oldPrefix ? $oldPrefix . '.' : '') . 'custom_fields.' . $field->slug;
        $value = old($inputKey);
    @endphp
    <div class="{{ $fieldWrap }}">
        <label for="{{ $idPrefix }}_custom_{{ $field->slug }}" class="{{ $labelClass }}">
            {{ $field->name }}
            @if($field->required)
                <span class="{{ $reqMark }}">*</span>
            @endif
        </label>
        @switch($field->type)
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
                    class="{{ $classInput }}">
                @break

            @case('time')
                <input type="time"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    value="{{ $value }}"
                    {{ $field->required ? 'required' : '' }}
                    class="{{ $classInput }}">
                @break

            @case('datetime')
                <input type="datetime-local"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    value="{{ $value }}"
                    {{ $field->required ? 'required' : '' }}
                    class="{{ $classInput }}">
                @break

            @case('select')
                <select
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    {{ $field->required ? 'required' : '' }}
                    class="{{ $classSelect }}">
                    <option value="">{{ $field->placeholder ?: 'Select ' . $field->name }}</option>
                    @foreach($field->options ?? [] as $option)
                        <option value="{{ $option }}" {{ (string) $value === (string) $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @break

            @case('checkbox')
                <div class="{{ $checkboxRow }}">
                    <input type="hidden" name="custom_fields[{{ $field->slug }}]" value="0">
                    <input type="checkbox"
                        name="custom_fields[{{ $field->slug }}]"
                        id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                        value="1"
                        {{ old($inputKey) ? 'checked' : '' }}
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
