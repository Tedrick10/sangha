{{--
    Snapshot of an Exam for admin inline panels (monastery exam form request).
    @var \App\Models\Exam $exam
    @var \Illuminate\Support\Collection $examFieldMeta  keyed by slug
    @var \Illuminate\Support\Collection $examExtraFieldDefinitions
--}}
@php
    $meta = fn (string $slug): ?\App\Models\CustomField => $examFieldMeta->get($slug);
@endphp
<div class="space-y-4">
    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ t('exam_details_snapshot_title', 'Exam information') }}</p>
    <dl class="grid gap-x-6 gap-y-3 sm:grid-cols-2 text-sm">
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('exam', 'name'))
            <div class="sm:col-span-2">
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('name')?->name ?? t('name') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100 break-words font-medium">{{ $exam->name ?: '—' }}</dd>
            </div>
        @endif
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('exam', 'exam_date'))
            <div>
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('exam_date')?->name ?? t('exam_date', 'Exam date') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100">
                    @if($exam->exam_date)
                        <time datetime="{{ $exam->exam_date->toIso8601String() }}">{{ $exam->exam_date->format('M d, Y') }}</time>
                    @else
                        —
                    @endif
                </dd>
            </div>
        @endif
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('exam', 'exam_type_id'))
            <div>
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('exam_type_id')?->name ?? t('exam_type') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100">{{ $exam->examType?->name ?? '—' }}</dd>
            </div>
        @endif
        @if($exam->relationLoaded('subjects') && $exam->subjects->isNotEmpty())
            <div class="sm:col-span-2">
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ t('subjects', 'Subjects') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100">
                    <ul class="list-inside list-disc space-y-0.5 text-sm">
                        @foreach($exam->subjects->sortBy('name') as $sub)
                            <li>{{ $sub->name }}</li>
                        @endforeach
                    </ul>
                </dd>
            </div>
        @endif
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('exam', 'location'))
            <div class="sm:col-span-2">
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('location')?->name ?? t('location', 'Location') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100 whitespace-pre-wrap break-words">{{ $exam->location ?: '—' }}</dd>
            </div>
        @endif
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('exam', 'description'))
            <div class="sm:col-span-2">
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('description')?->name ?? t('description') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100 whitespace-pre-wrap break-words">{{ $exam->description ?: '—' }}</dd>
            </div>
        @endif
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('exam', 'is_active'))
            <div>
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('is_active')?->name ?? t('active', 'Active') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100">{{ $exam->is_active ? t('yes', 'Yes') : t('no', 'No') }}</dd>
            </div>
        @endif
        <div>
            <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ t('exam_approved_column', 'Approved') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-100">{{ $exam->approved ? t('yes', 'Yes') : t('no', 'No') }}</dd>
        </div>
    </dl>

    @if($examExtraFieldDefinitions->isNotEmpty())
        <div class="border-t border-slate-200/70 dark:border-slate-700/60 pt-4">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">{{ t('custom_fields', 'Custom Fields') }}</p>
            <dl class="grid gap-x-6 gap-y-3 sm:grid-cols-2 text-sm">
                @foreach($examExtraFieldDefinitions as $cf)
                    @php
                        $cv = $exam->getCustomFieldValue($cf->slug);
                    @endphp
                    <div class="{{ $cf->type === 'textarea' ? 'sm:col-span-2' : '' }}">
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $cf->name }}</dt>
                        <dd class="mt-1 text-slate-900 dark:text-slate-100 break-words">
                            @if(! filled($cv))
                                <span class="text-slate-400 dark:text-slate-500">—</span>
                            @elseif($cf->type === 'checkbox')
                                {{ in_array((string) $cv, ['1', 'true'], true) ? t('yes', 'Yes') : t('no', 'No') }}
                            @elseif(in_array($cf->type, ['media', 'document', 'video'], true))
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($cv) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-amber-800 dark:text-amber-300 hover:underline font-medium">
                                    @include('partials.icon', ['name' => 'external-link', 'class' => 'w-3.5 h-3.5 shrink-0'])
                                    {{ basename($cv) }}
                                </a>
                            @else
                                <span class="whitespace-pre-wrap">{{ $cv }}</span>
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    @endif

    <div class="pt-2 border-t border-slate-200/70 dark:border-slate-700/60">
        <a href="{{ route('admin.exams.edit', $exam) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-amber-700 dark:text-amber-300 hover:text-amber-700 dark:hover:text-amber-200">
            @include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4 shrink-0'])
            {{ t('admin_open_exam_full_edit', 'Open full edit') }}
        </a>
    </div>
</div>
