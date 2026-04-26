@foreach($customFields as $field)
    @php $placeholder = $field->placeholder ?: 'Enter ' . $field->name; @endphp
    <div class="admin-form-group">
        <label for="custom_{{ $field->slug }}" class="admin-form-label">
            {{ $field->name }}
            @if($field->required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        @php $val = $values[$field->slug] ?? old('custom_fields.' . $field->slug); @endphp
        @switch($field->type)
            @case('textarea')
                <textarea
                    name="custom_fields[{{ $field->slug }}]"
                    id="custom_{{ $field->slug }}"
                    rows="3"
                    {{ $field->required ? 'required' : '' }}
                    placeholder="{{ $placeholder }}"
                    class="admin-textarea"
                >{{ $val }}</textarea>
                @break
            @case('number')
                <input type="number"
                    name="custom_fields[{{ $field->slug }}]"
                    id="custom_{{ $field->slug }}"
                    value="{{ $val }}"
                    {{ $field->required ? 'required' : '' }}
                    placeholder="{{ $placeholder }}"
                    class="admin-input">
                @break
            @case('date')
                <input type="date"
                    name="custom_fields[{{ $field->slug }}]"
                    id="custom_{{ $field->slug }}"
                    value="{{ $val }}"
                    {{ $field->required ? 'required' : '' }}
                    class="admin-input">
                @break
            @case('time')
                <input type="time"
                    name="custom_fields[{{ $field->slug }}]"
                    id="custom_{{ $field->slug }}"
                    value="{{ $val }}"
                    {{ $field->required ? 'required' : '' }}
                    class="admin-input">
                @break
            @case('datetime')
                <input type="datetime-local"
                    name="custom_fields[{{ $field->slug }}]"
                    id="custom_{{ $field->slug }}"
                    value="{{ $val }}"
                    {{ $field->required ? 'required' : '' }}
                    class="admin-input">
                @break
            @case('media')
                <input type="file"
                    name="custom_fields[{{ $field->slug }}]"
                    id="custom_{{ $field->slug }}"
                    accept="image/*"
                    {{ $field->required ? 'required' : '' }}
                    class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-amber-50 file:px-4 file:py-2.5 file:text-amber-700 file:font-medium hover:file:bg-amber-100 file:transition-colors">
                @if($val)
                    <p class="mt-1.5 text-sm text-slate-500">
                        Current:
                        @if(isset($sangha) && isset($field))
                            <a href="{{ route('admin.sanghas.custom-field-file', ['sangha' => $sangha->id, 'customField' => $field->id]) }}" target="_blank" rel="noopener noreferrer" class="font-medium text-amber-700 hover:underline dark:text-amber-300">{{ basename($val) }}</a>
                        @else
                            {{ basename($val) }}
                        @endif
                    </p>
                @endif
                @break
            @case('document')
                <input type="file"
                    name="custom_fields[{{ $field->slug }}]"
                    id="custom_{{ $field->slug }}"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.txt"
                    {{ $field->required ? 'required' : '' }}
                    class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-amber-50 file:px-4 file:py-2.5 file:text-amber-700 file:font-medium hover:file:bg-amber-100 file:transition-colors">
                @if($val)
                    <p class="mt-1.5 text-sm text-slate-500">
                        Current:
                        @if(isset($sangha) && isset($field))
                            <a href="{{ route('admin.sanghas.custom-field-file', ['sangha' => $sangha->id, 'customField' => $field->id]) }}" target="_blank" rel="noopener noreferrer" class="font-medium text-amber-700 hover:underline dark:text-amber-300">{{ basename($val) }}</a>
                        @else
                            {{ basename($val) }}
                        @endif
                    </p>
                @endif
                @break
            @case('video')
                <input type="file"
                    name="custom_fields[{{ $field->slug }}]"
                    id="custom_{{ $field->slug }}"
                    accept="video/*"
                    {{ $field->required ? 'required' : '' }}
                    class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-amber-50 file:px-4 file:py-2.5 file:text-amber-700 file:font-medium hover:file:bg-amber-100 file:transition-colors">
                @if($val)
                    <p class="mt-1.5 text-sm text-slate-500">
                        Current:
                        @if(isset($sangha) && isset($field))
                            <a href="{{ route('admin.sanghas.custom-field-file', ['sangha' => $sangha->id, 'customField' => $field->id]) }}" target="_blank" rel="noopener noreferrer" class="font-medium text-amber-700 hover:underline dark:text-amber-300">{{ basename($val) }}</a>
                        @else
                            {{ basename($val) }}
                        @endif
                    </p>
                @endif
                @break
            @case('select')
                <select name="custom_fields[{{ $field->slug }}]"
                    id="custom_{{ $field->slug }}"
                    {{ $field->required ? 'required' : '' }}
                    class="admin-select-input"
                    title="{{ $field->slug === 'level_information' ? t('select_level', 'Select level') : ($field->placeholder ?: 'Select ' . $field->name) }}">
                    <option value="">@if($field->slug === 'level_information'){{ t('select_level', 'Select level') }}@else{{ $field->placeholder ?: 'Select ' . $field->name . '...' }}@endif</option>
                    @foreach($field->options ?? [] as $opt)
                        <option value="{{ $opt }}" {{ $val == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                @break
            @case('monastery_select')
                @php
                    $adminMonasteryList = \App\Models\Monastery::query()
                        ->where('approved', true)
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get(['id', 'name']);
                @endphp
                <select name="custom_fields[{{ $field->slug }}]"
                    id="custom_{{ $field->slug }}"
                    {{ $field->required ? 'required' : '' }}
                    class="admin-select-input">
                    <option value="">{{ $field->placeholder ?: t('select_destination_monastery', 'Select destination monastery') }}</option>
                    @foreach($adminMonasteryList as $mRow)
                        <option value="{{ $mRow->id }}" {{ (string) $val === (string) $mRow->id ? 'selected' : '' }}>{{ $mRow->name }}</option>
                    @endforeach
                </select>
                @break
            @case('checkbox')
                <div class="flex items-center gap-3 mt-2">
                    <input type="hidden" name="custom_fields[{{ $field->slug }}]" value="0">
                    <input type="checkbox"
                        name="custom_fields[{{ $field->slug }}]"
                        id="custom_{{ $field->slug }}"
                        value="1"
                        {{ ($val && $val !== '0') ? 'checked' : '' }}
                        class="admin-checkbox">
                    <label for="custom_{{ $field->slug }}" class="text-sm text-slate-600 font-medium">Yes</label>
                </div>
                @break
            @default
                <input type="text"
                    name="custom_fields[{{ $field->slug }}]"
                    id="custom_{{ $field->slug }}"
                    value="{{ $val }}"
                    {{ $field->required ? 'required' : '' }}
                    placeholder="{{ $placeholder }}"
                    class="admin-input">
        @endswitch
        @error('custom_fields.' . $field->slug)
            <p class="admin-form-error">{{ $message }}</p>
        @enderror
    </div>
@endforeach
