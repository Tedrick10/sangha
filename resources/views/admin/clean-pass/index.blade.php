@extends('admin.layout')

@section('title', t('clean_pass_page_title', 'Clean Pass'))

@section('content')
@php
    $cleanPassUrl = function (array $overrides = []): string {
        $q = request()->except('page');
        foreach ($overrides as $key => $value) {
            if ($value === null || $value === '') {
                unset($q[$key]);
            } else {
                $q[$key] = $value;
            }
        }

        return route('admin.clean-pass.index', $q);
    };
@endphp

<div class="admin-page-header">
    <h1>{{ t('clean_pass_page_title', 'Clean Pass') }}</h1>
    <form method="POST" action="{{ route('admin.clean-pass.generate') }}" class="inline-flex items-center gap-2">
        @csrf
        @if($examTypes->isNotEmpty())
            <input type="hidden" name="exam_type_id" value="{{ request('exam_type_id', $examTypes->first()->id) }}">
        @endif
        <button type="submit" class="admin-btn-secondary inline-flex items-center gap-2 min-w-[118px] justify-center">
            @include('partials.icon', ['name' => 'external-link', 'class' => 'w-4 h-4'])
            {{ t('clean_pass_generate', 'Generate') }}
        </button>
    </form>
</div>

<div class="mb-4 sm:mb-6">
    <p class="admin-filter-label mb-2">{{ t('exam_type', 'Exam Type') }}</p>
    <div class="admin-sangha-segmented-track" role="tablist" aria-label="{{ t('exam_type', 'Exam Type') }}">
        @foreach($examTypes as $et)
            @php $etActive = (string) request('exam_type_id') === (string) $et->id; @endphp
            <a href="{{ $cleanPassUrl(['exam_type_id' => $et->id]) }}"
                class="admin-sangha-segment {{ $etActive ? 'admin-sangha-segment--active' : '' }}"
                @if($etActive) aria-current="true" @endif
                role="tab">{{ $et->name }}</a>
        @endforeach
    </div>
</div>

