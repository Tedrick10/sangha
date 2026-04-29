@extends('website.layout')

@section('title', $exam->name.' — '.t('exam_eligible_page_title', 'Exam candidates'))

@section('content')
<section class="relative py-10 sm:py-14 lg:py-16">
    <div class="max-w-6xl mx-auto space-y-6">
        <p>
            <a href="{{ route('website.exam-eligible.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-yellow-800 dark:text-yellow-300 hover:underline">
                @include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4']) {{ t('exam_eligible_back_to_list', 'All exams') }}
            </a>
        </p>
        <div class="rounded-3xl border border-stone-200/80 dark:border-slate-800 bg-white/85 dark:bg-slate-900/70 backdrop-blur-sm shadow-sm p-6 sm:p-8">
            <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-stone-900 dark:text-slate-100 tracking-tight">{{ $exam->name }}</h1>
            <p class="mt-2 text-sm text-stone-600 dark:text-slate-400">
                @if($exam->exam_date)
                    {{ $exam->exam_date->format('M j, Y') }}
                @else
                    —
                @endif
                @if($exam->examType)
                    <span class="text-stone-400 dark:text-slate-500"> · </span>{{ $exam->examType->name }}
                @endif
            </p>
            <p class="mt-3 text-sm sm:text-base text-stone-600 dark:text-slate-300">
                {{ t('exam_eligible_intro', 'Candidates approved for entrance (assigned desk numbers) for this examination.') }}
            </p>
            @if($snapshot)
                <p class="mt-2 text-xs text-stone-500 dark:text-slate-400">{{ t('exam_eligible_generated_at', 'Published at') }}: {{ \Illuminate\Support\Carbon::parse($snapshot['generated_at'])->format('M d, Y H:i') }}</p>
            @endif
        </div>

        <div class="rounded-3xl border border-stone-200/80 dark:border-slate-800 bg-white/85 dark:bg-slate-900/70 backdrop-blur-sm shadow-sm p-6 sm:p-8">
            <h2 class="font-heading text-2xl sm:text-3xl font-semibold text-stone-900 dark:text-slate-100 mb-4">{{ t('exam_eligible_list_heading', 'Eligible candidates') }}</h2>
            @php
                $deskPrefix = $exam->desk_number_prefix ?? '';
            @endphp
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-stone-200 dark:border-slate-700">
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">No.</th>
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">{{ t('roll_number', 'Roll Number') }}</th>
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                                <span class="block">{{ t('desk_number_short', 'Desk No.') }}</span>
                                <span class="block normal-case text-[10px] leading-tight">({{ t('exam_roll_number', 'Exam Roll Number') }})</span>
                            </th>
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">{{ t('name', 'Name') }}</th>
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">{{ t('score_table_father_nrc', 'Father / NRC') }}</th>
                            <th class="text-left py-3 pr-4 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">{{ t('monastery', 'Monastery') }}</th>
                            <th class="text-left py-3 text-stone-500 dark:text-slate-400 uppercase tracking-wide text-xs">{{ t('exam', 'Exam') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($snapshot ?? [])['candidates'] ?? [] as $row)
                            <tr class="border-b border-stone-100 dark:border-slate-800">
                                <td class="py-3 pr-4 text-stone-500 dark:text-slate-400">{{ $loop->iteration }}</td>
                                <td class="py-3 pr-4 font-mono tabular-nums text-stone-700 dark:text-slate-300">
                                    {{ ($row['eligible_roll_number'] ?? '') !== '' ? $row['eligible_roll_number'] : '—' }}
                                </td>
                                <td class="py-3 pr-4 font-semibold tabular-nums text-yellow-700 dark:text-yellow-400">
                                    @if(array_key_exists('desk_number', $row) && $row['desk_number'] !== null && $row['desk_number'] !== '')
                                        {{ $deskPrefix }}{{ str_pad((string) $row['desk_number'], 6, '0', STR_PAD_LEFT) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 pr-4 font-medium text-stone-900 dark:text-slate-100">{{ $row['name'] ?? '—' }}</td>
                                <td class="py-3 pr-4 text-stone-700 dark:text-slate-300">
                                    @php
                                        $father = trim((string) ($row['father_name'] ?? ''));
                                        $nrc = trim((string) ($row['nrc_number'] ?? ''));
                                    @endphp
                                    @if($father === '' && $nrc === '')
                                        —
                                    @else
                                        <div class="leading-tight">
                                            <div>{{ $father !== '' ? $father : '—' }}</div>
                                            <div class="text-xs text-stone-500 dark:text-slate-400">{{ $nrc !== '' ? $nrc : '—' }}</div>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-stone-700 dark:text-slate-300">{{ $row['monastery_name'] ?? '—' }}</td>
                                <td class="py-3 text-stone-700 dark:text-slate-300">{{ $snapshot['exam_name'] ?? $exam->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-stone-500 dark:text-slate-400">
                                    {{ t('exam_eligible_empty', 'No list has been published yet, or there are no approved candidates for this exam. An administrator can click Generate on the exam in the admin panel.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>
@endsection
