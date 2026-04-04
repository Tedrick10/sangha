<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $content = <<<'HTML'
<h2>Summary</h2>
<p>This Privacy Policy describes how Sangha Exam (the &ldquo;platform&rdquo;) handles personal information when monasteries and Sangha members use registration, examination, and results features. It applies to the public website and the monastery and Sangha portals unless a separate notice says otherwise.</p>
<p><strong>Demo notice:</strong> Replace this entire document with counsel-approved text before relying on it in production.</p>

<h2>Who we are</h2>
<p>The data controller for the platform is the organisation operating this deployment (for example, your examination secretariat or trust). Authorised monastery staff and platform administrators may process data under instructions from that controller.</p>

<h2>Information we collect</h2>
<p>We collect only what is reasonably needed to run examinations and portals. Categories may include:</p>
<ul>
<li>Identity and contact details (such as names, email addresses, and phone numbers)</li>
<li>Monastery affiliation, registration status, and approval workflow records</li>
<li>Examination data (sittings, subjects, scores, and moderation notes)</li>
<li>Technical data (such as IP address, browser type, and session identifiers) when you use the site</li>
<li>Content of messages you send through contact or support channels</li>
</ul>

<h2>How we use your information</h2>
<p>We use personal data to register candidates and monasteries, publish schedules, administer exams, record and moderate results, operate secure logins, maintain audit trails, improve reliability and security, and respond to enquiries. We do not sell personal data. We do not use it for third-party behavioural advertising on the public site in this demo configuration.</p>

<h2>Legal bases and retention</h2>
<p>Depending on your jurisdiction, processing may rely on contract, legitimate interests, legal obligation, or consent. Retention periods should reflect regulatory or institutional requirements, dispute resolution, and operational needs; define them clearly in your final policy.</p>

<h2>Your rights</h2>
<p>Where applicable law provides them, you may have the right to:</p>
<ul>
<li>Request access to or a copy of your personal data</li>
<li>Request correction of inaccurate or incomplete information</li>
<li>Ask how your information is used and with whom it is shared</li>
<li>Withdraw consent where processing is based on consent, subject to examination rules and the legitimate needs of the programme</li>
<li>Lodge a complaint with a supervisory authority</li>
</ul>
<p>To exercise these rights, contact the secretariat using the process published on the <strong>Contact</strong> page (replace with your real workflow).</p>

<h2>Cookies and similar technologies</h2>
<p>The site uses essential cookies and session storage for authentication, language preference, and theme choice (light or dark). These are needed for the service to operate. This demo does not load third-party advertising trackers on public pages.</p>

<h2>Security</h2>
<p>We apply administrative, technical, and organisational measures suited to the sensitivity of examination and identity data. No online service is perfectly secure; use strong passwords and protect your credentials.</p>

<h2>International transfers</h2>
<p>If data is processed across borders or by providers in other countries, describe the legal mechanism (for example standard contractual clauses) in your production policy. This demo text does not specify a jurisdiction.</p>

<h2>Changes to this policy</h2>
<p>We may update this policy when features or requirements change. Your organisation should decide how to notify users of material changes (website notice, email, or both).</p>

<h2>Contact</h2>
<p>For privacy-related questions, use the channel published on the <strong>Contact</strong> page.</p>
HTML;

        DB::table('websites')->where('slug', 'privacy')->update(['content' => $content, 'updated_at' => now()]);
    }

    public function down(): void
    {
        $previous = <<<'HTML'
<h2>Summary</h2><p>We collect only the information needed to run examinations and portals (names, monastery affiliation, scores, and contact details). Data is stored securely and accessed by authorized staff.</p><h2>Your rights</h2><ul><li>Request correction of personal data</li><li>Ask how your information is used</li><li>Withdraw consent where applicable, subject to exam rules</li></ul><h2>Cookies & sessions</h2><p>The site uses essential cookies and session storage for login, language, and theme preferences. We do not use third-party advertising trackers on the public site.</p>
HTML;

        DB::table('websites')->where('slug', 'privacy')->update(['content' => $previous, 'updated_at' => now()]);
    }
};
