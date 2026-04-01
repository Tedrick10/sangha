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
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">Label *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $customField->name) }}" required class="admin-input" placeholder="e.g. Email, Phone Number">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="type" class="admin-form-label">Field Type *</label>
            <select name="type" id="type" required class="admin-select-input">
                @foreach(\App\Models\CustomField::fieldTypes() as $key => $label)
                    <option value="{{ $key }}" {{ old('type', $customField->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('type')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div id="options-wrap" class="admin-form-group {{ ($customField->type ?? old('type')) === 'select' ? '' : 'hidden' }}">
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
        <div id="placeholder-wrap" class="admin-form-group {{ in_array($customField->type ?? old('type'), ['text','textarea','number']) ? '' : 'hidden' }}">
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
    var placeholderTypes = ['text', 'textarea', 'number'];

    function toggleOptions() {
        optionsWrap.classList.toggle('hidden', typeSelect.value !== 'select');
    }
    function togglePlaceholder() {
        placeholderWrap.classList.toggle('hidden', !placeholderTypes.includes(typeSelect.value));
    }
    toggleOptions();
    togglePlaceholder();
    typeSelect.addEventListener('change', function() { toggleOptions(); togglePlaceholder(); });

    addBtn.addEventListener('click', function() {
        var row = document.createElement('div');
        row.className = 'flex gap-2 items-center option-row';
        row.innerHTML = '<input type="text" name="options[]" placeholder="Option" class="admin-input flex-1"><button type="button" class="remove-option text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 font-medium" title="Remove">✕</button>';
        optionsList.appendChild(row);
        row.querySelector('.remove-option').addEventListener('click', function() { row.remove(); });
    });

    document.getElementById('options-list').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-option')) e.target.closest('.option-row').remove();
    });
});
</script>
@endsection
