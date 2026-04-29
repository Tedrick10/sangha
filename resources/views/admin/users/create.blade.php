@extends('admin.layout')

@section('title', t('add_user'))

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.users.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) {{ t('users') }}</a>
    <h1 class="admin-page-title">{{ t('add_user') }}</h1>
</div>

<form action="{{ route('admin.users.store') }}" method="POST" class="admin-form-card">
    @csrf
    <div class="space-y-5">
        <div class="admin-form-group">
            <label for="name" class="admin-form-label">{{ t('name') }} *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="admin-input" placeholder="User name">
            @error('name')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="email" class="admin-form-label">{{ t('email') }} *</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="admin-input" placeholder="user@example.com">
            @error('email')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="password" class="admin-form-label">{{ t('password') }} *</label>
            <input type="password" name="password" id="password" required class="admin-input" placeholder="••••••••">
            @error('password')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-form-group">
            <label for="password_confirmation" class="admin-form-label">{{ t('confirm_password') }} *</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required class="admin-input" placeholder="••••••••">
        </div>
        <div class="admin-form-group">
            <label for="role_id" class="admin-form-label">{{ t('role') }}</label>
            <select name="role_id" id="role_id" class="admin-select-input">
                <option value="">{{ t('optional') }} — {{ t('no_role') }}</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            @error('role_id')<p class="admin-form-error">{{ $message }}</p>@enderror
        </div>
        @php
            $teacherRoleId = $roles->firstWhere('name', 'Teacher')?->id;
            $oldScope = old('teacher_scope', []);
        @endphp
        <div class="admin-form-group" id="teacher-scope-block" data-teacher-role-id="{{ $teacherRoleId }}">
            <label class="admin-form-label">{{ t('teacher_score_scope', 'Teacher score entry scope') }}</label>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">{{ t('teacher_score_scope_hint', 'Choose allowed exam types and subjects for this teacher. They can only enter marks for selected combinations.') }}</p>
            <div class="space-y-4 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                @foreach($examTypes as $examType)
                    @php $chosen = array_map('intval', $oldScope[$examType->id] ?? []); @endphp
                    <div class="rounded-lg border border-slate-200/80 dark:border-slate-700 p-3">
                        <h4 class="mb-2 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $examType->name }}</h4>
                        @php $subjectsForType = $examTypeSubjects[$examType->id] ?? []; @endphp
                        @if($subjectsForType === [])
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ t('teacher_scope_no_subjects_for_exam_type', 'No subjects are configured yet for this exam type.') }}</p>
                        @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                            @foreach($subjectsForType as $subject)
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                    <input type="checkbox" class="admin-checkbox" name="teacher_scope[{{ $examType->id }}][]" value="{{ $subject['id'] }}" {{ in_array((int) $subject['id'], $chosen, true) ? 'checked' : '' }}>
                                    <span>{{ $subject['name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
            @error('teacher_scope')<p class="admin-form-error mt-2">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="admin-form-actions mt-6">
        <button type="submit" class="admin-btn-primary">{{ t('create_user') }}</button>
        <a href="{{ route('admin.users.index') }}" class="admin-btn-secondary">{{ t('cancel') }}</a>
    </div>
</form>
<script>
    (function () {
        var roleSelect = document.getElementById('role_id');
        var scopeBlock = document.getElementById('teacher-scope-block');
        if (!roleSelect || !scopeBlock) return;
        var teacherRoleId = String(scopeBlock.getAttribute('data-teacher-role-id') || '');
        function syncVisibility() {
            var isTeacher = teacherRoleId !== '' && String(roleSelect.value || '') === teacherRoleId;
            scopeBlock.style.display = isTeacher ? '' : 'none';
        }
        roleSelect.addEventListener('change', syncVisibility);
        syncVisibility();
    })();
</script>
@endsection
