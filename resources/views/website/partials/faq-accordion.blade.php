{{-- FAQ uses a text-first layout (not an accordion). Slug `faq` always loads this partial. --}}
<div class="not-prose faq-page text-left space-y-14 sm:space-y-16">
    <section class="space-y-8" aria-labelledby="faq-heading-registration">
        <h2 id="faq-heading-registration" class="font-heading text-xl sm:text-2xl font-semibold text-stone-900 dark:text-slate-50 tracking-tight pl-[1.125rem] border-l-[3px] border-amber-500 dark:border-amber-400">
            {{ t('fallback_faq_section_reg', 'Registration') }}
        </h2>
        <div class="space-y-9 sm:space-y-10">
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_monastery_reg', 'How do monasteries register?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_monastery_reg', 'Open the home page and choose Register monastery. Complete the form with your institution’s details. An administrator usually reviews new accounts before login is enabled; you will be notified when your monastery is approved.') }}</p>
            </div>
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_sangha_reg', 'How do Sangha candidates register?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_sangha_reg', 'Choose Register Sangha on the home page, select the monastery you belong to, and submit the requested information. Your coordinator or monastery portal may need to confirm eligibility before you can sit examinations.') }}</p>
            </div>
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_approval_time', 'How long does approval take?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_approval_time', 'Timing depends on your examination office. In busy seasons it may take several working days. If you have an urgent sitting date, mention it when you contact the secretariat.') }}</p>
            </div>
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_wrong_monastery', 'I chose the wrong monastery—what should I do?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_wrong_monastery', 'Do not create duplicate accounts. Contact your monastery coordinator or the examination office through the Contact page so your record can be corrected by an administrator.') }}</p>
            </div>
        </div>
    </section>

    <section class="space-y-8 pt-2 border-t border-stone-200/80 dark:border-slate-700/80" aria-labelledby="faq-heading-exams">
        <h2 id="faq-heading-exams" class="font-heading text-xl sm:text-2xl font-semibold text-stone-900 dark:text-slate-50 tracking-tight pl-[1.125rem] border-l-[3px] border-amber-500 dark:border-amber-400">
            {{ t('fallback_faq_section_exams', 'Examinations') }}
        </h2>
        <div class="space-y-9 sm:space-y-10">
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_schedule', 'Where is the schedule?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_schedule', 'Use the main menu: Examinations → Examination Schedule. Approved examinations from the database appear there with dates and venues where they have been entered. Coordinators should always confirm the final hall with candidates.') }}</p>
            </div>
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_results', 'When are results published?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_results', 'Subject marks are moderated in the admin panel. Sangha members typically review their own results in the Sangha portal after release. Public pass lists, if used, appear only when administrators choose to publish them.') }}</p>
            </div>
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_admission', 'What should I bring on examination day?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_admission', 'Follow the instructions issued for your sitting. Usually you will need identification and any admission slip or registration confirmation your monastery provides. Arrive in good time; invigilators may refuse entry if rules are not met.') }}</p>
            </div>
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_venue_change', 'The venue or date changed—where is the update?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_venue_change', 'Official changes should be reflected on the Examination Schedule once administrators update the record. Your monastery coordinator is the first point of contact for last-minute hall arrangements.') }}</p>
            </div>
        </div>
    </section>

    <section class="space-y-8 pt-2 border-t border-stone-200/80 dark:border-slate-700/80" aria-labelledby="faq-heading-accounts">
        <h2 id="faq-heading-accounts" class="font-heading text-xl sm:text-2xl font-semibold text-stone-900 dark:text-slate-50 tracking-tight pl-[1.125rem] border-l-[3px] border-amber-500 dark:border-amber-400">
            {{ t('fallback_faq_section_accounts', 'Accounts & portals') }}
        </h2>
        <div class="space-y-9 sm:space-y-10">
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_forgot_password', 'I forgot my password—how do I reset it?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_forgot_password', 'Use the login page for your account type (monastery or Sangha) and follow the password recovery or reset flow if your deployment enables it. If you are locked out, contact the examination office—only authorised staff should reset credentials on your behalf.') }}</p>
            </div>
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_who_sees_scores', 'Who can see my examination scores?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_who_sees_scores', 'Typically, Sangha candidates see their own subject marks in the Sangha portal. Monastery coordinators may see lists for their institution where the system allows. Administrative staff use the admin panel for moderation and records. Exact roles depend on how your organisation configures access.') }}</p>
            </div>
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_monastery_portal', 'What is the Monastery Portal for?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_monastery_portal', 'Approved monasteries use it to manage Sangha registrations, view examination-related lists, and exchange messages with administrators where messaging is enabled. It is separate from the public website.') }}</p>
            </div>
        </div>
    </section>

    <section class="space-y-8 pt-2 border-t border-stone-200/80 dark:border-slate-700/80" aria-labelledby="faq-heading-tech">
        <h2 id="faq-heading-tech" class="font-heading text-xl sm:text-2xl font-semibold text-stone-900 dark:text-slate-50 tracking-tight pl-[1.125rem] border-l-[3px] border-amber-500 dark:border-amber-400">
            {{ t('fallback_faq_section_tech', 'Technical') }}
        </h2>
        <div class="space-y-9 sm:space-y-10">
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_theme', 'Light or dark theme not updating?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_theme', 'The header control should switch appearance immediately. Your preference is stored in the browser for the next visit. If nothing changes, check that cookies are allowed for this site, try another browser, or disable extensions that block local storage.') }}</p>
            </div>
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_clear_cache', 'The page looks broken or outdated.') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_clear_cache', 'Perform a hard refresh (most browsers: Ctrl+Shift+R or Cmd+Shift+R). If assets were recently deployed, wait a minute and try again. Persistent issues may be due to a corporate proxy caching old files.') }}</p>
            </div>
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_mobile', 'Can I use a phone or tablet?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_mobile', 'The public site and portals are responsive. For long forms and document uploads, a desktop browser is more reliable. Ensure you have a stable connection before submitting registrations.') }}</p>
            </div>
        </div>
    </section>

    <section class="space-y-8 pt-2 border-t border-stone-200/80 dark:border-slate-700/80" aria-labelledby="faq-heading-data">
        <h2 id="faq-heading-data" class="font-heading text-xl sm:text-2xl font-semibold text-stone-900 dark:text-slate-50 tracking-tight pl-[1.125rem] border-l-[3px] border-amber-500 dark:border-amber-400">
            {{ t('fallback_faq_section_data', 'Data & privacy') }}
        </h2>
        <div class="space-y-9 sm:space-y-10">
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_what_stored', 'What personal data does the platform store?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_what_stored', 'At minimum, registration and examination workflows need identity details, monastery links, and examination outcomes. Technical logs may include IP addresses and session data. See the Privacy Policy for a structured description and replace demo wording with your organisation’s official text.') }}</p>
            </div>
            <div class="space-y-3 max-w-4xl">
                <h3 class="text-base sm:text-lg font-semibold text-stone-900 dark:text-slate-100 leading-snug">{{ t('fallback_faq_q_contact_privacy', 'Who do I contact about privacy?') }}</h3>
                <p class="text-[1.0625rem] sm:text-[1.075rem] leading-[1.8] text-stone-600 dark:text-slate-300">{{ t('fallback_faq_a_contact_privacy', 'Use the Contact page and mark your message as a privacy or data request. Your examination office should publish the correct address and process for formal rights requests.') }}</p>
            </div>
        </div>
    </section>
</div>
