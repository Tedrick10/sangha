@extends('admin.layout')

@section('title', $exam->name.' — '.t('exam_entrance_title', 'Entrance & desks'))

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.exams.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) {{ t('exams', 'Exams') }}</a>
    <h1 class="admin-page-title">{{ $exam->name }}</h1>
    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $exam->exam_date?->format('M j, Y') ?? '—' }} @if($exam->examType) · {{ $exam->examType->name }} @endif</p>
    <div class="mt-4 flex flex-wrap gap-2">
        <button type="button" class="js-admin-exam-generate-eligible relative admin-btn-secondary text-sm py-2 inline-flex items-center justify-center gap-2 min-w-[7.5rem]" data-url="{{ route('admin.exams.generate-eligible-list', $exam) }}" title="{{ t('admin_exam_generate_eligible_hint', 'Publish seated candidates to the public website') }}">
            <span class="js-admin-exam-generate-spinner hidden absolute left-1/2 top-1/2 h-8 w-8 -translate-x-1/2 -translate-y-1/2 border-[3px] border-current border-t-transparent rounded-full" style="animation: admin-exam-generate-spin 0.8s linear infinite;"></span>
            <span class="js-admin-exam-generate-label inline-flex items-center gap-2">@include('partials.icon', ['name' => 'external-link', 'class' => 'w-4 h-4']) {{ t('admin_exam_generate_eligible', 'Generate') }}</span>
        </button>
        <a href="{{ route('admin.exams.edit', $exam) }}" class="admin-btn-secondary text-sm py-2">@include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4']) {{ t('edit_exam', 'Edit exam') }}</a>
    </div>
</div>

@php
    $isEntrance = $tab === 'entrance';
@endphp

