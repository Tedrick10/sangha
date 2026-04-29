@extends('admin.layout')

@section('title', t('score_moderation_page_title', 'Moderation'))

@section('content')
@php
    $moderationUrl = function (array $overrides = []): string {
        $q = request()->except('page');
        foreach ($overrides as $key => $value) {
            if ($value === null || $value === '') {
                unset($q[$key]);
            } else {
                $q[$key] = $value;
            }
        }

        return route('admin.score-moderation.index', $q);
    };
@endphp

<div class="admin-page-header">
    <h1>{{ t('score_moderation_page_title', 'Moderation') }}</h1>
</div>

<p class="mb-6 max-w-3xl text-sm text-slate-600 dark:text-slate-400">
    {{ t('score_moderation_intro', 'View-only marks by examination. Edit scores in Exam Mark Entry.') }}
</p>

@if(session('success'))
    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
        {{ $errors->first() }}
    </div>
@endif

@if($examTypes->isNotEmpty())
<div class="mb-4 sm:mb-6">
    <p class="admin-filter-label mb-2">{{ t('exam_type', 'Exam Type') }}</p>
    <div class="admin-sangha-segmented-track" role="tablist" aria-label="{{ t('exam_type', 'Exam Type') }}">
        @foreach($examTypes as $et)
            @php $etActive = (string) request('exam_type_id') === (string) $et->id; @endphp
            <a href="{{ $moderationUrl(['exam_type_id' => $et->id]) }}"
                class="admin-sangha-segment {{ $etActive ? 'admin-sangha-segment--active' : '' }}"
                @if($etActive) aria-current="true" @endif
                role="tab">{{ $et->name }}</a>
        @endforeach
    </div>
</div>
@endif