<div class="admin-table-card overflow-x-auto">
    <div class="px-4 sm:px-6 py-3 border-b border-slate-100 dark:border-slate-600 text-sm font-medium text-slate-800 dark:text-slate-200">
        <span>{{ t('clean_pass_list_heading', 'Pass Sangha list') }}</span>
    </div>
    <table class="admin-table divide-y divide-slate-100" id="clean-pass-reorder-table">
        <thead>
            <tr>
                <th class="w-12">No.</th>
                <th>
                    <span class="block">{{ t('desk_number_short', 'Desk No.') }}</span>
                    <span class="block text-[10px] font-normal normal-case leading-tight text-slate-500 dark:text-slate-400">({{ t('exam_roll_number', 'Exam Roll Number') }})</span>
                </th>
                <th>{{ t('level', 'Level') }}</th>
                <th>{{ t('sanghas', 'Sangha') }}</th>
                <th>Monastery</th>
                <th>Father</th>
                <th>NRC</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            @forelse($passSanghas as $sangha)
                @php
                    $levelName = $sangha['level_name'] ?? $sangha['programme_level'] ?? null;
                    $rollShow = $sangha['roll_display'] ?? \App\Support\MonasteryPortalResultsSnapshot::formatRollDisplaySix($sangha['eligible_roll_number'] ?? null);
                    $deskShow = $sangha['desk_display'] ?? $sangha['desk_number'] ?? null;
                    $deskNorm = preg_replace('/\D+/', '', (string) ($deskShow ?? ''));
                    $rollNorm = preg_replace('/\D+/', '', (string) ($rollShow ?? ''));
                    $showRollLine = !($deskNorm !== '' && $rollNorm !== '' && $deskNorm === $rollNorm);
                @endphp
                <tr data-sangha-id="{{ (int) ($sangha['id'] ?? 0) }}" class="cursor-move">
                    <td class="text-slate-500 dark:text-slate-400 js-order-no">{{ $loop->iteration }}</td>
                    <td class="whitespace-nowrap">
                        <span class="block font-mono font-semibold tabular-nums text-amber-700 dark:text-amber-400">{{ $deskShow ?? '—' }}</span>
                        @if($showRollLine)
                            <span class="block font-mono tabular-nums text-xs text-slate-500 dark:text-slate-400">({{ $rollShow ?? '—' }})</span>
                        @endif
                    </td>
                    <td>{{ $levelName ?? '—' }}</td>
                    <td class="font-medium">{{ $sangha['name'] ?? '—' }}</td>
                    <td>{{ $sangha['monastery_name'] ?? '—' }}</td>
                    <td>{{ $sangha['father_name'] ?? '—' }}</td>
                    <td>{{ $sangha['nrc_number'] ?? '—' }}</td>
                    <td><span class="inline-flex rounded-full bg-emerald-100 dark:bg-emerald-900/40 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">Pass</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                        {{ t('clean_pass_empty', 'No pass data for this filter yet. Generate the pass list from here or from Scores → Pass.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<style>
/* Row movement feedback for Clean Pass reorder. */
#clean-pass-reorder-table tbody tr[data-sangha-id] {
    transition: background-color 160ms ease;
}
#clean-pass-reorder-table tbody tr[data-sangha-id].is-dragging {
    background-color: rgba(245, 158, 11, 0.12);
}
#clean-pass-reorder-table tbody tr[data-sangha-id].is-drop-target {
    box-shadow: inset 0 2px 0 rgba(245, 158, 11, 0.65);
}
@media (prefers-reduced-motion: reduce) {
    #clean-pass-reorder-table tbody tr[data-sangha-id] {
        transition: none !important;
    }
}
</style>
<script>
(function () {
    var table = document.getElementById('clean-pass-reorder-table');
    if (!table) return;
    var tbody = table.querySelector('tbody');
    if (!tbody) return;
    var reorderUrl = @json(route('admin.clean-pass.reorder'));
    var examTypeId = @json((int) request('exam_type_id', $examTypes->first()->id ?? 0));
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function rows() {
        return Array.from(tbody.querySelectorAll('tr[data-sangha-id]'));
    }

    function renumber() {
        rows().forEach(function (tr, idx) {
            var cell = tr.querySelector('.js-order-no');
            if (cell) cell.textContent = String(idx + 1);
        });
    }

    function collectOrderedIds() {
        return rows()
            .map(function (tr) { return Number(tr.getAttribute('data-sangha-id') || 0); })
            .filter(function (id) { return Number.isInteger(id) && id > 0; });
    }

    function currentOrderKey() {
        return collectOrderedIds().join(',');
    }

    var lastSavedOrderKey = currentOrderKey();
    var savingOrder = false;
    function saveOrderIfChanged() {
        if (savingOrder) return;
        var currentKey = currentOrderKey();
        if (currentKey === '' || currentKey === lastSavedOrderKey) return;
        savingOrder = true;
        fetch(reorderUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                exam_type_id: examTypeId,
                ordered_ids: collectOrderedIds(),
            }),
        }).then(function () {
            lastSavedOrderKey = currentKey;
        }).catch(function () {
            // Ignore network errors; dragging again retries save.
        }).finally(function () {
            savingOrder = false;
        });
    }

    var prefersReducedMotion = false;
    try {
        prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    } catch (e) {}

    // FLIP animation: animate rows from previous visual positions.
    function animateLayoutShift(beforeMap) {
        if (prefersReducedMotion) return;
        rows().forEach(function (tr) {
            var key = tr.getAttribute('data-sangha-id');
            var prev = beforeMap.get(key);
            if (!prev) return;
            var next = tr.getBoundingClientRect();
            var dy = prev.top - next.top;
            if (Math.abs(dy) < 1) return;
            tr.style.transition = 'none';
            tr.style.transform = 'translateY(' + dy + 'px)';
            tr.offsetHeight; // force reflow
            tr.style.transition = 'transform 220ms ease';
            tr.style.transform = 'translateY(0)';
            window.setTimeout(function () {
                tr.style.transition = '';
                tr.style.transform = '';
            }, 240);
        });
    }

    var dragSrc = null;
    function enableAllDraggable(enabled) {
        rows().forEach(function (tr) {
            tr.setAttribute('draggable', enabled ? 'true' : 'false');
        });
    }
    enableAllDraggable(false);

    // Click-hold any row to drag it up/down.
    tbody.addEventListener('mousedown', function (e) {
        var tr = e.target.closest('tr[data-sangha-id]');
        if (!tr) return;
        if (e.target.closest('a, button, input, select, textarea, label')) return;
        enableAllDraggable(false);
        tr.setAttribute('draggable', 'true');
    });
    tbody.addEventListener('mouseup', function () { enableAllDraggable(false); });
    tbody.addEventListener('mouseleave', function () { enableAllDraggable(false); });

    tbody.addEventListener('dragstart', function (e) {
        var tr = e.target.closest('tr[data-sangha-id]');
        if (!tr) return;
        dragSrc = tr;
        tr.classList.add('opacity-60', 'is-dragging');
        if (e.dataTransfer) {
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', tr.getAttribute('data-sangha-id') || '');
        }
    });

    tbody.addEventListener('dragover', function (e) {
        if (!dragSrc) return;
        var over = e.target.closest('tr[data-sangha-id]');
        if (!over || over === dragSrc) return;
        e.preventDefault();
        rows().forEach(function (r) { r.classList.remove('is-drop-target'); });
        over.classList.add('is-drop-target');
        var beforeRects = new Map();
        rows().forEach(function (r) { beforeRects.set(r.getAttribute('data-sangha-id'), r.getBoundingClientRect()); });
        var rect = over.getBoundingClientRect();
        var before = e.clientY < rect.top + rect.height / 2;
        tbody.insertBefore(dragSrc, before ? over : over.nextSibling);
        renumber();
        animateLayoutShift(beforeRects);
    });

    tbody.addEventListener('drop', function (e) {
        if (dragSrc) e.preventDefault();
    });

    tbody.addEventListener('dragend', function () {
        if (dragSrc) {
            dragSrc.classList.remove('opacity-60', 'is-dragging');
            dragSrc = null;
        }
        rows().forEach(function (r) { r.classList.remove('is-drop-target'); });
        enableAllDraggable(false);
        renumber();
        saveOrderIfChanged();
    });
})();
</script>
@endpush
