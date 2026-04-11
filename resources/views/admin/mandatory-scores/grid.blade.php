@extends('admin.layout')

@section('title', t('mandatory_scores_grid_title', 'Exam desk score grid'))

@section('content')
<div class="admin-page-header">
    <h1>{{ t('mandatory_scores_grid_title', 'Exam desk score grid') }}</h1>
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
            <button type="submit" name="apply" value="1" class="admin-btn-filter inline-flex items-center gap-2">@include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4']) {{ t('filter', 'Filter') }}</button>
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
        $yearLabel = $examModel->examType?->name;
        $examTitle = $examModel->name . ($examModel->exam_date ? ' — ' . $examModel->exam_date->format('M j, Y') : '');
    @endphp

    <div class="mb-4 flex flex-wrap gap-x-10 gap-y-2 rounded-lg border border-slate-200/80 bg-slate-50/80 px-4 py-3 text-sm dark:border-slate-600 dark:bg-slate-800/50">
        <div>
            <span class="font-semibold text-slate-500 dark:text-slate-400">{{ t('mandatory_scores_grid_year', 'Year') }}:</span>
            <span class="ml-2 text-slate-900 dark:text-slate-100">{{ $yearLabel ?? '—' }}</span>
        </div>
        <div>
            <span class="font-semibold text-slate-500 dark:text-slate-400">{{ t('exams', 'Exam') }}:</span>
            <span class="ml-2 text-slate-900 dark:text-slate-100">{{ $examTitle }}</span>
        </div>
    </div>

    @if($rows->isEmpty())
        <div class="rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800/50 px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
            {{ t('mandatory_scores_grid_no_desks', 'No candidates have an assigned desk for this exam yet. Use Examinations → Exam → Entrances to seat candidates first.') }}
        </div>
    @else
        <div class="hidden" aria-hidden="true">
            @foreach($rows as $row)
                @php $formSangha = $row['sangha']; @endphp
                <form id="mandatory-grid-form-{{ $formSangha->id }}" method="POST" action="{{ route('admin.mandatory-scores.grid-row') }}">
                    @csrf
                    <input type="hidden" name="exam_id" value="{{ $examModel->id }}">
                    <input type="hidden" name="sangha_id" value="{{ $formSangha->id }}">
                </form>
            @endforeach
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800/40 shadow-sm">
            <table class="min-w-full border-collapse text-sm mandatory-score-grid-table">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-100/90 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:border-slate-600 dark:bg-slate-900/60 dark:text-slate-300">
                        <th class="sticky left-0 z-10 border-r border-slate-200 bg-slate-100 px-3 py-2.5 dark:border-slate-600 dark:bg-slate-900">{{ t('desk_number', 'Desk No.') }}</th>
                        <th class="whitespace-nowrap px-3 py-2.5">{{ t('user_id', 'Student Id') }}</th>
                        <th class="min-w-[220px] px-3 py-2.5">{{ t('score_table_father_nrc', 'Father / NRC') }}</th>
                        <th class="min-w-[140px] px-3 py-2.5">{{ t('sanghas', 'Sangha') }}</th>
                        @foreach($subjects as $subject)
                            <th class="whitespace-nowrap px-2 py-2.5 text-center" title="{{ $subject->name }}">{{ \Illuminate\Support\Str::limit($subject->name, 14) }}</th>
                        @endforeach
                        <th class="w-28 px-2 py-2.5 text-center">{{ t('mandatory_scores_grid_confirm', 'Confirm') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($rows as $row)
                        @php
                            /** @var \App\Models\Sangha $sangha */
                            $sangha = $row['sangha'];
                            $scoresBySubject = $row['scoresBySubject'];
                            $deskDisplay = ($deskPrefix !== '' ? $deskPrefix : '') . $sangha->desk_number;
                            $formId = 'mandatory-grid-form-'.$sangha->id;
                        @endphp
                        <tr class="bg-white hover:bg-slate-50/80 dark:bg-slate-800/30 dark:hover:bg-slate-800/60">
                            <td class="sticky left-0 z-10 border-r border-slate-100 bg-white px-3 py-2 font-semibold tabular-nums text-amber-700 dark:border-slate-700 dark:bg-slate-800 dark:text-amber-400">
                                {{ $deskDisplay }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-2 font-mono text-xs text-slate-700 dark:text-slate-300">
                                {{ filled($sangha->username) ? $sangha->username : '—' }}
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
                                        aria-label="{{ $subject->name }}">
                                </td>
                            @endforeach
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
    var examOptionsUrl = @json(route('admin.mandatory-scores.exam-options'));

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
            var examSelect = document.getElementById('grid_exam_id');
            if (!yearSelect || !examSelect) return;

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

            function onYearChanged() {
                clearExamSelect(examSelect);
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
        });
    });
})();
</script>
@endpush
