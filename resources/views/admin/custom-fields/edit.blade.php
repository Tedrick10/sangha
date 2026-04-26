@extends('admin.layout')

@section('title', 'Edit Custom Field for ' . (\App\Models\CustomField::entityTypes()[$customField->entity_type] ?? 'Monastery'))

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.custom-fields.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Custom Fields</a>
    <h1 class="admin-page-title">Edit Custom Field for {{ \App\Models\CustomField::entityTypes()[$customField->entity_type] ?? 'Monastery' }}</h1>
</div>

<form action="{{ route('admin.custom-fields.update', $customField) }}" method="POST" class="admin-form-card">
    @csrf
    @method('PUT')
    <div class="space-y-5">
        <input type="hidden" name="entity_type" value="{{ old('entity_type', $customField->entity_type) }}">
        @if($customField->is_built_in)
            <p class="text-sm text-slate-600 dark:text-slate-400 rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/40 px-4 py-3">
                <span class="font-medium text-slate-800 dark:text-slate-200">Built-in field</span>
                — internal key <code class="text-xs bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 rounded">{{ $customField->slug }}</code> is fixed; you can change the label, type, placeholder, and required below.
            </p>
        @endif
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">Label *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $customField->name) }}" required class="admin-input" placeholder="e.g. Email, Phone Number">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="type" class="admin-form-label">Field Type *</label>
            @if($customField->is_built_in && in_array($customField->slug, ['approved_sangha_id', 'transfer_sangha_id'], true))
                <input type="hidden" name="type" value="approved_sangha">
                <p class="text-sm text-slate-700 dark:text-slate-300 rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/40 px-4 py-3">
                    {{ \App\Models\CustomField::fieldTypes()['approved_sangha'] ?? 'Approved student' }}
                    <span class="block mt-1 text-xs text-slate-500 dark:text-slate-400">
                        @if($customField->slug === 'transfer_sangha_id')
                            This type is fixed; the monastery portal lists only approved sangha members registered to that monastery.
                        @else
                            This type is fixed; options are loaded from approved students for each monastery.
                        @endif
                    </span>
                </p>
            @elseif($customField->is_built_in && $customField->slug === 'transfer_to')
                <input type="hidden" name="type" value="monastery_select">
                <p class="text-sm text-slate-700 dark:text-slate-300 rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/40 px-4 py-3">
                    {{ \App\Models\CustomField::fieldTypes()['monastery_select'] ?? 'Monastery (dropdown)' }}
                    <span class="block mt-1 text-xs text-slate-500 dark:text-slate-400">This type is fixed; the monastery portal lists approved, active monasteries other than the one submitting the transfer.</span>
                </p>
            @elseif($customField->is_built_in && $customField->slug === 'exam_session')
                <input type="hidden" name="type" value="dependent_select">
                <p class="text-sm text-slate-700 dark:text-slate-300 rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/40 px-4 py-3">
                    {{ \App\Models\CustomField::fieldTypes()['dependent_select'] ?? 'Dependent select' }}
                    <span class="block mt-1 text-xs text-slate-500 dark:text-slate-400">Exam choices are all <strong>active</strong> exams (with a date) from <strong>Examinations → Exams</strong>, grouped by exam date year. The submission is filed under the programme of the exam you select when possible.</span>
                </p>
            @else
                <select name="type" id="type" required class="admin-select-input">
                    @foreach(\App\Models\CustomField::fieldTypes() as $key => $label)
                        @if($key === 'dependent_select')
                            @continue
                        @endif
                        @if($key === 'monastery_select' && $customField->entity_type !== 'request')
                            @continue
                        @endif
                        <option value="{{ $key }}" {{ old('type', $customField->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            @endif
            @error('type')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        @if($customField->is_built_in && $customField->slug === 'exam_year')
            <p class="text-sm text-slate-600 dark:text-slate-400 rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/40 px-4 py-3">
                <span class="font-medium text-slate-800 dark:text-slate-200">Data source</span>
                <span class="block mt-1 text-xs text-slate-500 dark:text-slate-400">Year options come from calendar years of all <strong>active</strong> exams (with a date) in <strong>Examinations → Exams</strong>. You do not maintain a manual option list here.</span>
            </p>
        @endif
        <div id="options-wrap" class="admin-form-group {{ ($customField->is_built_in && in_array($customField->slug, ['approved_sangha_id', 'transfer_sangha_id', 'transfer_to', 'exam_session', 'exam_year'], true)) ? 'hidden' : (($customField->type ?? old('type')) === 'select' ? '' : 'hidden') }}">
            <label class="admin-form-label mb-2 block">Options</label>
            <div id="options-list" class="space-y-2">
                @php $opts = old('options', $customField->options ?? []); $opts = is_array($opts) ? $opts : (array_filter(explode("\n", $opts)) ?: ['']); @endphp
                @foreach($opts as $opt)
                <div class="flex gap-2 items-center option-row">
                    <input type="text" name="options[]" value="{{ is_string($opt) ? $opt : '' }}" placeholder="Option" class="admin-input flex-1">
                    <button type="button" class="remove-option text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 font-medium" title="Remove">✕</button>
                </div>
                @endforeach
            </div>
            <button type="button" id="add-option" class="mt-2 inline-flex items-center gap-1 text-sm text-amber-600 hover:text-amber-700 font-semibold">
                <span class="text-lg">+</span> Add option
            </button>
            @error('options')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div id="placeholder-wrap" class="admin-form-group {{ in_array($customField->type ?? old('type'), ['text','textarea','number','approved_sangha','dependent_select','monastery_select'], true) ? '' : 'hidden' }}">
            <label for="placeholder" class="admin-form-label">Placeholder</label>
            <input type="text" name="placeholder" id="placeholder" value="{{ old('placeholder', $customField->placeholder) }}" class="admin-input" placeholder="e.g. Enter your email">
            @error('placeholder')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-3 pt-2">
            <input type="checkbox" name="required" id="required" value="1" {{ old('required', $customField->required) ? 'checked' : '' }} class="admin-checkbox">
            <label for="required" class="text-sm font-medium text-slate-700">Required</label>
        </div>
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">Update Custom Field</button>
        <a href="{{ route('admin.custom-fields.index') }}" class="admin-btn-secondary">Cancel</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var typeSelect = document.getElementById('type');
    var optionsWrap = document.getElementById('options-wrap');
    var optionsList = document.getElementById('options-list');
    var addBtn = document.getElementById('add-option');

    var placeholderWrap = document.getElementById('placeholder-wrap');
    var placeholderTypes = ['text', 'textarea', 'number', 'approved_sangha', 'dependent_select', 'monastery_select'];

    function toggleOptions() {
        if (!typeSelect || !optionsWrap) return;
        optionsWrap.classList.toggle('hidden', typeSelect.value !== 'select');
    }
    function togglePlaceholder() {
        if (!typeSelect || !placeholderWrap) return;
        placeholderWrap.classList.toggle('hidden', !placeholderTypes.includes(typeSelect.value));
    }
    if (typeSelect) {
        toggleOptions();
        togglePlaceholder();
        typeSelect.addEventListener('change', function() { toggleOptions(); togglePlaceholder(); });
    }

    if (addBtn && optionsList) {
        addBtn.addEventListener('click', function() {
            var row = document.createElement('div');
            row.className = 'flex gap-2 items-center option-row';
            row.innerHTML = '<input type="text" name="options[]" placeholder="Option" class="admin-input flex-1"><button type="button" class="remove-option text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 font-medium" title="Remove">✕</button>';
            optionsList.appendChild(row);
            row.querySelector('.remove-option').addEventListener('click', function() { row.remove(); });
        });

        optionsList.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-option')) e.target.closest('.option-row').remove();
        });
    }
});
</script>
@endsection
