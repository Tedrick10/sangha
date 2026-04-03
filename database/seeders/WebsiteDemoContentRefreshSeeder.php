<?php

namespace Database\Seeders;

use App\Models\Website;
use Illuminate\Database\Seeder;

/**
 * Refreshes public website page HTML for demos (no user/score wipe).
 * Run: php artisan db:seed --class=WebsiteDemoContentRefreshSeeder
 */
class WebsiteDemoContentRefreshSeeder extends Seeder
{
    public function run(): void
    {
        $y = (int) date('Y');
        $pages = [
            'home' => [
                'title' => 'Home',
                'sort_order' => 0,
                'content' => '',
            ],
            'about' => [
                'title' => 'About Us',
                'sort_order' => 1,
                'content' => '<h2>Our mission</h2><p>Sangha Exam is a dedicated platform for organizing <strong>Pali</strong>, <strong>Vinaya</strong>, and <strong>Dhamma</strong> examinations for monastic communities. We help monasteries register candidates, publish schedules, record scores, and share results with clarity and respect.</p><h2>What we offer</h2><ul><li>Secure portals for monasteries and Sangha members</li><li>Centralized exam types, subjects, and score management</li><li>Public pages for schedules, policies, and pass lists</li><li>Multilingual interface options for regional use</li></ul><h2>Who we serve</h2><p>Partner monasteries, examination boards, and candidates use the system for end-to-end exam cycles—from registration to publication of outcomes.</p>',
            ],
            'contact' => [
                'title' => 'Contact',
                'sort_order' => 2,
                'content' => '<h2>Contact the secretariat</h2><p>For registration support, technical issues, or examination enquiries, reach us through the channels below. Typical response time is <strong>1–2 business days</strong>.</p><div class="not-prose my-6 grid gap-4 sm:grid-cols-2"><div class="rounded-2xl border border-stone-200 dark:border-slate-600 bg-stone-50 dark:bg-slate-800/60 p-5"><p class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-slate-400 mb-1">Email</p><a href="mailto:contact@sanghaexam.org" class="text-amber-700 dark:text-amber-400 font-medium">contact@sanghaexam.org</a></div><div class="rounded-2xl border border-stone-200 dark:border-slate-600 bg-stone-50 dark:bg-slate-800/60 p-5"><p class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-slate-400 mb-1">Phone</p><p class="font-medium text-stone-900 dark:text-slate-100">+95 9 123 456 789</p></div></div><h2>Office hours</h2><p>Monday–Friday, 09:00–17:00 (local time). During examination periods, extended support may be announced on the home page.</p>',
            ],
            'exam-schedule' => [
                'title' => 'Examination Schedule',
                'sort_order' => 3,
                'content' => '<p>The <strong>table below</strong> is generated automatically from approved, active examinations in the database. Use it in demos to show real venues, monasteries, and dates. Static notes can still be edited here in the admin panel.</p><p>Monastery coordinators should confirm final hall assignments before the exam date.</p>',
            ],
            'privacy' => [
                'title' => 'Privacy Policy',
                'sort_order' => 4,
                'content' => '<h2>Summary</h2><p>We collect only the information needed to run examinations and portals (names, monastery affiliation, scores, and contact details). Data is stored securely and accessed by authorized staff.</p><h2>Your rights</h2><ul><li>Request correction of personal data</li><li>Ask how your information is used</li><li>Withdraw consent where applicable, subject to exam rules</li></ul><h2>Cookies & sessions</h2><p>The site uses essential cookies and session storage for login, language, and theme preferences. We do not use third-party advertising trackers on the public site.</p>',
            ],
            'faq' => [
                'title' => 'FAQ',
                'sort_order' => 5,
                'content' => '<h2>Registration</h2><p><strong>How do monasteries register?</strong> Use the <em>Register monastery</em> option on the home page, then sign in after approval.</p><p><strong>How do Sangha candidates register?</strong> Choose <em>Register Sangha</em>, select your monastery, and complete the form.</p><h2>Examinations</h2><p><strong>Where is the schedule?</strong> Open <strong>Examination Schedule</strong> under Examinations—the list is pulled from the database.</p><p><strong>When are results published?</strong> After moderation in the admin panel; candidates see marks in the Sangha portal.</p><h2>Technical</h2><p><strong>Light or dark theme not updating?</strong> The header theme control updates instantly without a full page reload; your choice is saved for the next visit—ensure cookies are enabled for this domain.</p>',
            ],
            'results' => [
                'title' => 'Results',
                'sort_order' => 12,
                'content' => '<h2>Examination results</h2><p>Official results are published after moderation. In this demo, open the <strong>admin panel</strong> to manage scores and use a <strong>Sangha login</strong> to view the student dashboard.</p><h2>Public pass lists</h2><p>Published pass lists appear under <strong>Pass Sangha List</strong> when enabled by administrators.</p>',
            ],
            'registration' => [
                'title' => 'Registration',
                'sort_order' => 13,
                'content' => '<h2>Who can register</h2><p><strong>Monasteries</strong> create an account to manage candidates and exams. <strong>Sangha members</strong> register under their monastery to sit examinations and view results.</p><h2>How to start</h2><p>From the <strong>home page</strong>, use <em>Register monastery</em> or <em>Register Sangha</em>. After submission, monasteries may require admin approval before login is enabled.</p>',
            ],
            'guidelines' => [
                'title' => 'Exam Guidelines',
                'sort_order' => 14,
                'content' => '<h2>Before examination day</h2><ul><li>Confirm your registration status with your monastery coordinator.</li><li>Bring a valid photo ID and admission slip (if issued).</li><li>Arrive at least <strong>30 minutes</strong> before the published start time.</li></ul><h2>During the exam</h2><p>Mobile phones and smart watches should be switched off and stored as directed. Raise your hand for invigilator assistance; do not communicate with other candidates.</p><h2>Conduct & dress</h2><p>Follow monastery dress and comportment standards. Invigilators may dismiss anyone who disrupts the hall.</p><p><em>Demo text—replace with your official regulations PDF link when available.</em></p>',
            ],
            'syllabus' => [
                'title' => 'Syllabus',
                'sort_order' => 15,
                'content' => '<h2>Structure</h2><p>Examinations are organised by <strong>level</strong> and <strong>subject group</strong>. Typical papers include Pali grammar and composition, Vinaya case studies, and Dhamma (doctrinal) essays.</p><h2>Sample weighting (demo)</h2><div class="not-prose my-4 overflow-x-auto rounded-xl border border-stone-200 dark:border-slate-600"><table class="min-w-full text-sm text-left"><thead class="bg-stone-100 dark:bg-slate-800"><tr><th class="px-4 py-2">Component</th><th class="px-4 py-2">Focus</th></tr></thead><tbody><tr class="border-t border-stone-200 dark:border-slate-600"><td class="px-4 py-2">Pali</td><td class="px-4 py-2">Translation, parsing, prescribed texts</td></tr><tr class="border-t border-stone-200 dark:border-slate-600"><td class="px-4 py-2">Vinaya</td><td class="px-4 py-2">Khandhaka summaries, offence classes</td></tr><tr class="border-t border-stone-200 dark:border-slate-600"><td class="px-4 py-2">Dhamma</td><td class="px-4 py-2">Sutta themes, Abhidhamma basics</td></tr></tbody></table></div><h2>Official syllabi</h2><p>Topic outlines are distributed per cycle by the examination board. Contact your monastery for the PDF matching your sitting.</p>',
            ],
            'past-papers' => [
                'title' => 'Past Papers',
                'sort_order' => 16,
                'content' => '<h2>Past papers</h2><p>Representative questions from previous cycles help candidates prepare. Contact your monastery coordinator for access.</p>',
            ],
            'news' => [
                'title' => 'News',
                'sort_order' => 17,
                'content' => '<h2>Latest updates</h2><article class="not-prose my-6 space-y-4"><div class="rounded-2xl border border-stone-200 dark:border-slate-600 bg-stone-50/80 dark:bg-slate-800/50 p-5"><p class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Mar 1, '.$y.'</p><h3 class="font-heading text-lg text-stone-900 dark:text-slate-100 mt-1">Spring Pali Level 1 — registration window</h3><p class="text-sm text-stone-600 dark:text-slate-400 mt-2">Monasteries may begin submitting candidate lists. Closing date will be announced on the schedule page.</p></div><div class="rounded-2xl border border-stone-200 dark:border-slate-600 bg-stone-50/80 dark:bg-slate-800/50 p-5"><p class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Feb 1, '.$y.'</p><h3 class="font-heading text-lg text-stone-900 dark:text-slate-100 mt-1">Yangon region — venue confirmations</h3><p class="text-sm text-stone-600 dark:text-slate-400 mt-2">Several halls have been confirmed; coordinators received email summaries (demo narrative).</p></div></article>',
            ],
            'events' => [
                'title' => 'Events',
                'sort_order' => 18,
                'content' => '<h2>Calendar highlights</h2><div class="not-prose grid gap-4 sm:grid-cols-2 my-6"><div class="rounded-2xl border border-stone-200 dark:border-slate-600 p-5"><p class="text-amber-700 dark:text-amber-400 text-sm font-semibold">Briefing</p><p class="font-medium text-stone-900 dark:text-slate-100 mt-1">Pre-exam orientation</p><p class="text-sm text-stone-600 dark:text-slate-400 mt-2">For registered candidates; times vary by monastery.</p></div><div class="rounded-2xl border border-stone-200 dark:border-slate-600 p-5"><p class="text-amber-700 dark:text-amber-400 text-sm font-semibold">Results</p><p class="font-medium text-stone-900 dark:text-slate-100 mt-1">Publication ceremony</p><p class="text-sm text-stone-600 dark:text-slate-400 mt-2">Public pass lists may follow on the website after moderation.</p></div></div><p>See <strong>Examination Schedule</strong> for exact dates tied to your database.</p>',
            ],
            'gallery' => [
                'title' => 'Gallery',
                'sort_order' => 19,
                'content' => '<h2>Gallery</h2><p>Photo highlights from recent ceremonies and examination days will appear here.</p>',
            ],
            'terms-of-use' => [
                'title' => 'Terms of Use',
                'sort_order' => 90,
                'content' => '<h2>Terms of use</h2><p>Use of this platform is subject to fair use and respect for monastic privacy. Administrative accounts are issued to authorized staff only.</p>',
            ],
            'accessibility' => [
                'title' => 'Accessibility',
                'sort_order' => 91,
                'content' => '<h2>Our commitment</h2><p>We strive for readable type sizes, sufficient colour contrast in <strong>light</strong> and <strong>dark</strong> themes, and keyboard-accessible navigation across public pages.</p><h2>Features</h2><ul><li>Semantic headings and landmark regions</li><li>Focus-visible styles on interactive controls</li><li>Reduced-motion friendly animations where applicable</li></ul><h2>Feedback</h2><p>If you encounter a barrier, please contact us via the <strong>Contact</strong> page with the page URL and your browser or assistive technology (demo process).</p>',
            ],
            'donate' => [
                'title' => 'Donate',
                'sort_order' => 92,
                'content' => '<h2>Support the program</h2><p>Donations help cover examination materials, hall costs, and platform maintenance. This is <strong>demo copy</strong>—replace with your official donation channels.</p><ul><li><strong>Bank transfer:</strong> Demo Bank — Account name: Sangha Exam Trust — Reference: DONATION</li><li><strong>Contact:</strong> Use the Contact page for receipts and enquiries.</li></ul>',
            ],
            'resources' => [
                'title' => 'Resources',
                'sort_order' => 93,
                'content' => '<h2>Downloads & links</h2><ul><li>Candidate handbook (PDF) — available from your monastery coordinator</li><li>Sample timetable template for hall supervisors</li><li>Regional contact list — issued per examination cycle</li></ul><p>Administrators can attach real files through custom fields or future media modules.</p>',
            ],
            'volunteer' => [
                'title' => 'Volunteer',
                'sort_order' => 94,
                'content' => '<h2>Volunteer with us</h2><p>We welcome lay supporters for registration desks, timekeeping, and hall setup. Training is provided before each examination season.</p><p><strong>To apply:</strong> email volunteer@sanghaexam.org with your city and availability (demo address).</p>',
            ],
            'partners' => [
                'title' => 'Partners',
                'sort_order' => 95,
                'content' => '<h2>Partner monasteries</h2><p>Partner institutions host sittings, nominate invigilators, and validate candidate lists. The map and directory below can be replaced with your real partner list.</p><p>Current demo data includes monasteries across Yangon, Mandalay, and Mon regions.</p>',
            ],
            'sitemap' => [
                'title' => 'Sitemap',
                'sort_order' => 96,
                'content' => '<h2>Site overview</h2><p>Use the navigation menus for the full structure. Quick entry points: <strong>Home</strong>, <strong>Examination Schedule</strong>, <strong>FAQ</strong>, <strong>Contact</strong>, and <strong>Log in</strong> for portals.</p>',
            ],
        ];

        foreach ($pages as $slug => $row) {
            Website::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $row['title'],
                    'content' => $row['content'],
                    'type' => 'page',
                    'is_published' => true,
                    'sort_order' => $row['sort_order'],
                ]
            );
        }
    }
}
