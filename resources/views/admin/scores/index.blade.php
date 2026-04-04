@extends('admin.layout')

@section('title', 'Scores')

@section('content')
<div class="admin-page-header">
    <h1>Scores</h1>
    <div class="flex items-center gap-2">
        @if($screen === 'pass')
            <button type="button" id="generate-pass-list-btn" class="admin-btn-secondary inline-flex items-center gap-2 min-w-[118px] justify-center">
                <span id="generate-pass-list-spinner" class="w-4 h-4 border-2 border-current border-t-transparent rounded-full" style="display:none; animation: generate-spin 0.8s linear infinite;"></span>
                <span id="generate-pass-list-label" class="inline-flex items-center gap-2">@include('partials.icon', ['name' => 'external-link', 'class' => 'w-4 h-4']) Generate</span>
            </button>
        @endif
        <a href="{{ route('admin.scores.create') }}" class="admin-btn-add">@include('partials.icon', ['name' => 'plus', 'class' => 'w-5 h-5']) Add Score</a>
    </div>
</div>

<div class="mb-4 rounded-xl border border-slate-200/80 dark:border-slate-600 bg-white dark:bg-slate-800 p-2">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.scores.index', ['screen' => 'all']) }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ $screen === 'all' ? 'bg-amber-500 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">All</a>
        <a href="{{ route('admin.scores.index', ['screen' => 'top20']) }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ $screen === 'top20' ? 'bg-amber-500 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">Top 20 Sangha</a>
        <a href="{{ route('admin.scores.index', ['screen' => 'moderation']) }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ $screen === 'moderation' ? 'bg-amber-500 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">Moderation</a>
        <a href="{{ route('admin.scores.index', ['screen' => 'pass']) }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ $screen === 'pass' ? 'bg-amber-500 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">Pass</a>
        <a href="{{ route('admin.scores.index', ['screen' => 'fail']) }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ $screen === 'fail' ? 'bg-amber-500 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">Fail</a>
    </div>
</div>

