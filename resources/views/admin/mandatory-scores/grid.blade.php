@extends('admin.layout')

@section('title', t('mandatory_scores_grid_title', 'Exam Mark Entry'))

@section('content')
<div class="admin-page-header">
    <h1>{{ t('mandatory_scores_grid_title', 'Exam Mark Entry') }}</h1>
</div>

<p class="mb-6 max-w-3xl text-sm text-slate-600 dark:text-slate-400">
    {{ t('mandatory_scores_grid_intro', 'Select an examination to show every seated desk in one table. Enter marks per subject and confirm each row to save (same rules as single-desk entry).') }}
</p>

<div class="admin-form-card mb-6">
    <form method="GET" action="{{ route('admin.mandatory-scores.grid') }}" class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
        <div class="admin-form-group mb-0 min-w-[180px] flex-1">
            <label for="grid_year_id" class="admin-form-label">{{ t('mandatory_scores_grid_year', 'Year') }}</label>
            <select name="year_id" id="grid_year_id" class="admin-select-input w-full">
                <option value="">{{ t('all', 'All') }}</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ (string) ($yearId ?? '') === (string) $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="admin-form-group mb-0 min-w-[200px] flex-1">
            <label for="grid_exam_type_id" class="admin-form-label">{{ t('mandatory_scores_grid_exam_type', 'Exam type') }}</label>
            <select name="exam_type_id" id="grid_exam_type_id" class="admin-select-input w-full">
                <option value="">{{ t('all', 'All') }}</option>
                @foreach($examTypes as $et)
                    <option value="{{ $et->id }}" {{ (string) ($examTypeId ?? '') === (string) $et->id ? 'selected' : '' }}>{{ $et->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="admin-form-group mb-0 min-w-[240px] flex-1">
            <label for="grid_exam_id" class="admin-form-label">{{ t('exams', 'Exam') }}</label>
            <select name="exam_id" id="grid_exam_id" class="admin-select-input w-full">
                <option value="">{{ t('mandatory_scores_select_exam', 'Select exam') }}</option>
                @foreach($exams as $e)
                    <option value="{{ $e->id }}" {{ (string) ($examId ?? '') === (string) $e->id ? 'selected' : '' }}>
                        {{ $e->name }}{{ $e->exam_date ? ' (' . $e->exam_date->format('M j, Y') . ')' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 shrink-0">
            <button type="submit" name="apply" value="1" class="admin-btn-filter inline-flex items-center gap-2 min-h-11">@include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4']) {{ t('filter', 'Filter') }}</button>
        </div>
    </form>
</div>

@if($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
        {{ $errors->first() }}
    </div>
@endif

@if($gridError)
    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-200">{{ $gridError }}</div>
@endif

@if($examModel && $subjects->isNotEmpty())
    @php
        $deskPrefix = $examModel->desk_number_prefix ?? '';
        $calendarYearLabel = $examModel->exam_date?->format('Y');
        $examTypeLabel = $examModel->examType?->name;
        $examTitle = $examModel->name . ($examModel->exam_date ? ' — ' . $examModel->exam_date->format('M j, Y') : '');
    @endphp

    <div class="mb-4 flex flex-wrap gap-x-10 gap-y-2 rounded-lg border border-slate-200/80 bg-slate-50/80 px-4 py-3 text-sm dark:border-slate-600 dark:bg-slate-800/50">
        <div>
            <span class="font-semibold text-slate-500 dark:text-slate-400">{{ t('mandatory_scores_grid_year', 'Year') }}:</span>
            <span class="ml-2 text-slate-900 dark:text-slate-100">{{ $calendarYearLabel ?? '—' }}</span>
        </div>
        <div>
            <span class="font-semibold text-slate-500 dark:text-slate-400">{{ t('mandatory_scores_grid_exam_type', 'Exam type') }}:</span>
            <span class="ml-2 text-slate-900 dark:text-slate-100">{{ $examTypeLabel ?? '—' }}</span>
        </div>
        <div>
            <span class="font-semibold text-slate-500 dark:text-slate-400">{{ t('exams', 'Exam') }}:</span>
            <span class="ml-2 text-slate-900 dark:text-slate-100">{{ $examTitle }}</span>
        </div>
    </div>

    @if($rows->isEmpty())
        <div class="rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800/50 px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
            {{ t('mandatory_scores_grid_no_desks', 'No candidates have an assigned desk for this exam yet. Approve sanghas for this exam and assign hall desk numbers as needed.') }}
        </div>
    @else
        <div class="hidden" aria-hidden="true">
            @foreach($rows as $row)
                @php $formSangha = $row['sangha']; @endphp
                <form id="mandatory-grid-form-{{ $formSangha->id }}" method="POST" action="{{ route('admin.mandatory-scores.grid-row') }}">
                    @csrf
                    <input type="hidden" name="exam_id" value="{{ $examModel->id }}">
                    <input type="hidden" name="sangha_id" value="{{ $formSangha->id }}">
                    @if($examTypeId)
                        <input type="hidden" name="exam_type_id" value="{{ $examTypeId }}">
                    @endif
                </form>
            @endforeach
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800/40 shadow-sm">
            <table class="min-w-full border-collapse text-sm mandatory-score-grid-table">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-100/90 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:border-slate-600 dark:bg-slate-900/60 dark:text-slate-300">
                        <th class="sticky left-0 z-10 border-r border-slate-200 bg-slate-100 px-3 py-2.5 dark:border-slate-600 dark:bg-slate-900">{{ t('desk_number', 'Desk No.') }}</th>
                        <th class="whitespace-nowrap px-3 py-2.5">{{ t('roll_number', 'Roll Number') }}</th>
                        <th class="min-w-[220px] px-3 py-2.5">{{ t('score_table_father_nrc', 'Father / NRC') }}</th>
                        <th class="min-w-[140px] px-3 py-2.5">{{ t('sanghas', 'Sangha') }}</th>
                        @foreach($subjects as $subject)
                            <th class="whitespace-nowrap px-2 py-2.5 text-center" title="{{ $subject->name }}">{{ \Illuminate\Support\Str::limit($subject->name, 14) }}</th>
                        @endforeach
                        <th class="w-28 px-2 py-2.5 text-center">{{ t('total', 'Total') }}</th>
                        <th class="w-28 px-2 py-2.5 text-center">{{ t('mandatory_scores_grid_confirm', 'Confirm') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($rows as $row)
                        @php
                            /** @var \App\Models\Sangha $sangha */
                            $sangha = $row['sangha'];
                            $scoresBySubject = $row['scoresBySubject'];
                            $deskNumPad = $sangha->desk_number !== null ? str_pad((string) (int) $sangha->desk_number, 6, '0', STR_PAD_LEFT) : null;
                            $deskDisplay = $deskNumPad !== null ? (($deskPrefix !== '' ? $deskPrefix : '') . $deskNumPad) : '—';
                            $rollRaw = $sangha->eligible_roll_number;
                            $rollCell = filled($rollRaw) && ctype_digit(trim((string) $rollRaw))
                                ? str_pad(trim((string) $rollRaw), 6, '0', STR_PAD_LEFT)
                                : (filled($rollRaw) ? (string) $rollRaw : '—');
                            $formId = 'mandatory-grid-form-'.$sangha->id;
                        @endphp
                        <tr class="bg-white hover:bg-slate-50/80 dark:bg-slate-800/30 dark:hover:bg-slate-800/60">
                            <td class="sticky left-0 z-10 border-r border-slate-100 bg-white px-3 py-2 font-semibold tabular-nums text-amber-700 dark:border-slate-700 dark:bg-slate-800 dark:text-amber-400">
                                {{ $deskDisplay }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-2 font-mono text-xs text-slate-700 dark:text-slate-300 tabular-nums">
                                {{ $rollCell }}
                            </td>
                            <td class="px-3 py-2">
                                <div class="text-slate-800 dark:text-slate-200">{{ filled($sangha->father_name) ? $sangha->father_name : '—' }}</div>
                                <div class="font-mono text-[11px] text-slate-500 dark:text-slate-400 break-all">{{ filled($sangha->nrc_number) ? $sangha->nrc_number : '—' }}</div>
                            </td>
                            <td class="px-3 py-2 text-slate-900 dark:text-slate-100">
                                {{ filled($sangha->name) ? $sangha->name : '—' }}
                            </td>
                            @foreach($subjects as $subject)
                                @php
                                    $existing = $scoresBySubject->get($subject->id);
                                    $oldKey = 'scores.'.$subject->id;
                                    $val = old($oldKey, $existing ? $existing->value : null);
                                @endphp
                                <td class="p-1 align-middle">
                                    <input type="number"
                                        form="{{ $formId }}"
                                        name="scores[{{ $subject->id }}]"
                                        value="{{ $val !== null && $val !== '' ? $val : '0' }}"
                                        step="1"
                                        min="0"
                                        class="score-grid-input w-full min-w-[4.5rem] rounded border border-slate-200 bg-white px-2 py-1.5 text-center text-sm tabular-nums text-slate-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
                                        placeholder="0"
                                        autocomplete="off"
                                        data-row-score
                                        aria-label="{{ $subject->name }}">
                                </td>
                            @endforeach
                            @php
                                $total = 0.0;
                                foreach ($subjects as $subject) {
                                    $existing = $scoresBySubject->get($subject->id);
                                    $oldKey = 'scores.'.$subject->id;
                                    $val = old($oldKey, $existing ? $existing->value : null);
                                    if ($val !== null && $val !== '' && is_numeric((string) $val)) {
                                        $total += (float) $val;
                                    }
                                }
                            @endphp
                            <td class="px-2 py-2 text-center align-middle font-semibold tabular-nums text-slate-800 dark:text-slate-200" data-row-total>{{ format_number_display($total) }}</td>
                            <td class="p-1 align-middle text-center">
                                <button type="submit" form="{{ $formId }}" class="inline-flex items-center justify-center rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-900 transition hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-200 dark:hover:bg-amber-900/50">
                                    {{ t('mandatory_scores_grid_confirm', 'Confirm') }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endif
@endsection

@push('scripts')
<script>
(function () {
    var MSG_SELECT_EXAM = @json(t('mandatory_scores_select_exam', 'Select exam'));
    var ALL_LABEL = @json(t('all', 'All'));
    var examOptionsUrl = @json(route('admin.mandatory-scores.exam-options'));
    var yearOptionsUrl = @json(route('admin.mandatory-scores.year-options'));

    function clearExamSelect(examSelect) {
        if (!examSelect) return;
        if (examSelect.tomselect) {
            examSelect.tomselect.clear(true);
        } else {
            examSelect.value = '';
        }
    }

    function yearValue(yearSelect) {
        if (!yearSelect) return '';
        return yearSelect.tomselect ? yearSelect.tomselect.getValue() : yearSelect.value;
    }

    function setYearValue(yearSelect, y) {
        if (!yearSelect) return;
        if (yearSelect.tomselect) {
            yearSelect.tomselect.setValue(y ? String(y) : '', true);
        } else {
            yearSelect.value = y ? String(y) : '';
        }
    }

    function examTypeValue(examTypeSelect) {
        if (!examTypeSelect) return '';
        return examTypeSelect.tomselect ? examTypeSelect.tomselect.getValue() : examTypeSelect.value;
    }

    function syncSelectTomSelect(selectEl) {
        if (selectEl && selectEl.tomselect) {
            try {
                selectEl.tomselect.sync();
            } catch (e) {}
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        queueMicrotask(function initGridYearExamFilters() {
            var yearSelect = document.getElementById('grid_year_id');
            var examTypeSelect = document.getElementById('grid_exam_type_id');
            var examSelect = document.getElementById('grid_exam_id');
            if (!yearSelect || !examSelect || !examTypeSelect) return;

            function rebuildYearOptionsFromJson(years) {
                var prev = yearValue(yearSelect);
                if (yearSelect.tomselect) {
                    try {
                        yearSelect.tomselect.destroy();
                    } catch (e) {}
                }
                yearSelect.innerHTML = '';
                var allOpt = document.createElement('option');
                allOpt.value = '';
                allOpt.textContent = ALL_LABEL;
                yearSelect.appendChild(allOpt);
                (years || []).forEach(function (yr) {
                    var o = document.createElement('option');
                    o.value = String(yr);
                    o.textContent = String(yr);
                    yearSelect.appendChild(o);
                });
                if (typeof window.sanghaInitSearchableSelects === 'function') {
                    window.sanghaInitSearchableSelects(document);
                }
                var allowed = {};
                (years || []).forEach(function (yr) {
                    allowed[String(yr)] = true;
                });
                if (prev && allowed[prev]) {
                    setYearValue(yearSelect, prev);
                } else {
                    setYearValue(yearSelect, '');
                }
                syncSelectTomSelect(yearSelect);
            }

            function rebuildExamOptionsFromJson(exams) {
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

            function fetchExamOptions() {
                clearExamSelect(examSelect);
                var y = yearValue(yearSelect);
                var et = examTypeValue(examTypeSelect);
                var q = [];
                if (y) q.push('year_id=' + encodeURIComponent(y));
                if (et) q.push('exam_type_id=' + encodeURIComponent(et));
                var url = examOptionsUrl + (q.length ? '?' + q.join('&') : '');
                fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                    .then(function (r) {
                        if (!r.ok) throw new Error('bad response');
                        return r.json();
                    })
                    .then(function (data) {
                        rebuildExamOptionsFromJson(data.exams || []);
                    })
                    .catch(function () {
                        rebuildExamOptionsFromJson([]);
                    });
            }

            function onExamTypeChanged() {
                var et = examTypeValue(examTypeSelect);
                fetch(yearOptionsUrl + (et ? '?exam_type_id=' + encodeURIComponent(et) : ''), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                    .then(function (r) {
                        if (!r.ok) throw new Error('bad response');
                        return r.json();
                    })
                    .then(function (data) {
                        rebuildYearOptionsFromJson(data.years || []);
                        fetchExamOptions();
                    })
                    .catch(function () {
                        rebuildYearOptionsFromJson([]);
                        fetchExamOptions();
                    });
            }

            function onYearChanged() {
                fetchExamOptions();
            }

            examTypeSelect.addEventListener('change', onExamTypeChanged);
            yearSelect.addEventListener('change', onYearChanged);

            function recalcRowTotal(tr) {
                if (!tr) return;
                var totalEl = tr.querySelector('[data-row-total]');
                if (!totalEl) return;
                var sum = 0;
                tr.querySelectorAll('input[data-row-score]').forEach(function (input) {
                    var raw = (input.value || '').trim();
                    var n = Number(raw);
                    if (raw !== '' && Number.isFinite(n)) sum += n;
                });
                var rounded = Math.round(sum * 100) / 100;
                totalEl.textContent = Number.isInteger(rounded) ? String(rounded) : String(rounded);
            }

            document.querySelectorAll('table.mandatory-score-grid-table tbody tr').forEach(function (tr) {
                recalcRowTotal(tr);
                tr.querySelectorAll('input[data-row-score]').forEach(function (input) {
                    input.addEventListener('input', function () { recalcRowTotal(tr); });
                });
            });
        });
    });
})();
</script>
@endpush
