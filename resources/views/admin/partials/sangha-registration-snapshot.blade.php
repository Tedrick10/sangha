{{--
    Snapshot of Sangha registration for admin inline panels (e.g. exam form approved student).
    @var \App\Models\Sangha $sangha
    @var \Illuminate\Support\Collection $sanghaFieldMeta  keyed by slug
    @var \Illuminate\Support\Collection $sanghaExtraFieldDefinitions  non-built-in CustomField models
--}}
@php
    $meta = fn (string $slug): ?\App\Models\CustomField => $sanghaFieldMeta->get($slug);
    $ms = $sangha->moderationStatus();
@endphp
<div class="space-y-4">
    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ t('sangha_registration_snapshot_title', 'Registration details') }}</p>
    <dl class="grid gap-x-6 gap-y-3 sm:grid-cols-2 text-sm">
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'monastery_id'))
            <div class="sm:col-span-2">
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('monastery_id')?->name ?? t('monastery', 'Monastery') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100">{{ $sangha->monastery?->name ?? '—' }}</dd>
            </div>
        @endif
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'name'))
            <div>
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('name')?->name ?? t('name') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100 break-words">{{ $sangha->name ?: '—' }}</dd>
            </div>
        @endif
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'father_name'))
            <div>
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('father_name')?->name ?? t('score_father_name_label', 'Father name') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100 break-words">{{ $sangha->father_name ?: '—' }}</dd>
            </div>
        @endif
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'nrc_number'))
            <div>
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('nrc_number')?->name ?? t('score_nrc_label', 'NRC number') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100 break-words">{{ $sangha->nrc_number ?: '—' }}</dd>
            </div>
        @endif
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'username'))
            <div>
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('username')?->name ?? t('user_id', 'Student Id') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100 font-mono text-xs">{{ $sangha->username ?: '—' }}</dd>
            </div>
        @endif
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'exam_id'))
            <div class="sm:col-span-2">
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('exam_id')?->name ?? t('exam') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100">
                    @if($sangha->exam)
                        {{ $sangha->exam->name }}{{ $sangha->exam->exam_date ? ' ('.$sangha->exam->exam_date->format('M d, Y').')' : '' }}
                    @else
                        —
                    @endif
                </dd>
            </div>
        @endif
        @if(! \App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'description'))
            <div class="sm:col-span-2">
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $meta('description')?->name ?? t('description') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100 whitespace-pre-wrap break-words">{{ $sangha->description ?: '—' }}</dd>
            </div>
        @endif
        <div>
            <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ t('sangha_moderation_status_label', 'Application status') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-100">
                @if($ms === 'approved')
                    {{ t('status_approved', 'Approved') }}
                @elseif($ms === 'needed_update')
                    {{ t('status_needed_update', 'Needed Update') }}
                @elseif($ms === 'rejected')
                    {{ t('status_rejected', 'Rejected') }}
                @else
                    {{ t('status_pending', 'Pending') }}
                @endif
            </dd>
        </div>
        @if(in_array($ms, ['rejected', 'needed_update'], true) && filled($sangha->rejection_reason))
            <div class="sm:col-span-2">
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $ms === 'needed_update' ? t('needed_update_note', 'Needed update note') : t('rejection_note', 'Rejection note') }}</dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100 whitespace-pre-wrap break-words">{{ $sangha->rejection_reason }}</dd>
            </div>
        @endif
        @if(filled($sangha->desk_number))
            <div>
                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <span class="block">{{ t('desk_number_short', 'Desk No.') }}</span>
                    <span class="block font-normal normal-case text-[10px] leading-tight">({{ t('exam_roll_number', 'Exam Roll Number') }})</span>
                </dt>
                <dd class="mt-1 text-slate-900 dark:text-slate-100 font-mono text-xs">{{ ($sangha->exam?->desk_number_prefix ?? '') . str_pad((string) $sangha->desk_number, 6, '0', STR_PAD_LEFT) }}</dd>
            </div>
        @endif
    </dl>

    @if($sanghaExtraFieldDefinitions->isNotEmpty())
        <div class="border-t border-slate-200/70 dark:border-slate-700/60 pt-4">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">{{ t('custom_fields', 'Custom Fields') }}</p>
            <dl class="grid gap-x-6 gap-y-3 sm:grid-cols-2 text-sm">
                @foreach($sanghaExtraFieldDefinitions as $cf)
                    @php
                        $cv = $sangha->getCustomFieldValue($cf->slug);
                    @endphp
                    <div class="{{ $cf->type === 'textarea' ? 'sm:col-span-2' : '' }}">
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $cf->name }}</dt>
                        <dd class="mt-1 text-slate-900 dark:text-slate-100 break-words">
                            @if(! filled($cv))
                                <span class="text-slate-400 dark:text-slate-500">—</span>
                            @elseif($cf->type === 'checkbox')
                                {{ in_array((string) $cv, ['1', 'true'], true) ? t('yes', 'Yes') : t('no', 'No') }}
                            @elseif(in_array($cf->type, ['media', 'document', 'video'], true))
                                <a href="{{ route('admin.sanghas.custom-field-file', ['sangha' => $sangha->id, 'customField' => $cf->id]) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-amber-800 dark:text-amber-300 hover:underline font-medium">
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
        <a href="{{ route('admin.sanghas.edit', $sangha) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-amber-700 dark:text-amber-300 hover:text-amber-700 dark:hover:text-amber-200">
            @include('partials.icon', ['name' => 'pencil', 'class' => 'w-4 h-4 shrink-0'])
            {{ t('admin_open_sangha_full_edit', 'Open full edit') }}
        </a>
    </div>
</div>
