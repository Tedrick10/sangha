@switch($page->slug)
    @case('about')
        <h2>{{ t('fallback_about_mission_h', 'Our mission') }}</h2>
        <p>{{ t('fallback_about_mission_p', 'Sangha Exam supports Pali, Vinaya, and Dhamma examinations for monastic communities—with secure portals, clear schedules, and respectful handling of results.') }}</p>
        <h2>{{ t('fallback_about_offer_h', 'What we offer') }}</h2>
        <ul>
            <li>{{ t('fallback_about_li1', 'Monastery and Sangha workspaces with role-appropriate views') }}</li>
            <li>{{ t('fallback_about_li2', 'Central exam types, subjects, and moderated scoring') }}</li>
            <li>{{ t('fallback_about_li3', 'Public pages for policies, help, and examination calendars') }}</li>
        </ul>
        @break
    @case('contact')
        <h2>{{ t('fallback_contact_h', 'Contact the secretariat') }}</h2>
        <p>{{ t('fallback_contact_p', 'For registration, technical issues, or examination enquiries—typical response within 1–2 business days (demo).') }}</p>
        <div class="not-prose my-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-stone-200 dark:border-slate-600 bg-stone-50 dark:bg-slate-800/60 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-slate-400 mb-1">{{ t('fallback_contact_email_l', 'Email') }}</p>
                <a href="mailto:contact@sanghaexam.org" class="text-amber-700 dark:text-amber-400 font-medium">contact@sanghaexam.org</a>
            </div>
            <div class="rounded-2xl border border-stone-200 dark:border-slate-600 bg-stone-50 dark:bg-slate-800/60 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-slate-400 mb-1">{{ t('fallback_contact_phone_l', 'Phone') }}</p>
                <p class="font-medium text-stone-900 dark:text-slate-100">+95 9 123 456 789</p>
            </div>
        </div>
        @break
    @case('privacy')
        <h2>{{ t('fallback_privacy_summary', 'Summary') }}</h2>
        <p>{{ t('fallback_privacy_summary_p', 'We collect only data needed to run examinations and portals. Access is limited to authorized staff.') }}</p>
        <h2>{{ t('fallback_privacy_rights', 'Your rights') }}</h2>
        <ul>
            <li>{{ t('fallback_privacy_r1', 'Request correction of personal data') }}</li>
            <li>{{ t('fallback_privacy_r2', 'Ask how your information is used') }}</li>
        </ul>
        @break
    @case('accessibility')
        <h2>{{ t('fallback_a11y_h', 'Our commitment') }}</h2>
        <p>{{ t('fallback_a11y_p', 'We aim for readable typography, solid contrast in light and dark themes, and keyboard-friendly navigation on public pages.') }}</p>
        <ul>
            <li>{{ t('fallback_a11y_li1', 'Semantic headings and landmarks') }}</li>
            <li>{{ t('fallback_a11y_li2', 'Visible focus styles on controls') }}</li>
        </ul>
        @break
    @case('guidelines')
        <h2>{{ t('fallback_guide_before', 'Before examination day') }}</h2>
        <ul>
            <li>{{ t('fallback_guide_b1', 'Confirm registration with your monastery coordinator.') }}</li>
            <li>{{ t('fallback_guide_b2', 'Arrive at least 30 minutes before the published start.') }}</li>
        </ul>
        <h2>{{ t('fallback_guide_during', 'During the exam') }}</h2>
        <p>{{ t('fallback_guide_during_p', 'Silence phones and smart watches; follow invigilator instructions at all times.') }}</p>
        @break
    @case('syllabus')
        <h2>{{ t('fallback_syl_h', 'Structure') }}</h2>
        <p>{{ t('fallback_syl_p', 'Papers are organised by level and subject group—typically Pali, Vinaya, and Dhamma components.') }}</p>
        <h2>{{ t('fallback_syl_official', 'Official syllabi') }}</h2>
        <p>{{ t('fallback_syl_official_p', 'Topic outlines are issued per cycle by your board; coordinators distribute PDFs to candidates.') }}</p>
        @break
    @case('news')
        <h2>{{ t('fallback_news_h', 'Latest updates') }}</h2>
        <p>{{ t('fallback_news_p', 'News items can be edited in the admin Website section. Below is sample copy for demos.') }}</p>
        <p><strong>{{ t('fallback_news_item1', 'Registration window') }}</strong> — {{ t('fallback_news_item1_p', 'Monasteries may begin submitting candidate lists for the next sitting.') }}</p>
        @break
    @case('events')
        <h2>{{ t('fallback_events_h', 'Calendar highlights') }}</h2>
        <p>{{ t('fallback_events_p', 'Upcoming sittings from your database appear above; use this area for narrative context and ceremonies.') }}</p>
        @break
    @case('sitemap')
        <h2>{{ t('fallback_sitemap_h', 'How to use this page') }}</h2>
        <p>{{ t('fallback_sitemap_p', 'The grouped links above mirror your published pages. Replace this note in the admin panel when you are ready.') }}</p>
        @break
    @case('partners')
        <h2>{{ t('fallback_partners_h', 'Partnership model') }}</h2>
        <p>{{ t('fallback_partners_p', 'Partner monasteries host halls, nominate invigilators, and validate lists. Live partner cards appear above when your database is seeded.') }}</p>
        @break
    @case('donate')
        <h2>{{ t('fallback_donate_h', 'Support the program') }}</h2>
        <p>{{ t('fallback_donate_p', 'Donations help cover materials, halls, and platform care. Replace with your official channels.') }}</p>
        <ul>
            <li><strong>{{ t('fallback_donate_bank', 'Bank transfer:') }}</strong> {{ t('fallback_donate_bank_p', 'Demo Bank — Reference: DONATION') }}</li>
        </ul>
        @break
    @case('volunteer')
        <h2>{{ t('fallback_vol_h', 'Volunteer with us') }}</h2>
        <p>{{ t('fallback_vol_p', 'Lay supporters help with registration desks, timing, and hall setup. Training is offered before each season.') }}</p>
        @break
    @case('resources')
        <h2>{{ t('fallback_res_h', 'Downloads & links') }}</h2>
        <ul>
            <li>{{ t('fallback_res_li1', 'Candidate handbook (via monastery coordinator)') }}</li>
            <li>{{ t('fallback_res_li2', 'Timetable template for hall supervisors') }}</li>
        </ul>
        @break
    @case('gallery')
        <h2>{{ t('fallback_gal_h', 'Gallery') }}</h2>
        <p>{{ t('fallback_gal_p', 'Photo highlights from ceremonies and examination days can be placed here or linked from your media library.') }}</p>
        @break
    @case('terms-of-use')
        <h2>{{ t('fallback_terms_h', 'Fair use') }}</h2>
        <p>{{ t('fallback_terms_p', 'Use this platform respectfully. Administrative access is limited to authorized staff.') }}</p>
        @break
    @case('past-papers')
        <h2>{{ t('fallback_papers_h', 'Past papers') }}</h2>
        <p>{{ t('fallback_papers_p', 'Representative questions from previous cycles help candidates prepare. Request access through your coordinator.') }}</p>
        @break
    @default
        <p>{{ t('fallback_generic_p', 'This page is ready for your content. Open the admin Website section to replace placeholder text with your official copy.') }}</p>
@endswitch