<div class="rounded-xl border border-slate-200/80 bg-white dark:border-slate-600 dark:bg-slate-900/50 overflow-hidden mb-6">
    <nav class="flex min-w-0 gap-0 border-b border-slate-200 dark:border-slate-600" role="tablist">
        <a href="{{ route('admin.exams.entrances', ['exam' => $exam, 'tab' => 'entrance']) }}"
           class="px-5 py-3.5 text-sm font-medium transition-colors {{ $isEntrance ? 'bg-white dark:bg-slate-900 text-amber-600 dark:text-amber-400 border-b-2 border-amber-500 -mb-px' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-800/80' }}">
            {{ t('exam_tab_entrance', 'Entrance') }}
            @if($pending->total() > 0)
                <span class="ml-1.5 inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-slate-200 px-1.5 text-[11px] font-bold text-slate-800 dark:bg-slate-700 dark:text-slate-100">{{ $pending->total() }}</span>
            @endif
        </a>
        <a href="{{ route('admin.exams.entrances', ['exam' => $exam, 'tab' => 'approved']) }}"
           class="px-5 py-3.5 text-sm font-medium transition-colors {{ ! $isEntrance ? 'bg-white dark:bg-slate-900 text-amber-600 dark:text-amber-400 border-b-2 border-amber-500 -mb-px' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-800/80' }}">
            {{ t('exam_tab_approved', 'Approved') }}
            @if($seated->isNotEmpty())
                <span class="ml-1.5 inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-slate-200 px-1.5 text-[11px] font-bold text-slate-800 dark:bg-slate-700 dark:text-slate-100">{{ $seated->count() }}</span>
            @endif
        </a>
    </nav>

    <div class="p-4 sm:p-6">
        @if($isEntrance)
            <p class="mb-4 text-sm text-slate-600 dark:text-slate-400">{{ t('exam_entrance_help', 'All sanghas not yet seated for this exam are listed here. Confirm to assign them to this exam and the next desk number.') }}</p>

            @if($pending->total() === 0)
                <p class="rounded-xl border border-dashed border-slate-200 bg-slate-50/80 px-4 py-8 text-center text-sm text-slate-600 dark:border-slate-600 dark:bg-slate-800/40 dark:text-slate-400">{{ t('exam_entrance_empty', 'No candidates waiting for entrance.') }}</p>
            @else
                <form id="bulk-entrance-form" action="{{ route('admin.exams.entrances.confirm-bulk', $exam) }}" method="POST" class="hidden" aria-hidden="true">@csrf</form>
                @foreach($pending as $s)
                    <form id="entrance-single-{{ $s->id }}" action="{{ route('admin.exams.entrances.confirm', $exam) }}" method="POST" class="hidden" aria-hidden="true">@csrf<input type="hidden" name="sangha_id" value="{{ $s->id }}"></form>
                @endforeach
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" id="select-all-entrance" class="admin-checkbox rounded border-slate-400">
                            <span>{{ t('select_all', 'Select all') }}</span>
                        </label>
                        <button type="submit" form="bulk-entrance-form" class="admin-btn-primary text-sm py-2" onclick="return document.querySelectorAll('input[form=\'bulk-entrance-form\'][name=\'sangha_ids[]\']:checked').length > 0 || (alert('{{ e(t('exam_entrance_select_first', 'Select at least one candidate.')) }}'), false);">
                            {{ t('confirm_selected', 'Confirm selected') }}
                        </button>
                    </div>

                    <div class="admin-table-card overflow-x-auto">
                        <table class="admin-table divide-y divide-slate-100">
                            <thead>
                                <tr>
                                    <th class="w-10"></th>
                                    <th class="text-left">{{ t('user_id', 'Student Id') }}</th>
                                    <th class="text-left">{{ t('name', 'Name') }}</th>
                                    <th class="text-left">{{ t('monastery', 'Monastery') }}</th>
                                    <th class="text-left">{{ t('exam', 'Exam') }}</th>
                                    <th class="text-right w-40">{{ t('actions', 'Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pending as $sangha)
                                    <tr>
                                        <td>
                                            <input form="bulk-entrance-form" type="checkbox" name="sangha_ids[]" value="{{ $sangha->id }}" class="row-cb admin-checkbox rounded border-slate-400">
                                        </td>
                                        <td class="font-mono text-sm text-slate-600 dark:text-slate-300">{{ $sangha->username }}</td>
                                        <td class="font-medium text-slate-900 dark:text-slate-100">{{ $sangha->name }}</td>
                                        <td>{{ $sangha->monastery?->name ?? '—' }}</td>
                                        <td class="text-sm text-slate-600 dark:text-slate-300">{{ $sangha->exam?->name ?? '—' }}</td>
                                        <td class="text-right">
                                            <button type="submit" form="entrance-single-{{ $sangha->id }}" class="admin-btn-primary text-sm py-1.5 px-3">{{ t('confirm', 'Confirm') }}</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @include('admin.partials.table-pagination', ['paginator' => $pending, 'routeName' => 'admin.exams.entrances', 'routeParams' => ['exam' => $exam]])
                </div>
            @endif
        @else
            <p class="mb-4 text-sm text-slate-600 dark:text-slate-400">{{ t('exam_approved_help', 'Confirmed candidates and assigned desk numbers.') }}</p>

            @if($seated->isEmpty())
                <p class="rounded-xl border border-dashed border-slate-200 bg-slate-50/80 px-4 py-8 text-center text-sm text-slate-600 dark:border-slate-600 dark:bg-slate-800/40 dark:text-slate-400">{{ t('exam_approved_empty', 'No approved candidates yet. Confirm candidates from the Entrance tab.') }}</p>
            @else
                <div class="mb-4">
                    <label for="approved-desk-prefix" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1.5">{{ t('exam_desk_prefix_label', 'Desk number prefix') }}</label>
                    <input type="text" id="approved-desk-prefix" class="admin-input max-w-md" placeholder="e.g. 1st yr - " autocomplete="off" maxlength="80" value="{{ old('desk_number_prefix', $exam->desk_number_prefix ?? '') }}" data-save-url="{{ route('admin.exams.desk-number-prefix', $exam) }}">
                </div>
                <div class="admin-table-card overflow-x-auto">
                    <table class="admin-table divide-y divide-slate-100" id="approved-desk-table">
                        <thead>
                            <tr>
                                <th class="w-24 text-left">{{ t('desk_number', 'Desk No.') }}</th>
                                <th class="text-left">{{ t('user_id', 'Student Id') }}</th>
                                <th class="text-left">{{ t('name', 'Name') }}</th>
                                <th class="text-left">{{ t('monastery', 'Monastery') }}</th>
                                <th class="text-right w-44">{{ t('actions', 'Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seated as $sangha)
                                <tr>
                                    <td class="font-bold tabular-nums text-amber-700 dark:text-amber-400 js-approved-desk-cell" data-desk-number="{{ $sangha->desk_number }}">{{ $sangha->desk_number }}</td>
                                    <td class="font-mono text-sm text-slate-600 dark:text-slate-300">{{ $sangha->username }}</td>
                                    <td class="font-medium text-slate-900 dark:text-slate-100">{{ $sangha->name }}</td>
                                    <td>{{ $sangha->monastery?->name ?? '—' }}</td>
                                    <td class="text-right">
                                        <form action="{{ route('admin.exams.entrances.unseat', $exam) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ e(t('exam_entrance_remove_confirm', 'Remove this candidate from the approved list? They will return to the entrance queue.')) }}');">
                                            @csrf
                                            <input type="hidden" name="sangha_id" value="{{ $sangha->id }}">
                                            <button type="submit" class="admin-btn-secondary text-sm py-1.5 px-3 border border-rose-300/80 text-rose-800 hover:bg-rose-50 dark:border-rose-700/80 dark:text-rose-200 dark:hover:bg-rose-950/40">{{ t('exam_entrance_remove', 'Remove from approved') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    </div>
</div>
@include('admin.partials.exam-generate-eligible')
@endsection

@push('scripts')
@if($isEntrance && $pending->total() > 0)
<script>
document.getElementById('select-all-entrance')?.addEventListener('change', function () {
    var on = document.getElementById('select-all-entrance').checked;
    document.querySelectorAll('input.row-cb').forEach(function (cb) {
        cb.checked = on;
    });
});
</script>
@endif
@if(! $isEntrance && $seated->isNotEmpty())
<script>
(function () {
    var input = document.getElementById('approved-desk-prefix');
    if (!input) return;
    var saveUrl = input.getAttribute('data-save-url');
    var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    var timer = null;

    function applyPrefix() {
        var prefix = input.value || '';
        document.querySelectorAll('.js-approved-desk-cell').forEach(function (td) {
            var n = td.getAttribute('data-desk-number');
            if (n === null || n === '') {
                td.textContent = '—';
                return;
            }
            td.textContent = prefix ? prefix + n : String(n);
        });
    }

    function savePrefix() {
        if (!saveUrl || !token) return;
        clearTimeout(timer);
        timer = setTimeout(function () {
            fetch(saveUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ desk_number_prefix: input.value })
            }).catch(function () {});
        }, 400);
    }

    input.addEventListener('input', function () {
        applyPrefix();
        savePrefix();
    });

    applyPrefix();
})();
</script>
@endif
@endpush
