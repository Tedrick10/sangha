@extends('admin.layout')

@section('title', 'Edit Sangha')

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.sanghas.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Sanghas</a>
    <h1 class="admin-page-title">Edit Sangha</h1>
</div>

@php
    $metaMon = $sanghaFieldMeta->get('monastery_id');
    $metaName = $sanghaFieldMeta->get('name');
    $metaFather = $sanghaFieldMeta->get('father_name');
    $metaNrc = $sanghaFieldMeta->get('nrc_number');
    $metaExam = $sanghaFieldMeta->get('exam_id');
    $metaDesc = $sanghaFieldMeta->get('description');
    $optionalPlaceholderLabel = t('score_optional_placeholder', 'Optional');
    $adminFatherPlaceholder = $metaFather?->placeholder;
    if ($adminFatherPlaceholder === null || $adminFatherPlaceholder === '' || $adminFatherPlaceholder === 'Optional' || $adminFatherPlaceholder === $optionalPlaceholderLabel) {
        $adminFatherPlaceholder = t('enter_father_name', 'Enter father name');
    }
    $adminNrcPlaceholder = $metaNrc?->placeholder;
    if ($adminNrcPlaceholder === null || $adminNrcPlaceholder === '' || $adminNrcPlaceholder === 'Optional' || $adminNrcPlaceholder === $optionalPlaceholderLabel) {
        $adminNrcPlaceholder = t('enter_nrc_number', 'Enter NRC number');
    }
