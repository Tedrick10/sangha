@foreach($customFields as $field)
    @php
        $placeholder = $field->placeholder ?: 'Enter ' . $field->name;
        $inputKey = ($oldPrefix ? $oldPrefix . '.' : '') . 'custom_fields.' . $field->slug;
        $value = old($inputKey);
    @endphp
    <div class="space-y-1.5">
        <label for="{{ $idPrefix }}_custom_{{ $field->slug }}" class="block text-sm font-medium text-stone-700 dark:text-slate-300">
            {{ $field->name }}
            @if($field->required)
                <span class="text-red-500">*</span>
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
                    class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 placeholder-stone-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors"
                >{{ $value }}</textarea>
                @break

            @case('number')
                <input type="number"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    value="{{ $value }}"
                    {{ $field->required ? 'required' : '' }}
                    placeholder="{{ $placeholder }}"
                    class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 placeholder-stone-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                @break

            @case('date')
                <input type="date"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    value="{{ $value }}"
                    {{ $field->required ? 'required' : '' }}
                    class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                @break

            @case('time')
                <input type="time"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    value="{{ $value }}"
                    {{ $field->required ? 'required' : '' }}
                    class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                @break

            @case('datetime')
                <input type="datetime-local"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    value="{{ $value }}"
                    {{ $field->required ? 'required' : '' }}
                    class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                @break

            @case('select')
                <select
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    {{ $field->required ? 'required' : '' }}
                    class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                    <option value="">{{ $field->placeholder ?: 'Select ' . $field->name }}</option>
                    @foreach($field->options ?? [] as $option)
                        <option value="{{ $option }}" {{ (string) $value === (string) $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @break

            @case('checkbox')
                <div class="flex items-center gap-2.5 pt-1">
                    <input type="hidden" name="custom_fields[{{ $field->slug }}]" value="0">
                    <input type="checkbox"
                        name="custom_fields[{{ $field->slug }}]"
                        id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                        value="1"
                        {{ old($inputKey) ? 'checked' : '' }}
                        class="rounded border-stone-300 dark:border-slate-500 text-amber-500 focus:ring-amber-500">
                    <span class="text-sm text-stone-600 dark:text-slate-300">Yes</span>
                </div>
                @break

            @case('media')
                <input type="file"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    accept="image/*"
                    {{ $field->required ? 'required' : '' }}
                    class="block w-full text-sm text-stone-600 dark:text-slate-300 file:mr-4 file:rounded-lg file:border-0 file:bg-amber-50 file:px-4 file:py-2.5 file:text-amber-700 file:font-medium hover:file:bg-amber-100 file:transition-colors">
                @break

            @case('document')
                <input type="file"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.txt"
                    {{ $field->required ? 'required' : '' }}
                    class="block w-full text-sm text-stone-600 dark:text-slate-300 file:mr-4 file:rounded-lg file:border-0 file:bg-amber-50 file:px-4 file:py-2.5 file:text-amber-700 file:font-medium hover:file:bg-amber-100 file:transition-colors">
                @break

            @case('video')
                <input type="file"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    accept="video/*"
                    {{ $field->required ? 'required' : '' }}
                    class="block w-full text-sm text-stone-600 dark:text-slate-300 file:mr-4 file:rounded-lg file:border-0 file:bg-amber-50 file:px-4 file:py-2.5 file:text-amber-700 file:font-medium hover:file:bg-amber-100 file:transition-colors">
                @break

            @default
                <input type="text"
                    name="custom_fields[{{ $field->slug }}]"
                    id="{{ $idPrefix }}_custom_{{ $field->slug }}"
                    value="{{ $value }}"
                    {{ $field->required ? 'required' : '' }}
                    placeholder="{{ $placeholder }}"
                    class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-slate-600 bg-stone-50/60 dark:bg-slate-700/50 text-stone-900 dark:text-slate-100 placeholder-stone-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
        @endswitch

        @error($inputKey)
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
@endforeach

