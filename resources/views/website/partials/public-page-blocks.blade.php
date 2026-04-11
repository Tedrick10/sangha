{{-- Theme-specific live data & decorative blocks (shown above CMS body) --}}

@isset($sitemapPages, $sitemapSections)
    @php
        $allGroupedSlugs = collect($sitemapSections)->flatMap(fn ($s) => $s['slugs'])->unique()->values();
        $otherPages = $sitemapPages->filter(fn ($p) => ! $allGroupedSlugs->contains($p->slug));
        $sitemapIcons = [
            'exam-schedule' => 'calendar', 'guidelines' => 'clipboard-list', 'syllabus' => 'academic-cap',
            'past-papers' => 'book-open', 'about' => 'information-circle',
            'contact' => 'envelope', 'partners' => 'users', 'news' => 'newspaper', 'events' => 'megaphone',
            'gallery' => 'photograph', 'privacy' => 'shield-check', 'terms-of-use' => 'scale',
            'faq' => 'question-mark-circle', 'accessibility' => 'sparkles', 'resources' => 'folder',
            'donate' => 'heart', 'volunteer' => 'users', 'sitemap' => 'map',
        ];
    @endphp
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-8">
        @foreach($sitemapSections as $sec)
            @php
                $links = $sitemapPages->filter(fn ($p) => in_array($p->slug, $sec['slugs'], true))->values();
            @endphp
            @if($links->isNotEmpty())
                <div class="rounded-2xl border border-stone-200/90 dark:border-slate-600 bg-white/90 dark:bg-slate-800/80 p-5 shadow-sm">
                    <h2 class="font-heading text-sm font-semibold uppercase tracking-wider text-yellow-800 dark:text-yellow-300 mb-4">
                        {{ t($sec['label_key'], $sec['label_default']) }}
                    </h2>
                    <ul class="space-y-2">
                        @foreach($links as $navPage)
                            @php $ic = $sitemapIcons[$navPage->slug] ?? 'document-text'; @endphp
                            <li>
                                <a href="{{ route('website.page', $navPage->slug) }}" class="group flex items-center gap-2.5 rounded-xl px-2 py-2 text-sm text-stone-700 dark:text-slate-200 hover:bg-yellow-50/80 dark:hover:bg-slate-700/50 transition-colors">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-stone-100 dark:bg-slate-700/80 text-stone-600 dark:text-slate-300 group-hover:text-yellow-700 dark:group-hover:text-yellow-400">
                                        @include('partials.icon', ['name' => $ic, 'class' => 'w-4 h-4'])
                                    </span>
                                    <span class="font-medium leading-snug">{{ $navPage->title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </div>
    @if($otherPages->isNotEmpty())
        <div class="rounded-2xl border border-dashed border-stone-300 dark:border-slate-600 bg-stone-50/50 dark:bg-slate-800/40 p-5 mb-8">
            <h2 class="font-heading text-sm font-semibold text-stone-800 dark:text-slate-200 mb-3">{{ t('sitemap_section_other', 'Other pages') }}</h2>
            <ul class="flex flex-wrap gap-2">
                @foreach($otherPages as $navPage)
                    <li>
                        <a href="{{ route('website.page', $navPage->slug) }}" class="inline-flex items-center gap-1.5 rounded-full border border-stone-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-1.5 text-sm font-medium text-stone-700 dark:text-slate-200 hover:border-yellow-400/60 transition-colors">
                            @include('partials.icon', ['name' => 'document-text', 'class' => 'w-3.5 h-3.5 text-yellow-600 dark:text-yellow-400'])
                            {{ $navPage->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endisset

@isset($partnerMonasteries)
    @if($partnerMonasteries->isNotEmpty())
        <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($partnerMonasteries as $mon)
                <div class="rounded-2xl border border-stone-200 dark:border-slate-600 bg-gradient-to-br from-white to-stone-50/80 dark:from-slate-800 dark:to-slate-900/80 p-5 website-card-hover">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-100 dark:bg-cyan-900/40 text-cyan-800 dark:text-cyan-300">
                            @include('partials.icon', ['name' => 'home', 'class' => 'w-5 h-5'])
                        </span>
                        <div class="min-w-0">
                            <h3 class="font-heading font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ $mon->name }}</h3>
                            <p class="text-xs text-stone-500 dark:text-slate-400 mt-1">
                                @if($mon->city || $mon->region)
                                    {{ collect([$mon->city, $mon->region])->filter()->implode(' · ') }}
                                @else
                                    {{ t('partners_region_tbc', 'Region on file') }}
                                @endif
                            </p>
                            @if($mon->phone)
                                <p class="text-xs text-stone-600 dark:text-slate-400 mt-2 tabular-nums">{{ $mon->phone }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="mb-8 rounded-2xl border border-dashed border-stone-300 dark:border-slate-600 px-6 py-10 text-center text-stone-600 dark:text-slate-400 text-sm">
            {{ t('partners_empty', 'No active monasteries in the database yet—seed demo data or publish partners from the admin panel.') }}
        </div>
    @endif
@endisset

@isset($eventsExams)
    @if($eventsExams->isNotEmpty())
        <div class="mb-8">
            <h2 class="font-heading text-lg font-semibold text-stone-900 dark:text-slate-100 mb-4">{{ t('events_live_from_db', 'Upcoming & recent sittings (live data)') }}</h2>
            <div class="space-y-3">
                @foreach($eventsExams as $ex)
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 rounded-2xl border border-stone-200 dark:border-slate-600 bg-white/90 dark:bg-slate-800/60 px-4 py-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-100 dark:bg-violet-900/40 text-violet-800 dark:text-violet-200">
                            @include('partials.icon', ['name' => 'calendar', 'class' => 'w-6 h-6'])
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-stone-900 dark:text-slate-200">{{ $ex->name }}</p>
                            <p class="text-sm text-stone-600 dark:text-slate-400">
                                {{ $ex->exam_date ? $ex->exam_date->format('M j, Y') : t('home_exam_date_tbc', 'TBC') }}
                                @if($ex->monastery)
                                    <span class="text-stone-400 dark:text-slate-2000">·</span> {{ $ex->monastery->name }}
                                @endif
                            </p>
                        </div>
                        @if($ex->examType)
                            <span class="self-start sm:self-center text-xs font-medium uppercase tracking-wide text-violet-700 dark:text-violet-300 bg-violet-100/80 dark:bg-violet-900/30 px-2.5 py-1 rounded-lg">{{ $ex->examType->name }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endisset

@if(($pageTheme ?? '') === 'gallery')
    <div class="mb-8 grid grid-cols-2 md:grid-cols-3 gap-3">
        @foreach([
            ['from' => 'from-yellow-200', 'to' => 'to-yellow-400', 'l' => 'Opening ceremony'],
            ['from' => 'from-teal-200', 'to' => 'to-teal-500', 'l' => 'Examination hall'],
            ['from' => 'from-violet-200', 'to' => 'to-violet-500', 'l' => 'Results day'],
            ['from' => 'from-rose-200', 'to' => 'to-rose-400', 'l' => 'Study circle'],
            ['from' => 'from-sky-200', 'to' => 'to-sky-500', 'l' => 'Regional sitting'],
            ['from' => 'from-stone-300', 'to' => 'to-stone-500', 'l' => 'Archive'],
        ] as $tile)
            <div class="group relative aspect-[4/3] rounded-2xl overflow-hidden border border-stone-200/80 dark:border-slate-600">
                <div class="absolute inset-0 bg-gradient-to-br {{ $tile['from'] }} {{ $tile['to'] }} opacity-90 dark:opacity-80 group-hover:scale-105 transition-transform duration-500"></div>
                <div class="absolute inset-0 flex items-end p-4">
                    <span class="text-sm font-semibold text-white drop-shadow-sm">{{ $tile['l'] }}</span>
                </div>
            </div>
        @endforeach
    </div>
    <p class="text-xs text-stone-500 dark:text-slate-400 mb-8 not-prose">{{ t('gallery_demo_tiles', 'Demo colour tiles—replace with real images when your media library is connected.') }}</p>
@endif

@if(($pageTheme ?? '') === 'resources')
    <div class="not-prose mb-8 space-y-3">
        @foreach([
            ['icon' => 'document-text', 't' => 'Candidate handbook (PDF)', 's' => 'Issued per cycle · demo label'],
            ['icon' => 'clipboard-list', 't' => 'Hall supervisor timetable', 's' => 'Printable A4 template'],
            ['icon' => 'envelope', 't' => 'Regional coordinator list', 's' => 'Contact sheet · internal use'],
        ] as $row)
            <div class="flex items-center gap-4 rounded-2xl border border-stone-200 dark:border-slate-600 bg-stone-50/80 dark:bg-slate-800/50 px-4 py-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-900/35 text-orange-800 dark:text-orange-300">
                    @include('partials.icon', ['name' => $row['icon'], 'class' => 'w-5 h-5'])
                </span>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-stone-900 dark:text-slate-100">{{ $row['t'] }}</p>
                    <p class="text-sm text-stone-600 dark:text-slate-400">{{ $row['s'] }}</p>
                </div>
                <span class="text-xs font-medium text-yellow-700 dark:text-yellow-400 shrink-0 hidden sm:inline">{{ t('resources_demo_tag', 'Demo') }}</span>
            </div>
        @endforeach
    </div>
@endif

@if(($pageTheme ?? '') === 'timeline' && ($page->slug ?? '') === 'guidelines')
    <div class="not-prose mb-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border-l-4 border-yellow-500 bg-yellow-50/50 dark:bg-yellow-950/20 px-5 py-4">
            <p class="text-xs font-bold text-yellow-800 dark:text-yellow-300 uppercase tracking-wider">{{ t('guide_step_1', 'Step 1') }}</p>
            <p class="font-semibold text-stone-900 dark:text-slate-100 mt-1">{{ t('guide_step_1_t', 'Confirm registration') }}</p>
            <p class="text-sm text-stone-600 dark:text-slate-400 mt-2">{{ t('guide_step_1_p', 'Check status with your monastery coordinator before travel.') }}</p>
        </div>
        <div class="rounded-2xl border-l-4 border-teal-500 bg-teal-50/50 dark:bg-teal-950/20 px-5 py-4">
            <p class="text-xs font-bold text-teal-800 dark:text-teal-300 uppercase tracking-wider">{{ t('guide_step_2', 'Step 2') }}</p>
            <p class="font-semibold text-stone-900 dark:text-slate-100 mt-1">{{ t('guide_step_2_t', 'Arrive early') }}</p>
            <p class="text-sm text-stone-600 dark:text-slate-400 mt-2">{{ t('guide_step_2_p', 'Bring ID, admission materials, and follow hall signage.') }}</p>
        </div>
        <div class="rounded-2xl border-l-4 border-violet-500 bg-violet-50/50 dark:bg-violet-950/20 px-5 py-4">
            <p class="text-xs font-bold text-violet-800 dark:text-violet-300 uppercase tracking-wider">{{ t('guide_step_3', 'Step 3') }}</p>
            <p class="font-semibold text-stone-900 dark:text-slate-100 mt-1">{{ t('guide_step_3_t', 'During the paper') }}</p>
            <p class="text-sm text-stone-600 dark:text-slate-400 mt-2">{{ t('guide_step_3_p', 'Devices off; invigilator instructions only.') }}</p>
        </div>
    </div>
@endif

@if(($pageTheme ?? '') === 'papers')
    <div class="not-prose mb-8 flex items-center gap-3 rounded-2xl bg-gradient-to-r from-indigo-950/90 to-slate-900 text-indigo-100 px-5 py-4 border border-indigo-800/50">
        @include('partials.icon', ['name' => 'book-open', 'class' => 'w-8 h-8 text-indigo-300 shrink-0'])
        <div>
            <p class="font-heading font-semibold text-white">{{ t('papers_shelf_title', 'Reading room') }}</p>
            <p class="text-sm text-indigo-200/90">{{ t('papers_shelf_sub', 'Past papers are distributed by coordinators—this strip is a visual cue for catalogue-style content.') }}</p>
        </div>
    </div>
@endif
