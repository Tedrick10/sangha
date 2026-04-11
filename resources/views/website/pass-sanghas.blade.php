@extends('website.layout')

@section('title', 'Pass Sangha List')

@section('content')
<section class="relative py-10 sm:py-14 lg:py-16">
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="rounded-3xl border border-stone-200/80 dark:border-slate-800 bg-white/85 dark:bg-slate-900/70 backdrop-blur-sm shadow-sm p-6 sm:p-8">
            <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-stone-900 dark:text-slate-100 tracking-tight">Pass Sangha List</h1>
            <p class="mt-3 text-sm sm:text-base text-stone-600 dark:text-slate-300">
                The list below shows the latest generated pass sangha data.
            </p>
            @if($generatedAt)
                <p class="mt-2 text-xs text-stone-500 dark:text-slate-400">Generated at: {{ \Illuminate\Support\Carbon::parse($generatedAt)->format('M d, Y H:i') }}</p>
            @endif
        </div>

        <div class="rounded-3xl border border-stone-200/80 dark:border-slate-800 bg-white/85 dark:bg-slate-900/70 backdrop-blur-sm shadow-sm p-6 sm:p-8">
            <h2 class="font-heading text-2xl sm:text-3xl font-semibold text-stone-900 dark:text-slate-100 mb-4">All Pass Sanghas</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-stone-200 dark:border-slate-700">
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">No.</th>
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">{{ t('score_candidate_ref_label', 'Student Id') }}</th>
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">{{ t('desk_number', 'Desk No.') }}</th>
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">Sangha</th>
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">Monastery</th>
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">Father</th>
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">NRC</th>
                            <th class="text-left py-3 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($passSanghas as $sangha)
                            <tr class="border-b border-stone-100 dark:border-slate-800">
                                <td class="py-3 pr-4 text-stone-500 dark:text-slate-400">{{ $loop->iteration }}</td>
                                <td class="py-3 pr-4 text-stone-700 dark:text-slate-300">{{ $sangha['candidate_ref'] ?? '—' }}</td>
                                <td class="py-3 pr-4 font-semibold tabular-nums text-yellow-700 dark:text-yellow-400">{{ isset($sangha['desk_number']) && $sangha['desk_number'] !== null ? $sangha['desk_number'] : '—' }}</td>
                                <td class="py-3 pr-4 font-medium text-stone-900 dark:text-slate-100">{{ $sangha['name'] ?? '—' }}</td>
                                <td class="py-3 pr-4 text-stone-700 dark:text-slate-300">{{ $sangha['monastery_name'] ?? '—' }}</td>
                                <td class="py-3 pr-4 text-stone-700 dark:text-slate-300">{{ $sangha['father_name'] ?? '—' }}</td>
                                <td class="py-3 pr-4 text-stone-700 dark:text-slate-300">{{ $sangha['nrc_number'] ?? '—' }}</td>
                                <td class="py-3"><span class="inline-flex rounded-full bg-emerald-100 dark:bg-emerald-900/40 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">Pass</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-stone-500 dark:text-slate-400">No generated data yet. Admin needs to click Generate in Scores > Pass screen.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>
@endsection