@endphp
<form action="{{ route('admin.sanghas.update', $sangha) }}" method="POST" enctype="multipart/form-data" class="admin-form-card">
    @csrf
    @method('PUT')
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="monastery_id" class="admin-form-label">{{ $metaMon?->name ?? 'Monastery' }}{{ ($metaMon?->required ?? true) ? ' *' : '' }}</label>
            <select name="monastery_id" id="monastery_id" class="admin-select-input" @if($metaMon?->required ?? true) required @endif>
                @foreach($monasteries as $m)
                    <option value="{{ $m->id }}" {{ old('monastery_id', $sangha->monastery_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
            @error('monastery_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'name'))
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">{{ $metaName?->name ?? t('name') }}{{ ($metaName?->required ?? true) ? ' *' : '' }}</label>
            <input type="text" name="name" id="name" value="{{ old('name', $sangha->name) }}" class="admin-input" placeholder="{{ $metaName?->placeholder ?: 'e.g. U Sitagu' }}" @if($metaName?->required ?? true) required @endif>
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        @endif
        @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'father_name') || !\App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'nrc_number'))
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'father_name'))
            <div class="admin-form-group mb-0">
                <label for="father_name" class="admin-form-label">{{ $metaFather?->name ?? t('score_father_name_label', 'Father name') }} *</label>
                <input type="text" name="father_name" id="father_name" value="{{ old('father_name', $sangha->father_name) }}" maxlength="255" class="admin-input" placeholder="{{ $adminFatherPlaceholder }}" required>
                @error('father_name')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
            @endif
            @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'nrc_number'))
            <div class="admin-form-group mb-0">
                <label for="nrc_number" class="admin-form-label">{{ $metaNrc?->name ?? t('score_nrc_label', 'NRC number') }} *</label>
                <input type="text" name="nrc_number" id="nrc_number" value="{{ old('nrc_number', $sangha->nrc_number) }}" maxlength="100" class="admin-input" placeholder="{{ $adminNrcPlaceholder }}" required>
                @error('nrc_number')<p class="admin-form-error">{{ $message }}</p>@enderror
            </div>
            @endif
        </div>
        @endif
        <div class="admin-form-group">
            <label for="eligible_roll_number" class="admin-form-label">{{ t('roll_number', 'Roll Number') }}</label>
            <input type="text" id="eligible_roll_number" value="{{ $sangha->eligible_roll_number ?: '—' }}" class="admin-input" readonly>
        </div>
        @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'exam_id'))
        <div class="admin-form-group">
            <label for="exam_id" class="admin-form-label">{{ $metaExam?->name ?? t('exam') }} *</label>
            <select name="exam_id" id="exam_id" class="admin-select-input" required>
                <option value="">{{ t('select_exam', 'Select Exam') }}</option>
                @foreach($exams as $exam)
                    <option value="{{ $exam->id }}" {{ old('exam_id', $sangha->exam_id) == $exam->id ? 'selected' : '' }}>{{ $exam->name }}{{ $exam->exam_date ? ' (' . $exam->exam_date->format('M d, Y') . ')' : '' }}</option>
                @endforeach
            </select>
            @error('exam_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        @endif
        @if(($showSanghaExtraFields ?? true) && $customFields->isNotEmpty())
            <div class="admin-form-section">
                <h3 class="admin-form-section-title">Custom Fields</h3>
                <div class="space-y-5">
                    @include('admin.partials.custom-fields-form', ['customFields' => $customFields, 'values' => $customFieldValues, 'sangha' => $sangha])
                </div>
            </div>
        @endif
        @if(($programmeCustomFields ?? collect())->isNotEmpty())
            <div class="admin-form-section">
                <h3 class="admin-form-section-title">{{ t('programme_fields', 'Programme fields') }}</h3>
                <div class="space-y-5">
                    @include('admin.partials.custom-fields-form', ['customFields' => $programmeCustomFields, 'values' => $programmeCustomFieldValues ?? [], 'sangha' => $sangha])
                </div>
            </div>
        @endif
        @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'description'))
        <div class="admin-form-group">
            <label for="description" class="admin-form-label">{{ $metaDesc?->name ?? t('description') }}{{ ($metaDesc?->required ?? false) ? ' *' : '' }}</label>
            <textarea name="description" id="description" rows="3" class="admin-textarea" placeholder="{{ $metaDesc?->placeholder ?? t('sangha_description_placeholder', 'Brief description of the sangha') }}" @if($metaDesc?->required ?? false) required @endif>{{ old('description', $sangha->description) }}</textarea>
            @error('description')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        @endif
        <div class="admin-form-group">
            <label for="moderation_status" class="admin-form-label">Status</label>
            @php
                $moderationStatus = old('moderation_status', $sangha->moderationStatus());
            @endphp
            <select name="moderation_status" id="moderation_status" class="admin-select-input">
                <option value="eligible" {{ $moderationStatus === 'eligible' ? 'selected' : '' }}>Eligible</option>
                <option value="pending" {{ $moderationStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $moderationStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="needed_update" {{ $moderationStatus === 'needed_update' ? 'selected' : '' }}>{{ t('status_needed_update', 'Needed Update') }}</option>
                <option value="rejected" {{ $moderationStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            @error('moderation_status')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group" id="rejection-reason-wrap">
            <label for="rejection_reason" class="admin-form-label" id="rejection_reason_label">{{ t('feedback_reason', 'Reason') }}</label>
            <textarea name="rejection_reason" id="rejection_reason" rows="3" class="admin-textarea" placeholder="{{ t('feedback_reason_placeholder', 'Explain what needs to be updated (or why this registration is rejected)') }}">{{ old('rejection_reason', $sangha->rejection_reason) }}</textarea>
            @error('rejection_reason')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="admin-form-actions">
        <button type="submit" class="admin-btn-primary">Update Sangha</button>
        <a href="{{ route('admin.sanghas.index') }}" class="admin-btn-secondary">Cancel</a>
    </div>
</form>
<script>
(function() {
    var statusEl = document.getElementById('moderation_status');
    var reasonWrap = document.getElementById('rejection-reason-wrap');
    var reasonEl = document.getElementById('rejection_reason');
    var reasonLabel = document.getElementById('rejection_reason_label');
    if (!statusEl || !reasonWrap || !reasonEl) return;
    function refresh() {
        var rejected = statusEl.value === 'rejected';
        var needsUpdate = statusEl.value === 'needed_update';
        var showReason = rejected || needsUpdate;
        reasonWrap.classList.toggle('hidden', !showReason);
        reasonEl.required = showReason;
        if (reasonLabel) {
            reasonLabel.textContent = rejected ? 'Rejection Reason' : (needsUpdate ? 'Needed Update Reason' : 'Reason');
        }
    }
    statusEl.addEventListener('change', refresh);
    refresh();
})();
</script>
@endsection