<form method="GET" class="admin-filter-bar flex flex-nowrap items-end gap-2 sm:gap-3 overflow-x-auto">
    <input type="hidden" name="screen" value="{{ $screen }}">
    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
    @if(request('order'))<input type="hidden" name="order" value="{{ request('order') }}">@endif
    <div class="flex flex-col gap-1 shrink-0 min-w-0">
        <label for="search" class="admin-filter-label text-xs max-w-[80px] sm:max-w-none">Search</label>
        <div class="relative w-32 sm:w-36 shrink-0">
            <svg class="admin-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Sangha, subject..." class="admin-search-input py-2 text-sm">
        </div>
    </div>
    <div class="flex flex-col gap-1 shrink-0 min-w-0">
        <label for="subject_id" class="admin-filter-label text-xs max-w-[80px] sm:max-w-none">Subject</label>
        <select name="subject_id" id="subject_id" class="admin-select py-2 text-sm w-28 sm:w-32 shrink-0">
            <option value="">All</option>
            @foreach($subjects as $s)
                <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ Str::limit($s->name, 16) }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex flex-col gap-1 shrink-0 min-w-0">
        <label for="sangha_id" class="admin-filter-label text-xs max-w-[80px] sm:max-w-none">Sangha</label>
        <select name="sangha_id" id="sangha_id" class="admin-select py-2 text-sm w-36 sm:w-40 max-w-[160px] shrink-0">
            <option value="">All</option>
            @foreach($sanghas as $s)
                <option value="{{ $s->id }}" {{ request('sangha_id') == $s->id ? 'selected' : '' }}>{{ Str::limit($s->name, 18) }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex flex-col gap-1 shrink-0 min-w-0">
        <label for="exam_id" class="admin-filter-label text-xs max-w-[80px] sm:max-w-none">Exam</label>
        <select name="exam_id" id="exam_id" class="admin-select py-2 text-sm w-36 sm:w-40 max-w-[160px] shrink-0">
            <option value="">All</option>
            @foreach($exams as $e)
                <option value="{{ $e->id }}" {{ request('exam_id') == $e->id ? 'selected' : '' }}>{{ Str::limit($e->name . ($e->exam_date ? ' ' . $e->exam_date->format('M d') : ''), 24) }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2 shrink-0 ml-auto self-end">
        <button type="submit" class="admin-btn-filter py-2 text-sm">@include('partials.icon', ['name' => 'funnel', 'class' => 'w-4 h-4']) Filter</button>
        @if(request()->hasAny(['search', 'subject_id', 'sangha_id', 'exam_id']))
            <a href="{{ route('admin.scores.index', ['screen' => $screen]) }}" class="admin-btn-clear py-2 text-sm">@include('partials.icon', ['name' => 'x', 'class' => 'w-4 h-4']) Clear</a>
        @endif
    </div>
</form>

<div class="admin-table-card overflow-x-auto">
    @if($screen === 'top20')
        <div class="px-4 sm:px-6 py-3 border-b border-slate-100 dark:border-slate-600 text-sm text-slate-600 dark:text-slate-300">
            Top 20 highest-scoring sanghas. Drag rows to reorder as you like.
        </div>
        <table id="scores-top20-table" class="admin-table divide-y divide-slate-100">
            <thead>
                <tr>
                    <th class="w-12">No.</th>
                    <th class="w-10"></th>
                    <th>Sangha</th>
                    <th>Monastery</th>
                    <th>Total Score</th>
                    <th>Average Score</th>
                    <th>Scores Count</th>
                </tr>
            </thead>
            <tbody id="top20-sortable">
                @forelse($topSanghas as $sangha)
                    <tr draggable="true" class="top20-row cursor-move" data-sangha-id="{{ $sangha->id }}">
                        <td class="text-slate-600 dark:text-slate-400">{{ $loop->iteration }}</td>
                        <td class="text-slate-400">⋮⋮</td>
                        <td><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $sangha->name }}</span></td>
                        <td>{{ $sangha->monastery->name ?? '—' }}</td>
                        <td><span class="font-semibold text-slate-900 dark:text-slate-100">{{ format_number_display($sangha->total_score) }}</span></td>
                        <td>{{ format_number_display($sangha->average_score) }}</td>
                        <td>{{ $sangha->score_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="admin-table-empty">No score records to rank yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <div class="flex justify-start px-4 sm:px-6 py-2 border-b border-slate-100 dark:border-slate-600">
            @include('admin.partials.column-visibility', [
                'tableId' => 'scores-table',
                'storageKey' => 'admin-scores-columns',
                'columns' => [
                    ['id' => 'sangha', 'label' => 'Sangha'],
                    ['id' => 'subject', 'label' => 'Subject'],
                    ['id' => 'exam', 'label' => 'Exam'],
                    ['id' => 'value', 'label' => 'Value'],
                ],
            ])
        </div>
        <table id="scores-table" class="admin-table divide-y divide-slate-100">
            <thead>
                <tr>
                    <th class="w-12 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">No.</th>
                    @include('admin.partials.sortable-th', ['key' => 'sangha', 'label' => 'Sangha', 'dataColumn' => 'sangha'])
                    @include('admin.partials.sortable-th', ['key' => 'subject', 'label' => 'Subject', 'dataColumn' => 'subject'])
                    @include('admin.partials.sortable-th', ['key' => 'exam', 'label' => 'Exam', 'dataColumn' => 'exam'])
                    @include('admin.partials.sortable-th', ['key' => 'value', 'label' => 'Value', 'dataColumn' => 'value'])
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scores as $score)
                    <tr>
                        <td class="text-slate-600 dark:text-slate-400">{{ $scores->firstItem() + $loop->index }}</td>
                        <td data-column="sangha">
                            <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $score->sangha->name }}</span>
                            <span class="block text-xs text-slate-500">{{ $score->sangha->monastery->name ?? '—' }}</span>
                        </td>
                        <td data-column="subject">{{ $score->subject->name }}</td>
                        <td data-column="exam">{{ $score->exam?->name ?: '—' }}</td>
                        <td data-column="value">
                            <span class="font-semibold text-slate-900 dark:text-slate-100">{{ format_number_display($score->value) }}</span>
                            @if($screen === 'moderation')
                                <span class="block text-xs text-amber-600 dark:text-amber-300">between moderation and pass</span>
                            @elseif($screen === 'pass')
                                <span class="block text-xs text-emerald-600 dark:text-emerald-300">pass</span>
                            @elseif($screen === 'fail')
                                <span class="block text-xs text-red-600 dark:text-red-300">below moderation</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.scores.edit', $score) }}" class="admin-action-link admin-action-edit">@include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4']) Edit</a>
                            <form action="{{ route('admin.scores.destroy', $score) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Delete this score?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-action-link admin-action-delete">@include('partials.icon', ['name' => 'trash', 'class' => 'w-4 h-4']) Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="admin-table-empty">No scores found for this screen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</div>

