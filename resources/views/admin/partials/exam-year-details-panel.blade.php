{{--
    @var int $yearInt
    @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exam> $examsForYear
--}}
<div class="space-y-3">
    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
        {{ str_replace(':year', (string) $yearInt, t('exams_scheduled_in_year_heading', 'Exams scheduled in :year')) }}
    </p>
    @if($examsForYear->isEmpty())
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ t('no_exams_in_year_catalog', 'No active exams with a date are configured for this year.') }}</p>
    @else
        <ul class="divide-y divide-slate-200/80 dark:divide-slate-700/60 rounded-lg border border-slate-200/70 dark:border-slate-600/50 overflow-hidden">
            @foreach($examsForYear as $exam)
                <li class="bg-white/50 px-3 py-2.5 dark:bg-slate-900/30">
                    <a href="{{ route('admin.exams.edit', $exam) }}" class="font-medium text-amber-800 hover:text-amber-900 hover:underline dark:text-amber-200 dark:hover:text-amber-100">
                        {{ $exam->name }}
                    </a>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                        @if($exam->exam_date)
                            <time datetime="{{ $exam->exam_date->toIso8601String() }}">{{ $exam->exam_date->format('M d, Y') }}</time>
                        @endif
                        @if($exam->examType)
                            <span class="text-slate-400 dark:text-slate-500"> · </span>{{ $exam->examType->name }}
                        @endif
                        @if(filled($exam->location))
                            <span class="text-slate-400 dark:text-slate-500"> · </span>{{ $exam->location }}
                        @endif
                    </p>
                </li>
            @endforeach
        </ul>
    @endif
</div>