<div class="admin-form-card mb-6">
    <form method="GET" action="{{ route('admin.score-moderation.index') }}" class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
        @if($examTypes->isNotEmpty())
            <input type="hidden" name="exam_type_id" value="{{ request('exam_type_id', $examTypes->first()->id) }}">
        @endif
        <div class="admin-form-group mb-0 min-w-[180px] flex-1">
            <label for="mod_grid_year_id" class="admin-form-label">{{ t('mandatory_scores_grid_year', 'Year') }}</label>
            <select name="year_id" id="mod_grid_year_id" class="admin-select-input w-full">
                <option value="">{{ t('all', 'All') }}</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ (string) ($yearId ?? '') === (string) $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="admin-form-group mb-0 min-w-[240px] flex-1">
            <label for="mod_grid_exam_id" class="admin-form-label">{{ t('exams', 'Exam') }}</label>
            <select name="exam_id" id="mod_grid_exam_id" class="admin-select-input w-full">
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

    @if($moderationSummary->isNotEmpty())
        <div class="mb-4 rounded-2xl border border-slate-200/80 dark:border-slate-700 bg-white dark:bg-slate-900/70 shadow-sm px-4 sm:px-5 py-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-semibold tracking-wide text-slate-900 dark:text-slate-100">{{ t('score_moderation_threshold_summary', 'Moderation Summary') }}</h3>
                @if($moderationStats)
                    <div class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-1 text-xs text-slate-700 dark:text-slate-200">
                        <span>Pass <span class="font-semibold tabular-nums">{{ $moderationStats['pass'] }}</span></span>
                        <span class="text-slate-300 dark:text-slate-600">|</span>
                        <span>Fail <span class="font-semibold tabular-nums">{{ $moderationStats['fail'] }}</span></span>
                        <span class="text-slate-300 dark:text-slate-600">|</span>
                        <span>Total <span class="font-semibold tabular-nums">{{ $moderationStats['total'] }}</span></span>
                    </div>
                @endif
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($moderationSummary as $item)
                    <span class="inline-flex items-center rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-1.5 text-xs sm:text-sm text-slate-700 dark:text-slate-200">
                        <span class="font-medium">{{ $item['subject'] }}</span>
                        <span class="mx-1.5 text-slate-400 dark:text-slate-500">:</span>
                        <span class="font-semibold tabular-nums text-amber-600 dark:text-amber-300">{{ format_number_display($item['threshold']) }}</span>
                    </span>
                @endforeach
            </div>

            <p class="mt-3 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                {{ t('score_moderation_rule', 'If any one subject is below its moderation mark, that sangha is counted as Fail.') }}
            </p>
        </div>
    @endif

    @if($subjects->isNotEmpty() && ! $moderationLocked)
        <div class="mb-5 rounded-2xl border border-slate-200/80 dark:border-slate-700 bg-white dark:bg-slate-900/70 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/70">
                <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ t('score_moderation_control_title', 'Moderation Control') }}</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ t('score_moderation_control_intro', 'Set moderation marks by subject, then Save or Confirm for this exam.') }}</p>
            </div>
            <form method="POST" action="{{ route('admin.score-moderation.control') }}" class="p-4 sm:p-5">
                @csrf
                <input type="hidden" name="exam_type_id" value="{{ $examTypeId }}">
                @if($yearId)<input type="hidden" name="year_id" value="{{ $yearId }}">@endif
                <input type="hidden" name="exam_id" value="{{ $examModel->id }}">

                <div class="grid gap-3 md:gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($subjects as $subject)
                        @php
                            $threshold = $moderationThresholds[(int) $subject->id] ?? $moderationThresholds[(string) $subject->id] ?? null;
                            $inputVal = old('scores.'.$subject->id, $threshold);
                        @endphp
                        <div>
                            <label class="admin-form-label mb-1.5 block text-slate-700 dark:text-slate-300">{{ \Illuminate\Support\Str::limit($subject->name, 28) }}</label>
                            <input type="number"
                                name="scores[{{ $subject->id }}]"
                                value="{{ $inputVal !== null && $inputVal !== '' ? $inputVal : '' }}"
                                step="1"
                                min="0"
                                class="admin-input w-full h-11 rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-sm text-center text-slate-900 dark:text-slate-100 tabular-nums"
                                placeholder="0">
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-2.5">
                    <button type="submit" name="submit_action" value="save" class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                        {{ t('save', 'Save') }}
                    </button>
                    <button type="submit" name="submit_action" value="confirm" class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-400/70">
                        {{ t('confirm', 'Confirm') }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($rows->isEmpty())
        <div class="rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800/50 px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
            {{ t('mandatory_scores_grid_no_desks', 'No candidates have an assigned desk for this exam yet. Approve sanghas for this exam and assign hall desk numbers as needed.') }}
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800/40 shadow-sm">
            <table class="min-w-full border-collapse text-sm mandatory-score-grid-table">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-100/90 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:border-slate-600 dark:bg-slate-900/60 dark:text-slate-300">
                        <th class="sticky left-0 z-10 border-r border-slate-200 bg-slate-100 px-3 py-2.5 dark:border-slate-600 dark:bg-slate-900">{{ t('desk_number_exam_roll', 'Desk No. (Exam Roll Number)') }}</th>
                        <th class="whitespace-nowrap px-3 py-2.5">{{ t('roll_number', 'Roll Number') }}</th>
                        <th class="min-w-[220px] px-3 py-2.5">{{ t('score_table_father_nrc', 'Father / NRC') }}</th>
                        <th class="min-w-[140px] px-3 py-2.5">{{ t('sanghas', 'Sangha') }}</th>
                        @foreach($subjects as $subject)
                            <th class="whitespace-nowrap px-2 py-2.5 text-center" title="{{ $subject->name }}">{{ \Illuminate\Support\Str::limit($subject->name, 14) }}</th>
                        @endforeach
                        <th class="w-24 px-2 py-2.5 text-center">{{ t('total', 'Total') }}</th>
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
                                    $rawVal = $existing ? $existing->value : null;
                                    $showMark = $rawVal !== null && $rawVal !== '' ? (string) $rawVal : null;
                                @endphp
                                <td class="px-2 py-2 text-center align-middle tabular-nums text-slate-800 dark:text-slate-200">
                                    {{ $showMark !== null ? $showMark : '—' }}
                                </td>
                            @endforeach
                            @php
                                $total = $subjects->sum(function ($subject) use ($scoresBySubject) {
                                    $existing = $scoresBySubject->get($subject->id);
                                    $rawVal = $existing ? $existing->value : null;
                                    return ($rawVal !== null && $rawVal !== '' && is_numeric((string) $rawVal)) ? (float) $rawVal : 0;
                                });
                            @endphp
                            <td class="px-2 py-2 text-center align-middle font-semibold tabular-nums text-slate-900 dark:text-slate-100">
                                {{ format_number_display($total) }}
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
@if($examTypes->isNotEmpty())
<script>
(function () {
    var MSG_SELECT_EXAM = @json(t('mandatory_scores_select_exam', 'Select exam'));
    var examOptionsUrl = @json(route('admin.mandatory-scores.exam-options'));
    var fixedExamTypeId = @json((string) request('exam_type_id', $examTypes->first()->id));

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
        queueMicrotask(function initModerationGridFilters() {
            var yearSelect = document.getElementById('mod_grid_year_id');
            var examSelect = document.getElementById('mod_grid_exam_id');
            if (!yearSelect || !examSelect || !fixedExamTypeId) return;

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
                var q = [];
                if (y) q.push('year_id=' + encodeURIComponent(y));
                q.push('exam_type_id=' + encodeURIComponent(fixedExamTypeId));
                var url = examOptionsUrl + '?' + q.join('&');
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

            yearSelect.addEventListener('change', fetchExamOptions);

        });
    });
})();
</script>
@endif
@endpush
