@extends('admin.layout')

@section('title', t('mandatory_scores_page_title', 'Mandatory score entry'))

@section('content')
<div class="admin-page-header">
    <h1>{{ t('mandatory_scores_page_title', 'Mandatory score entry') }}</h1>
</div>

<p class="mb-6 max-w-3xl text-sm text-slate-600 dark:text-slate-400">
    {{ t('mandatory_scores_intro', 'Choose an exam and desk number (approved entrance). Filter to load the candidate, enter marks for each subject for that exam, then save.') }}
</p>

<div class="admin-form-card mb-6">
    <form method="GET" action="{{ route('admin.mandatory-scores.index') }}" class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
        <div class="admin-form-group mb-0 min-w-[180px] flex-1">
            <label for="filter_year_id" class="admin-form-label">{{ t('mandatory_scores_grid_year', 'Year') }}</label>
            <select name="year_id" id="filter_year_id" class="admin-select-input w-full">
                <option value="">{{ t('all', 'All') }}</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ (string) ($yearId ?? '') === (string) $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="admin-form-group mb-0 min-w-[200px] flex-1">
            <label for="filter_exam_id" class="admin-form-label">{{ t('exams', 'Exam') }}</label>
            <select name="exam_id" id="filter_exam_id" class="admin-select-input w-full">
                <option value="">{{ t('mandatory_scores_select_exam', 'Select exam') }}</option>
                @foreach($exams as $e)
                    <option value="{{ $e->id }}" {{ (string) ($examId ?? '') === (string) $e->id ? 'selected' : '' }}>
                        {{ $e->name }}{{ $e->exam_date ? ' (' . $e->exam_date->format('M j, Y') . ')' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="admin-form-group mb-0 min-w-[180px] flex-1">
            <label for="filter_desk_number" class="admin-form-label">{{ t('desk_number', 'Desk No.') }}</label>
            <select name="desk_number" id="filter_desk_number" class="admin-select-input w-full">
                <option value="">{{ $examId ? t('mandatory_scores_select_desk', 'Select desk') : t('mandatory_scores_select_exam_first_desk', 'Select an exam first…') }}</option>
                @php
                    $deskOptPrefix = $examForDeskFilter?->desk_number_prefix ?? '';
                    $deskOptPad = fn ($n) => str_pad((string) (int) $n, 6, '0', STR_PAD_LEFT);
                @endphp
                @foreach($deskOptions as $d)
                    <option value="{{ $d->desk_number }}" {{ (string) ($deskNumber ?? '') === (string) $d->desk_number ? 'selected' : '' }}>
                        {{ $deskOptPrefix !== '' ? $deskOptPrefix.$deskOptPad($d->desk_number) : $deskOptPad($d->desk_number) }} — {{ $d->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 shrink-0">
            <button type="submit" class="admin-btn-filter inline-flex items-center gap-2">@include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4']) {{ t('filter', 'Filter') }}</button>
        </div>
    </form>
</div>

@if(session('success'))
    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950 dark:border-amber-800 dark:bg-amber-900/35 dark:text-amber-100">{{ session('success') }}</div>
@endif

@if($filterError)
    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-200">{{ $filterError }}</div>
@endif

@if($sangha && $examModel && $subjects->isNotEmpty())
    <div class="rounded-xl border border-slate-200/80 dark:border-slate-600 bg-white dark:bg-slate-800 p-5 mb-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-3">{{ t('mandatory_scores_candidate', 'Candidate') }}</h2>
        @php
            $deskDisplayPrefix = $examModel->desk_number_prefix ?? '';
            $deskNumPadded = $sangha->desk_number !== null ? str_pad((string) (int) $sangha->desk_number, 6, '0', STR_PAD_LEFT) : null;
            $rollRaw = $sangha->eligible_roll_number;
            $rollDisplay = filled($rollRaw) && ctype_digit(trim((string) $rollRaw))
                ? str_pad(trim((string) $rollRaw), 6, '0', STR_PAD_LEFT)
                : (filled($rollRaw) ? (string) $rollRaw : '—');
        @endphp
        <dl class="grid gap-3 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-slate-500 dark:text-slate-400">{{ t('sanghas', 'Sangha') }}</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">{{ $sangha->name }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 dark:text-slate-400">{{ t('monasteries', 'Monastery') }}</dt>
                <dd class="text-slate-800 dark:text-slate-200">{{ $sangha->monastery->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 dark:text-slate-400">{{ t('roll_number', 'Roll Number') }}</dt>
                <dd class="font-mono text-sm text-slate-800 dark:text-slate-200 break-all tabular-nums">{{ $rollDisplay }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 dark:text-slate-400">{{ t('score_nrc_label', 'NRC number') }}</dt>
                <dd class="font-mono text-sm text-slate-800 dark:text-slate-200 break-words">{{ filled($sangha->nrc_number) ? $sangha->nrc_number : '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 dark:text-slate-400">{{ t('desk_number', 'Desk No.') }}</dt>
                <dd class="font-bold tabular-nums text-amber-700 dark:text-amber-400">{{ $deskNumPadded !== null ? ($deskDisplayPrefix !== '' ? $deskDisplayPrefix.$deskNumPadded : $deskNumPadded) : '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 dark:text-slate-400">{{ t('exams', 'Exam') }}</dt>
                <dd class="text-slate-800 dark:text-slate-200">{{ $examModel->name }}</dd>
            </div>
        </dl>
    </div>

    <form method="POST" action="{{ route('admin.mandatory-scores.store') }}" class="admin-form-card">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $examModel->id }}">
        <input type="hidden" name="sangha_id" value="{{ $sangha->id }}">
        <input type="hidden" name="desk_number" value="{{ $sangha->desk_number }}">

        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">{{ t('mandatory_scores_subject_marks', 'Subject marks') }}</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">{{ t('mandatory_scores_partial_save_hint', 'Leave a field empty to leave that subject unchanged. At least one score is required to save.') }}</p>

        @error('desk_number')
            <p class="admin-form-error mb-4">{{ $message }}</p>
        @enderror

        @error('scores')
            <p class="admin-form-error mb-4">{{ $message }}</p>
        @enderror

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($subjects as $subject)
                @php
                    $existing = $scoresBySubject->get($subject->id);
                    $oldVal = old('scores.'.$subject->id);
                    $val = $oldVal !== null && $oldVal !== '' ? $oldVal : ($existing ? format_number_display($existing->value) : '0');
                @endphp
                <div class="admin-form-group mb-0">
                    <label for="score_{{ $subject->id }}" class="admin-form-label">{{ $subject->name }}</label>
                    <input type="number" name="scores[{{ $subject->id }}]" id="score_{{ $subject->id }}" value="{{ $val }}" step="1" min="0" class="admin-input" placeholder="0" autocomplete="off">
                </div>
            @endforeach
        </div>

        <div class="admin-form-actions mt-8">
            <button type="submit" class="admin-btn-primary">{{ t('save', 'Save') }}</button>
        </div>
    </form>
@endif
@endsection

@push('scripts')
<script>
(function () {
    var MSG_SELECT_EXAM = @json(t('mandatory_scores_select_exam', 'Select exam'));
    var MSG_SELECT_DESK = @json(t('mandatory_scores_select_desk', 'Select desk'));
    var MSG_SELECT_EXAM_FIRST_DESK = @json(t('mandatory_scores_select_exam_first_desk', 'Select an exam first…'));
    var MSG_LOADING = @json(t('mandatory_scores_loading_desks', 'Loading…'));
    var MSG_DESKS_ERROR = @json(t('mandatory_scores_desks_error', 'Could not load desks'));
    var examOptionsUrl = @json(route('admin.mandatory-scores.exam-options'));
    var deskOptionsUrl = @json(route('admin.mandatory-scores.desk-options'));

    function clearExamSelect(examSelect) {
        if (!examSelect) return;
        if (examSelect.tomselect) {
            examSelect.tomselect.clear(true);
        } else {
            examSelect.value = '';
        }
    }

    function examValue(examSelect) {
        if (!examSelect) return '';
        return examSelect.tomselect ? examSelect.tomselect.getValue() : examSelect.value;
    }

    function yearValue(yearSelect) {
        if (!yearSelect) return '';
        return yearSelect.tomselect ? yearSelect.tomselect.getValue() : yearSelect.value;
    }

    function syncSelectTomSelect(selectEl) {
        if (selectEl && selectEl.tomselect) {
            try {
                selectEl.tomselect.sync();
            } catch (e) {}
        }
    }

    /** Native disabled alone does not unlock Tom Select — must call enable()/disable(). */
    function deskSetDisabled(deskSelect, disabled) {
        if (!deskSelect) return;
        if (disabled) {
            deskSelect.setAttribute('disabled', 'disabled');
        } else {
            deskSelect.removeAttribute('disabled');
        }
        if (deskSelect.tomselect) {
            if (disabled) {
                deskSelect.tomselect.disable();
            } else {
                deskSelect.tomselect.enable();
            }
        }
    }

    function reinitDeskSearchable() {
        if (typeof window.sanghaInitSearchableSelects !== 'function') return;
        window.sanghaInitSearchableSelects(document);
    }

    function initMandatoryScoreFilters() {
        var yearSelect = document.getElementById('filter_year_id');
        var examSelect = document.getElementById('filter_exam_id');
        var deskSelect = document.getElementById('filter_desk_number');
        if (!examSelect || !deskSelect) return;

        function rebuildExamOptionsFromJson(exams) {
            /* Tom Select sync() only adds options; it never drops keys removed from <select>.
               Without clearOptions(), old exams stay in the dropdown when the year filter narrows. */
            if (examSelect.tomselect) {
                examSelect.tomselect.clearOptions(function () {
                    return false;
                });
            }
            examSelect.innerHTML = '';
            var emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = MSG_SELECT_EXAM;
            examSelect.appendChild(emptyOpt);
            (exams || []).forEach(function (ex) {
                var o = document.createElement('option');
                o.value = String(ex.id);
                o.textContent = ex.label || '';
                examSelect.appendChild(o);
            });
            syncSelectTomSelect(examSelect);
        }

        function resetDeskEmpty() {
            if (deskSelect.tomselect) {
                deskSelect.tomselect.clear(true);
                deskSelect.tomselect.clearOptions(function () {
                    return false;
                });
            }
            deskSelect.innerHTML = '<option value="">' + MSG_SELECT_EXAM_FIRST_DESK + '</option>';
            deskSetDisabled(deskSelect, false);
            if (deskSelect.tomselect) {
                syncSelectTomSelect(deskSelect);
            } else {
                reinitDeskSearchable();
            }
        }

        function setDeskLoading() {
            if (deskSelect.tomselect) {
                deskSelect.tomselect.clear(true);
                deskSelect.tomselect.clearOptions(function () {
                    return false;
                });
            }
            deskSelect.innerHTML = '<option value="">' + MSG_LOADING + '</option>';
            deskSetDisabled(deskSelect, true);
            syncSelectTomSelect(deskSelect);
        }

        function padDeskSix(n) {
            var num = parseInt(String(n), 10);
            if (isNaN(num) || num < 0) {
                num = 0;
            }
            var s = String(num);
            while (s.length < 6) {
                s = '0' + s;
            }
            return s;
        }

        function fillDesks(desks, selectedDesk, prefix, examIdLocked) {
            prefix = prefix || '';
            if (deskSelect.tomselect) {
                deskSelect.tomselect.clear(true);
                deskSelect.tomselect.clearOptions(function () {
                    return false;
                });
            }
            deskSelect.innerHTML = '<option value="">' + MSG_SELECT_DESK + '</option>';
            desks.forEach(function (d) {
                var opt = document.createElement('option');
                opt.value = String(d.desk_number);
                var padded = padDeskSix(d.desk_number);
                opt.textContent = (prefix ? prefix + padded : padded) + ' — ' + (d.name || '');
                if (selectedDesk !== null && selectedDesk !== undefined && String(selectedDesk) === String(d.desk_number)) {
                    opt.selected = true;
                }
                deskSelect.appendChild(opt);
            });
            var allow = examIdLocked != null && String(examIdLocked) !== '';
            deskSetDisabled(deskSelect, !allow);
            if (deskSelect.tomselect) {
                syncSelectTomSelect(deskSelect);
                if (allow) {
                    deskSelect.tomselect.enable();
                }
            } else if (allow) {
                reinitDeskSearchable();
            }
        }

        function loadDesksForExam(selectedDesk) {
            var examId = examValue(examSelect);
            if (!examId) {
                resetDeskEmpty();
                return;
            }
            var requestExamId = String(examId);
            setDeskLoading();
            fetch(deskOptionsUrl + '?exam_id=' + encodeURIComponent(examId), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
                .then(function (r) {
                    if (!r.ok) {
                        throw new Error('bad response');
                    }
                    return r.json();
                })
                .then(function (data) {
                    if (String(examValue(examSelect) || '') !== requestExamId) {
                        return;
                    }
                    fillDesks(data.desks || [], selectedDesk, data.desk_number_prefix || '', requestExamId);
                })
                .catch(function () {
                    if (deskSelect.tomselect) {
                        deskSelect.tomselect.clear(true);
                        deskSelect.tomselect.clearOptions(function () {
                            return false;
                        });
                    }
                    deskSelect.innerHTML = '<option value="">' + MSG_DESKS_ERROR + '</option>';
                    deskSetDisabled(deskSelect, true);
                    syncSelectTomSelect(deskSelect);
                });
        }

        if (yearSelect) {
            function onYearChanged() {
                clearExamSelect(examSelect);
                resetDeskEmpty();
                var y = yearValue(yearSelect);
                fetch(examOptionsUrl + '?year_id=' + encodeURIComponent(y || ''), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                    .then(function (r) {
                        if (!r.ok) {
                            throw new Error('bad response');
                        }
                        return r.json();
                    })
                    .then(function (data) {
                        rebuildExamOptionsFromJson(data.exams || []);
                    })
                    .catch(function () {
                        rebuildExamOptionsFromJson([]);
                    });
            }
            yearSelect.addEventListener('change', onYearChanged);
        }

        function onExamChanged() {
            loadDesksForExam(null);
        }

        examSelect.addEventListener('change', onExamChanged);
    }

    document.addEventListener('DOMContentLoaded', function () {
        queueMicrotask(initMandatoryScoreFilters);
    });
})();
</script>
@endpush
