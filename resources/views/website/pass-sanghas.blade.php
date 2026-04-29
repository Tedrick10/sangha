@extends('website.layout')

@section('title', 'Pass Sangha List')

@section('content')
<section class="relative py-10 sm:py-14 lg:py-16">
    <div class="max-w-6xl mx-auto space-y-4 px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-stone-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm p-6 sm:p-8">
            <h1 class="font-heading text-3xl sm:text-4xl font-semibold tracking-tight text-stone-900 dark:text-slate-100">Pass Sangha List</h1>
            <p class="mt-2 text-sm text-stone-600 dark:text-slate-300">Year -> Exam Type -> Exam -> Pass Sangha Table</p>
            @if($generatedAt)
                <p class="mt-3 inline-flex items-center rounded-full border border-stone-200 dark:border-slate-700 px-3 py-1 text-xs text-stone-500 dark:text-slate-400">
                    Generated at: {{ \Illuminate\Support\Carbon::parse($generatedAt)->format('M d, Y H:i') }}
                </p>
            @endif
        </div>

        <div class="rounded-3xl border border-stone-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm p-5 sm:p-7 transition-opacity duration-200">
            @php
                $url = function (array $overrides = []) {
                    $q = request()->query();
                    foreach ($overrides as $k => $v) {
                        if ($v === null || $v === '') unset($q[$k]); else $q[$k] = $v;
                    }
                    return route('website.pass-sanghas', $q);
                };

                $breadcrumbs = [['label' => 'Years', 'url' => $url(['year' => null, 'exam_type_id' => null, 'exam_id' => null, 'q' => null])]];
                if ($selectedYear) $breadcrumbs[] = ['label' => $selectedYear, 'url' => $url(['exam_type_id' => null, 'exam_id' => null, 'q' => null])];
                if ($selectedExamType) $breadcrumbs[] = ['label' => $selectedExamType['name'], 'url' => $url(['exam_id' => null, 'q' => null])];
                if ($selectedExam) $breadcrumbs[] = ['label' => $selectedExam['label'], 'url' => null];

                $backUrl = null;
                if ($selectedExamId) $backUrl = $url(['exam_id' => null, 'q' => null]);
                elseif ($selectedExamTypeId) $backUrl = $url(['exam_type_id' => null, 'exam_id' => null, 'q' => null]);
                elseif ($selectedYear) $backUrl = $url(['year' => null, 'exam_type_id' => null, 'exam_id' => null, 'q' => null]);

            @endphp

            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm">
                    @foreach($breadcrumbs as $crumb)
                        @if($crumb['url'])
                            <a href="{{ $crumb['url'] }}" class="text-stone-600 hover:text-stone-900 dark:text-slate-300 dark:hover:text-slate-100">{{ $crumb['label'] }}</a>
                        @else
                            <span class="font-semibold text-stone-900 dark:text-slate-100">{{ $crumb['label'] }}</span>
                        @endif
                        @if(! $loop->last)<span class="text-stone-400 dark:text-slate-500">/</span>@endif
                    @endforeach
                </div>
                @if($backUrl)
                    <a href="{{ $backUrl }}" class="inline-flex items-center rounded-xl border border-stone-300 dark:border-slate-700 px-3 py-1.5 text-xs font-semibold text-stone-700 dark:text-slate-200 hover:bg-stone-50 dark:hover:bg-slate-800">Back</a>
                @endif
            </div>

            @if(! $selectedYear)
                <h2 class="font-heading text-xl sm:text-2xl font-semibold text-stone-900 dark:text-slate-100 mb-4">Choose Year</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                    @forelse($years as $year)
                        <a href="{{ $url(['year' => $year, 'exam_type_id' => null, 'exam_id' => null, 'q' => null]) }}" class="group rounded-2xl border border-stone-200 dark:border-slate-700 bg-stone-50/60 dark:bg-slate-800/30 p-4 text-center transition duration-200 hover:-translate-y-0.5 hover:border-stone-300 dark:hover:border-slate-600 hover:bg-white dark:hover:bg-slate-800">
                            <p class="text-lg font-semibold text-stone-900 dark:text-slate-100">{{ $year }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-stone-500 dark:text-slate-400">No year data available.</p>
                    @endforelse
                </div>
            @elseif(! $selectedExamTypeId)
                <h2 class="font-heading text-xl sm:text-2xl font-semibold text-stone-900 dark:text-slate-100 mb-4">Choose Exam Type</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($examTypesInYear as $type)
                        <a href="{{ $url(['exam_type_id' => $type['id'], 'exam_id' => null, 'q' => null]) }}" class="rounded-2xl border border-stone-200 dark:border-slate-700 bg-stone-50/60 dark:bg-slate-800/30 p-5 transition duration-200 hover:-translate-y-0.5 hover:border-stone-300 dark:hover:border-slate-600 hover:bg-white dark:hover:bg-slate-800">
                            <p class="font-semibold text-stone-900 dark:text-slate-100">{{ $type['name'] }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-stone-500 dark:text-slate-400">No exam types found for this year.</p>
                    @endforelse
                </div>
            @elseif(! $selectedExamId)
                <h2 class="font-heading text-xl sm:text-2xl font-semibold text-stone-900 dark:text-slate-100 mb-4">Choose Exam</h2>
                <div class="space-y-3">
                    @forelse($examOptions as $exam)
                        <a href="{{ $url(['exam_id' => $exam['id']]) }}" class="flex items-center justify-between gap-3 rounded-2xl border border-stone-200 dark:border-slate-700 bg-stone-50/60 dark:bg-slate-800/30 p-4 transition duration-200 hover:-translate-y-0.5 hover:border-stone-300 dark:hover:border-slate-600 hover:bg-white dark:hover:bg-slate-800">
                            <span class="min-w-0 truncate font-medium text-stone-900 dark:text-slate-100">{{ $exam['label'] }}</span>
                            <span class="inline-flex rounded-lg border border-stone-300 dark:border-slate-600 px-2 py-1 text-xs text-stone-700 dark:text-slate-300">Open</span>
                        </a>
                    @empty
                        <p class="text-sm text-stone-500 dark:text-slate-400">No exams found for this selection.</p>
                    @endforelse
                </div>
            @else
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h2 class="font-heading text-xl sm:text-2xl font-semibold text-stone-900 dark:text-slate-100">Pass Sangha List</h2>
                    <form method="GET" action="{{ route('website.pass-sanghas') }}" class="w-full sm:w-auto">
                        <input type="hidden" name="year" value="{{ $selectedYear }}">
                        <input type="hidden" name="exam_type_id" value="{{ $selectedExamTypeId }}">
                        <input type="hidden" name="exam_id" value="{{ $selectedExamId }}">
                        <input type="text" name="q" value="{{ $search }}" placeholder="Search sangha name..." class="w-full sm:w-80 rounded-xl border border-stone-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-stone-800 dark:text-slate-100 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-300 dark:focus:ring-slate-600">
                    </form>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-stone-200 dark:border-slate-700">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 z-10 bg-stone-100/95 dark:bg-slate-800/95 backdrop-blur">
                            <tr class="border-b border-stone-200 dark:border-slate-700">
                                <th class="text-left py-3 px-3 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">No.</th>
                                <th class="text-left py-3 px-3 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                                    <span class="block">{{ t('desk_number_short', 'Desk No.') }}</span>
                                    <span class="block normal-case text-[10px] leading-tight">({{ t('exam_roll_number', 'Exam Roll Number') }})</span>
                                </th>
                                <th class="text-left py-3 px-3 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">{{ t('level', 'Level') }}</th>
                                <th class="text-left py-3 px-3 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">{{ t('sanghas', 'Sangha') }}</th>
                                <th class="text-left py-3 px-3 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">Monastery</th>
                                <th class="text-left py-3 px-3 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">Father</th>
                                <th class="text-left py-3 px-3 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">NRC</th>
                                <th class="text-left py-3 px-3 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">Result</th>
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
                                <tr class="{{ $loop->odd ? 'bg-white dark:bg-slate-900' : 'bg-stone-50/70 dark:bg-slate-900/60' }} border-b border-stone-100 dark:border-slate-800">
                                    <td class="py-3 px-3 text-stone-500 dark:text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <span class="block font-mono font-semibold tabular-nums text-yellow-700 dark:text-yellow-400">{{ $deskShow ?? '—' }}</span>
                                        @if($showRollLine)
                                            <span class="block font-mono tabular-nums text-xs text-stone-500 dark:text-slate-400">({{ $rollShow ?? '—' }})</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-stone-700 dark:text-slate-300">{{ $levelName ?? '—' }}</td>
                                    <td class="py-3 px-3 font-medium text-stone-900 dark:text-slate-100">{{ $sangha['name'] ?? '—' }}</td>
                                    <td class="py-3 px-3 text-stone-700 dark:text-slate-300">{{ $sangha['monastery_name'] ?? '—' }}</td>
                                    <td class="py-3 px-3 text-stone-700 dark:text-slate-300">{{ $sangha['father_name'] ?? '—' }}</td>
                                    <td class="py-3 px-3 text-stone-700 dark:text-slate-300">{{ $sangha['nrc_number'] ?? '—' }}</td>
                                    <td class="py-3 px-3"><span class="inline-flex rounded-full border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">Pass</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-stone-500 dark:text-slate-400">No pass sangha found for this exam.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</section>
@endsection