@if($screen !== 'top20')
    @include('admin.partials.table-pagination', ['paginator' => $scores, 'routeName' => 'admin.scores.index'])
@endif

@if($screen === 'top20')
<script>
(function() {
    var tbody = document.getElementById('top20-sortable');
    if (!tbody) return;
    var dragging = null;
    var saveTimer = null;
    var reorderUrl = @json(route('admin.scores.top20.reorder'));
    var csrfToken = @json(csrf_token());

    function setRowNumbers() {
        Array.from(tbody.querySelectorAll('tr.top20-row')).forEach(function(row, index) {
            var firstCell = row.querySelector('td');
            if (firstCell) firstCell.textContent = String(index + 1);
        });
    }

    function currentOrderIds() {
        return Array.from(tbody.querySelectorAll('tr.top20-row'))
            .map(function(row) { return Number(row.getAttribute('data-sangha-id')); })
            .filter(function(id) { return Number.isInteger(id) && id > 0; });
    }

    function persistOrder() {
        var ids = currentOrderIds();
        if (!ids.length) return;

        fetch(reorderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ sangha_ids: ids })
        }).catch(function() {
            // Silent fail keeps drag UX smooth even if network hiccups.
        });
    }

    tbody.querySelectorAll('tr.top20-row').forEach(function(row) {
        row.addEventListener('dragstart', function() {
            dragging = row;
            row.classList.add('opacity-60');
        });
        row.addEventListener('dragend', function() {
            row.classList.remove('opacity-60');
            dragging = null;
            setRowNumbers();
            if (saveTimer) window.clearTimeout(saveTimer);
            saveTimer = window.setTimeout(persistOrder, 250);
        });
        row.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (!dragging || dragging === row) return;
            var rect = row.getBoundingClientRect();
            var shouldInsertAfter = (e.clientY - rect.top) > rect.height / 2;
            if (shouldInsertAfter) {
                row.parentNode.insertBefore(dragging, row.nextSibling);
            } else {
                row.parentNode.insertBefore(dragging, row);
            }
        });
    });
})();
</script>
@endif

@if($screen === 'pass')
<script>
(function() {
    var styleEl = document.createElement('style');
    styleEl.textContent = '@keyframes generate-spin{to{transform:rotate(360deg)}}';
    document.head.appendChild(styleEl);

    var btn = document.getElementById('generate-pass-list-btn');
    if (!btn) return;

    var spinner = document.getElementById('generate-pass-list-spinner');
    var label = document.getElementById('generate-pass-list-label');
    var url = @json(route('admin.scores.generate-pass-list'));
    var csrfToken = @json(csrf_token());
    var minimumLoadingMs = 1200;

    function setLoading(isLoading) {
        btn.disabled = isLoading;
        spinner.style.display = isLoading ? 'inline-block' : 'none';
        label.style.display = isLoading ? 'none' : 'inline-flex';
        btn.classList.toggle('opacity-80', isLoading);
        btn.classList.toggle('cursor-wait', isLoading);
    }

    function showPopup(message, isError) {
        var popup = document.createElement('div');
        popup.className = 'fixed top-5 right-5 z-[9999] rounded-xl px-4 py-3 text-sm font-semibold shadow-lg border ' + (isError
            ? 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-200 dark:border-red-800'
            : 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-800');
        popup.textContent = message;
        document.body.appendChild(popup);
        setTimeout(function() { popup.remove(); }, 2500);
    }

    btn.addEventListener('click', function() {
        var startedAt = Date.now();
        setLoading(true);
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({})
        })
        .then(function(res) { return res.json(); })
        .then(function(payload) {
            showPopup(payload.message || 'Generated successful.', false);
        })
        .catch(function() {
            showPopup('Generate failed. Please try again.', true);
        })
        .finally(function() {
            var elapsed = Date.now() - startedAt;
            var waitMs = Math.max(0, minimumLoadingMs - elapsed);
            window.setTimeout(function() {
                setLoading(false);
            }, waitMs);
        });
    });
})();
</script>
@endif
@endsection
