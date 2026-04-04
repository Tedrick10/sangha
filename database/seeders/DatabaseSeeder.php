<?php

namespace Database\Seeders;

use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Monastery;
use App\Models\Role;
use App\Models\Sangha;
use App\Models\Score;
use App\Models\Subject;
use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(LanguageSeeder::class);
        $this->call(LanguageTranslationSeeder::class);
        $this->call(LanguageLocalizationSeeder::class);

        // Roles (must exist before users due to role_id FK)
        $allPermissions = Role::allPermissionSlugs();
        $adminRole = Role::updateOrCreate(
            ['name' => 'Admin'],
            ['permissions' => $allPermissions]
        );
        $editorRole = Role::updateOrCreate(
            ['name' => 'Editor'],
            ['permissions' => array_values(array_filter($allPermissions, fn (string $p) => in_array($p, [
                'monasteries.read', 'monasteries.update', 'sanghas.read', 'sanghas.update',
                'subjects.read', 'exam_types.read', 'exams.read', 'exams.update',
                'scores.read', 'scores.update', 'websites.read', 'websites.update',
                'custom_fields.read', 'languages.read',
            ], true)))]
        );
        $viewerRole = Role::updateOrCreate(
            ['name' => 'Viewer'],
            ['permissions' => array_values(array_filter($allPermissions, fn (string $p) => str_ends_with($p, '.read')))]
        );

        // Users (admin panel users)
        User::updateOrCreate(
            ['email' => 'admin@sanghaexam.org'],
            ['name' => 'Admin User', 'password' => Hash::make('password123'), 'role_id' => $adminRole->id]
        );
        User::updateOrCreate(
            ['email' => 'editor@sanghaexam.org'],
            ['name' => 'Editor User', 'password' => Hash::make('password123'), 'role_id' => $editorRole->id]
        );
        User::updateOrCreate(
            ['email' => 'viewer@sanghaexam.org'],
            ['name' => 'Viewer User', 'password' => Hash::make('password123'), 'role_id' => $viewerRole->id]
        );
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => Hash::make('password123'), 'role_id' => $adminRole->id]
        );
        User::updateOrCreate(
            ['email' => 'john@example.com'],
            ['name' => 'John Doe', 'password' => Hash::make('password123'), 'role_id' => $editorRole->id]
        );
        User::updateOrCreate(
            ['email' => 'jane@example.com'],
            ['name' => 'Jane Smith', 'password' => Hash::make('password123'), 'role_id' => $viewerRole->id]
        );
        $roles = [$adminRole, $editorRole, $viewerRole];
        for ($i = 0; $i < 15; $i++) {
            User::updateOrCreate(
                ['email' => 'staff'.($i + 1).'@sanghaexam.org'],
                ['name' => 'Staff User '.($i + 1), 'password' => Hash::make('password123'), 'role_id' => $roles[$i % 3]->id]
            );
        }

        // Monasteries
        $monasteries = [
            Monastery::updateOrCreate(
                ['username' => 'shwegu'],
                [
                    'name' => 'Shwegu Monastery',
                    'password' => Hash::make('password123'),
                    'region' => 'Mandalay',
                    'city' => 'Mandalay',
                    'address' => '123 Pagoda Road',
                    'phone' => '09-123456789',
                    'description' => 'A leading monastery in Mandalay.',
                    'is_active' => true,
                    'approved' => true,
                ]),
            Monastery::updateOrCreate(
                ['username' => 'maha_aungmye'],
                [
                    'name' => 'Maha Aungmye Monastery',
                    'password' => Hash::make('password123'),
                    'region' => 'Yangon',
                    'city' => 'Yangon',
                    'address' => '456 Bahan Street',
                    'phone' => '09-987654321',
                    'description' => 'Historic monastery in Yangon.',
                    'is_active' => true,
                    'approved' => true,
                ]),
            Monastery::updateOrCreate(
                ['username' => 'thamanya'],
                [
                    'name' => 'Thamanya Monastery',
                    'password' => Hash::make('password123'),
                    'region' => 'Kayin',
                    'city' => 'Hpa-an',
                    'address' => 'Thamanya Hill',
                    'phone' => '09-555555555',
                    'description' => 'Peaceful hillside monastery.',
                    'is_active' => true,
                    'approved' => false,
                ]),
            Monastery::updateOrCreate(
                ['username' => 'saddhamma'],
                [
                    'name' => 'Saddhamma Monastery',
                    'password' => Hash::make('password123'),
                    'region' => 'Sagaing',
                    'city' => 'Sagaing',
                    'address' => 'Sagaing Hill',
                    'phone' => '09-111222333',
                    'description' => 'Renowned for Pali studies.',
                    'is_active' => true,
                    'approved' => true,
                ]),
            Monastery::updateOrCreate(
                ['username' => 'chanthagon'],
                [
                    'name' => 'Chanthagon Monastery',
                    'password' => Hash::make('password123'),
                    'region' => 'Bago',
                    'city' => 'Bago',
                    'address' => '78 Shwemawdaw Road',
                    'phone' => '09-444666888',
                    'description' => 'Traditional teaching monastery.',
                    'is_active' => true,
                    'approved' => true,
                ]),
        ];

        // More monasteries (so table has multiple pages)
        $moreMonasteryNames = [
            ['name' => 'Dhamma Joti Monastery', 'region' => 'Mandalay', 'city' => 'Amarapura'],
            ['name' => 'Mahasi Monastery', 'region' => 'Yangon', 'city' => 'Yangon'],
            ['name' => 'Panditarama Monastery', 'region' => 'Yangon', 'city' => 'Yangon'],
            ['name' => 'Chanmyay Yeiktha', 'region' => 'Yangon', 'city' => 'Yangon'],
            ['name' => 'Pa-Auk Forest Monastery', 'region' => 'Mon', 'city' => 'Mawlamyine'],
            ['name' => 'Shwe Oo Min Monastery', 'region' => 'Yangon', 'city' => 'Yangon'],
            ['name' => 'Sitagu International Academy', 'region' => 'Sagaing', 'city' => 'Sagaing'],
            ['name' => 'Maha Bodhi Monastery', 'region' => 'Mandalay', 'city' => 'Mandalay'],
            ['name' => 'Mingun Monastery', 'region' => 'Sagaing', 'city' => 'Mingun'],
            ['name' => 'Insein Monastery', 'region' => 'Yangon', 'city' => 'Insein'],
            ['name' => 'Bahan Monastery', 'region' => 'Yangon', 'city' => 'Bahan'],
            ['name' => 'Ledi Monastery', 'region' => 'Mandalay', 'city' => 'Mandalay'],
            ['name' => 'Monywa Monastery', 'region' => 'Sagaing', 'city' => 'Monywa'],
            ['name' => 'Pakokku Monastery', 'region' => 'Magway', 'city' => 'Pakokku'],
            ['name' => 'Taunggyi Monastery', 'region' => 'Shan', 'city' => 'Taunggyi'],
            ['name' => 'Mawlamyine Monastery', 'region' => 'Mon', 'city' => 'Mawlamyine'],
            ['name' => 'Pathein Monastery', 'region' => 'Ayeyarwady', 'city' => 'Pathein'],
            ['name' => 'Pyay Monastery', 'region' => 'Bago', 'city' => 'Pyay'],
            ['name' => 'Meiktila Monastery', 'region' => 'Mandalay', 'city' => 'Meiktila'],
            ['name' => 'Magway Monastery', 'region' => 'Magway', 'city' => 'Magway'],
        ];
        foreach ($moreMonasteryNames as $i => $m) {
            $username = Str::slug($m['name']).'_'.($i + 10);
            $monasteries[] = Monastery::updateOrCreate(
                ['username' => $username],
                [
                    'name' => $m['name'],
                    'password' => Hash::make('password123'),
                    'region' => $m['region'],
                    'city' => $m['city'],
                    'address' => 'Address '.$m['city'],
                    'phone' => '09-'.str_pad((string) (100000000 + $i), 9, '0'),
                    'description' => 'Monastery in '.$m['city'].'.',
                    'is_active' => $i % 5 !== 0,
                    'approved' => $i % 4 !== 0,
                ]);
        }

        // Exam Types
        $examTypes = [
            ExamType::updateOrCreate(['name' => 'Pali Level 1'], ['description' => 'Introductory Pali examination', 'is_active' => true, 'approved' => true]),
            ExamType::updateOrCreate(['name' => 'Pali Level 2'], ['description' => 'Intermediate Pali examination', 'is_active' => true, 'approved' => true]),
            ExamType::updateOrCreate(['name' => 'Pali Level 3'], ['description' => 'Advanced Pali examination', 'is_active' => true, 'approved' => true]),
            ExamType::updateOrCreate(['name' => 'Dhamma Exam'], ['description' => 'Dhamma theory examination', 'is_active' => true, 'approved' => true]),
            ExamType::updateOrCreate(['name' => 'Vinaya Exam'], ['description' => 'Monastic discipline examination', 'is_active' => true, 'approved' => true]),
        ];
        $moreExamTypeNames = ['Pali Level 4', 'Pali Level 5', 'Abhidhamma Exam', 'Suttanta Exam', 'Tipitaka Exam', 'Memorization Exam', 'Writing Exam', 'Oral Exam', 'Preliminary Exam', 'Final Exam'];
        foreach ($moreExamTypeNames as $i => $name) {
            $examTypes[] = ExamType::updateOrCreate(['name' => $name], ['description' => $name.' description', 'is_active' => true, 'approved' => $i % 3 !== 0]);
        }

        // Subjects
        $subjects = [
            Subject::updateOrCreate(['name' => 'Pali Grammar'], ['description' => 'Basic Pali grammar and syntax', 'moderation_mark' => 40, 'is_active' => true]),
            Subject::updateOrCreate(['name' => 'Pali Literature'], ['description' => 'Pali texts and literature', 'moderation_mark' => 40, 'is_active' => true]),
            Subject::updateOrCreate(['name' => 'Abhidhamma'], ['description' => 'Buddhist philosophy and psychology', 'moderation_mark' => 35, 'is_active' => true]),
            Subject::updateOrCreate(['name' => 'Suttanta'], ['description' => 'Discourse studies', 'moderation_mark' => 40, 'is_active' => true]),
            Subject::updateOrCreate(['name' => 'Vinaya'], ['description' => 'Monastic discipline and rules', 'moderation_mark' => 40, 'is_active' => true]),
            Subject::updateOrCreate(['name' => 'Pali Composition'], ['description' => 'Pali composition and writing', 'moderation_mark' => 35, 'is_active' => true]),
        ];
        $moreSubjectNames = ['Pali Prosody', 'Pali Phonetics', 'Buddhist History', 'Meditation Theory', 'Ethics', 'Logic', 'Pali Translation', 'Commentary Studies', 'Pali Poetry', 'Pali Prose'];
        foreach ($moreSubjectNames as $i => $name) {
            $subjects[] = Subject::updateOrCreate(['name' => $name], ['description' => $name.' subject', 'moderation_mark' => 35 + ($i % 5), 'is_active' => true]);
        }

        // Exams
        $exams = [
            Exam::updateOrCreate(
                ['name' => 'Pali Level 1 - March 2025', 'monastery_id' => $monasteries[0]->id],
                ['description' => 'Annual Pali Level 1 examination', 'exam_date' => now()->addMonths(1), 'exam_type_id' => $examTypes[0]->id, 'location' => 'Shwegu Monastery Hall', 'is_active' => true, 'approved' => true]
            ),
            Exam::updateOrCreate(
                ['name' => 'Pali Level 2 - April 2025', 'monastery_id' => $monasteries[0]->id],
                ['description' => 'Annual Pali Level 2 examination', 'exam_date' => now()->addMonths(2), 'exam_type_id' => $examTypes[1]->id, 'location' => 'Shwegu Monastery Hall', 'is_active' => true, 'approved' => true]
            ),
            Exam::updateOrCreate(
                ['name' => 'Dhamma Exam - January 2025', 'monastery_id' => $monasteries[1]->id],
                ['description' => 'Dhamma theory exam (past)', 'exam_date' => now()->subMonths(2), 'exam_type_id' => $examTypes[3]->id, 'location' => 'Maha Aungmye Hall', 'is_active' => true, 'approved' => true]
            ),
            Exam::updateOrCreate(
                ['name' => 'Pali Level 3 - May 2025', 'monastery_id' => $monasteries[3]->id],
                ['description' => 'Advanced Pali examination', 'exam_date' => now()->addMonths(3), 'exam_type_id' => $examTypes[2]->id, 'location' => 'Saddhamma Monastery', 'is_active' => true, 'approved' => true]
            ),
            Exam::updateOrCreate(
                ['name' => 'Vinaya Exam - February 2025', 'monastery_id' => $monasteries[4]->id],
                ['description' => 'Monastic discipline exam', 'exam_date' => now()->subMonths(1), 'exam_type_id' => $examTypes[4]->id, 'location' => 'Chanthagon Hall', 'is_active' => true, 'approved' => true]
            ),
            Exam::updateOrCreate(
                ['name' => 'Pali Level 1 - Sagaing 2025', 'monastery_id' => $monasteries[3]->id],
                ['description' => 'Sagaing region Pali Level 1', 'exam_date' => now()->addMonths(2), 'exam_type_id' => $examTypes[0]->id, 'location' => 'Saddhamma Monastery', 'is_active' => true, 'approved' => true]
            ),
        ];
        for ($i = 0; $i < 18; $i++) {
            $mon = $monasteries[array_rand($monasteries)];
            $et = $examTypes[array_rand($examTypes)];
            $examName = 'Exam '.($i + 7).' - '.$mon->city.' '.now()->addMonths($i)->format('M Y');
            $exams[] = Exam::updateOrCreate(
                ['name' => $examName, 'monastery_id' => $mon->id],
                ['description' => 'Scheduled examination', 'exam_date' => now()->addMonths($i % 12)->addDays($i * 2), 'exam_type_id' => $et->id, 'location' => $mon->name.' Hall', 'is_active' => true, 'approved' => $i % 3 !== 0]
            );
        }

        // Link subjects to exams
        $exams[0]->subjects()->sync([$subjects[0]->id, $subjects[1]->id]);
        $exams[1]->subjects()->sync([$subjects[0]->id, $subjects[1]->id, $subjects[2]->id]);
        $exams[2]->subjects()->sync([$subjects[2]->id, $subjects[3]->id]);
        $exams[3]->subjects()->sync([$subjects[0]->id, $subjects[1]->id, $subjects[5]->id]);
        $exams[4]->subjects()->sync([$subjects[4]->id, $subjects[2]->id]);
        $exams[5]->subjects()->sync([$subjects[0]->id, $subjects[1]->id]);
        for ($i = 6; $i < count($exams); $i++) {
            $numSubjects = 2 + ($i % 3);
            $subjectIds = array_slice(array_map(fn ($s) => $s->id, $subjects), 0, $numSubjects);
            $exams[$i]->subjects()->sync($subjectIds);
        }

        // Sanghas
        $sanghas = [
            Sangha::updateOrCreate(
                ['username' => 'u_agga'],
                [
                    'monastery_id' => $monasteries[0]->id,
                    'exam_id' => $exams[0]->id,
                    'name' => 'U Agga',
                    'password' => Hash::make('password123'),
                    'description' => 'Senior student',
                    'is_active' => true,
                    'approved' => true,
                ]),
            Sangha::updateOrCreate(
                ['username' => 'u_bodhi'],
                [
                    'monastery_id' => $monasteries[0]->id,
                    'exam_id' => $exams[0]->id,
                    'name' => 'U Bodhi',
                    'password' => Hash::make('password123'),
                    'description' => 'Intermediate student',
                    'is_active' => true,
                    'approved' => true,
                ]),
            Sangha::updateOrCreate(
                ['username' => 'u_metta'],
                [
                    'monastery_id' => $monasteries[0]->id,
                    'exam_id' => $exams[0]->id,
                    'name' => 'U Metta',
                    'password' => Hash::make('password123'),
                    'description' => 'Level 1 candidate',
                    'is_active' => true,
                    'approved' => true,
                ]),
            Sangha::updateOrCreate(
                ['username' => 'u_citta'],
                [
                    'monastery_id' => $monasteries[0]->id,
                    'exam_id' => $exams[1]->id,
                    'name' => 'U Citta',
                    'password' => Hash::make('password123'),
                    'description' => 'Level 2 candidate',
                    'is_active' => true,
                    'approved' => true,
                ]),
            Sangha::updateOrCreate(
                ['username' => 'u_sila'],
                [
                    'monastery_id' => $monasteries[0]->id,
                    'exam_id' => $exams[1]->id,
                    'name' => 'U Sila',
                    'password' => Hash::make('password123'),
                    'description' => 'Level 2 candidate',
                    'is_active' => true,
                    'approved' => true,
                ]),
            Sangha::updateOrCreate(
                ['username' => 'u_dhamma'],
                [
                    'monastery_id' => $monasteries[1]->id,
                    'exam_id' => $exams[2]->id,
                    'name' => 'U Dhamma',
                    'password' => Hash::make('password123'),
                    'description' => 'Dhamma student',
                    'is_active' => true,
                    'approved' => true,
                ]),
            Sangha::updateOrCreate(
                ['username' => 'u_pannya'],
                [
                    'monastery_id' => $monasteries[1]->id,
                    'exam_id' => $exams[2]->id,
                    'name' => 'U Pannya',
                    'password' => Hash::make('password123'),
                    'description' => 'Dhamma student',
                    'is_active' => true,
                    'approved' => true,
                ]),
            Sangha::updateOrCreate(
                ['username' => 'u_saddha'],
                [
                    'monastery_id' => $monasteries[3]->id,
                    'exam_id' => $exams[3]->id,
                    'name' => 'U Saddha',
                    'password' => Hash::make('password123'),
                    'description' => 'Advanced Pali student',
                    'is_active' => true,
                    'approved' => true,
                ]),
            Sangha::updateOrCreate(
                ['username' => 'u_viriya'],
                [
                    'monastery_id' => $monasteries[3]->id,
                    'exam_id' => $exams[5]->id,
                    'name' => 'U Viriya',
                    'password' => Hash::make('password123'),
                    'description' => 'Level 1 candidate',
                    'is_active' => true,
                    'approved' => true,
                ]),
            Sangha::updateOrCreate(
                ['username' => 'u_samadhi'],
                [
                    'monastery_id' => $monasteries[4]->id,
                    'exam_id' => $exams[4]->id,
                    'name' => 'U Samadhi',
                    'password' => Hash::make('password123'),
                    'description' => 'Vinaya student',
                    'is_active' => true,
                    'approved' => true,
                ]),
        ];
        $sanghaNames = ['U Khemaka', 'U Rahula', 'U Ananda', 'U Kassapa', 'U Moggallana', 'U Sariputta', 'U Upali', 'U Mahakassapa', 'U Punna', 'U Revata', 'U Sobhita', 'U Kondanna', 'U Assaji', 'U Bhaddiya', 'U Vappa', 'U Kimila', 'U Yasa', 'U Nanda', 'U Kimbila', 'U Devadatta', 'U Kokalika', 'U Katyayana', 'U Subhuti', 'U Purna', 'U Gavampati', 'U Bakula', 'U Sivali', 'U Angulimala', 'U Pilindavaccha', 'U Mahakaccana'];
        foreach ($sanghaNames as $i => $name) {
            $mon = $monasteries[$i % count($monasteries)];
            $exam = $exams[$i % count($exams)];
            $username = 'sangha_extra_'.($i + 10);
            $sanghas[] = Sangha::updateOrCreate(
                ['username' => $username],
                [
                    'monastery_id' => $mon->id,
                    'exam_id' => $exam->id,
                    'name' => $name,
                    'password' => Hash::make('password123'),
                    'description' => 'Student',
                    'is_active' => $i % 5 !== 0,
                    'approved' => true,
                ]);
        }

        // Scores
        Score::updateOrCreate(
            ['sangha_id' => $sanghas[0]->id, 'subject_id' => $subjects[0]->id, 'exam_id' => $exams[0]->id],
            ['value' => 85]
        );
        Score::updateOrCreate(
            ['sangha_id' => $sanghas[0]->id, 'subject_id' => $subjects[1]->id, 'exam_id' => $exams[0]->id],
            ['value' => 72]
        );
        foreach ([
            [$sanghas[1], $subjects[0], $exams[0], 65], [$sanghas[1], $subjects[1], $exams[0], 58],
            [$sanghas[2], $subjects[0], $exams[0], 70], [$sanghas[2], $subjects[1], $exams[0], 62],
            [$sanghas[3], $subjects[0], $exams[1], 90], [$sanghas[3], $subjects[1], $exams[1], 78], [$sanghas[3], $subjects[2], $exams[1], 82],
            [$sanghas[4], $subjects[0], $exams[1], 88], [$sanghas[4], $subjects[1], $exams[1], 75], [$sanghas[4], $subjects[2], $exams[1], 80],
            [$sanghas[5], $subjects[2], $exams[2], 75], [$sanghas[5], $subjects[3], $exams[2], 68],
            [$sanghas[6], $subjects[2], $exams[2], 82], [$sanghas[6], $subjects[3], $exams[2], 71],
            [$sanghas[7], $subjects[0], $exams[3], 92], [$sanghas[7], $subjects[1], $exams[3], 88], [$sanghas[7], $subjects[5], $exams[3], 85],
            [$sanghas[8], $subjects[0], $exams[5], 68], [$sanghas[8], $subjects[1], $exams[5], 55],
            [$sanghas[9], $subjects[4], $exams[4], 78], [$sanghas[9], $subjects[2], $exams[4], 72],
        ] as $row) {
            Score::updateOrCreate(
                ['sangha_id' => $row[0]->id, 'subject_id' => $row[1]->id, 'exam_id' => $row[2]->id],
                ['value' => $row[3]]
            );
        }
        foreach (array_slice($sanghas, 10) as $sangha) {
            $exam = $sangha->exam;
            if (! $exam) {
                continue;
            }
            $examSubjects = $exam->subjects;
            if ($examSubjects->isEmpty()) {
                $examSubjects = collect($subjects)->take(2);
            }
            foreach ($examSubjects as $subj) {
                Score::updateOrCreate(
                    ['sangha_id' => $sangha->id, 'subject_id' => $subj->id, 'exam_id' => $exam->id],
                    ['value' => rand(50, 95)]
                );
            }
        }

        // Websites
        Website::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'content' => '',
                'type' => 'page',
                'is_published' => true,
                'sort_order' => 0,
            ]);
        Website::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'content' => '<h2>Our mission</h2><p>Sangha Exam is a dedicated platform for organizing <strong>Pali</strong>, <strong>Vinaya</strong>, and <strong>Dhamma</strong> examinations for monastic communities. We help monasteries register candidates, publish schedules, record scores, and share results with clarity and respect.</p><h2>What we offer</h2><ul><li>Secure portals for monasteries and Sangha members</li><li>Centralized exam types, subjects, and score management</li><li>Public pages for schedules, policies, and pass lists</li><li>Multilingual interface options for regional use</li></ul><h2>Who we serve</h2><p>Partner monasteries, examination boards, and candidates use the system for end-to-end exam cycles—from registration to publication of outcomes.</p>',
                'type' => 'page',
                'is_published' => true,
                'sort_order' => 1,
            ]);
        Website::updateOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact',
                'content' => '<h2>Contact the secretariat</h2><p>For registration support, technical issues, or examination enquiries, reach us through the channels below. Typical response time is <strong>1–2 business days</strong>.</p><div class="not-prose my-6 grid gap-4 sm:grid-cols-2"><div class="rounded-2xl border border-stone-200 dark:border-slate-600 bg-stone-50 dark:bg-slate-800/60 p-5"><p class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-slate-400 mb-1">Email</p><a href="mailto:contact@sanghaexam.org" class="text-amber-700 dark:text-amber-400 font-medium">contact@sanghaexam.org</a></div><div class="rounded-2xl border border-stone-200 dark:border-slate-600 bg-stone-50 dark:bg-slate-800/60 p-5"><p class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-slate-400 mb-1">Phone</p><p class="font-medium text-stone-900 dark:text-slate-100">+95 9 123 456 789</p></div></div><h2>Office hours</h2><p>Monday–Friday, 09:00–17:00 (local time). During examination periods, extended support may be announced on the home page.</p>',
                'type' => 'page',
                'is_published' => true,
                'sort_order' => 2,
            ]);
        Website::updateOrCreate(
            ['slug' => 'exam-schedule'],
            [
                'title' => 'Examination Schedule',
                'content' => '<p>The <strong>table below</strong> is generated automatically from approved, active examinations in the database. Use it in demos to show real venues, monasteries, and dates. Static notes can still be edited here in the admin panel.</p><p>Monastery coordinators should confirm final hall assignments before the exam date.</p>',
                'type' => 'page',
                'is_published' => true,
                'sort_order' => 3,
            ]);
        Website::updateOrCreate(
            ['slug' => 'privacy'],
            [
                'title' => 'Privacy Policy',
                'content' => <<<'HTML'
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
HTML,
                'type' => 'page',
                'is_published' => true,
                'sort_order' => 4,
            ]);
        Website::updateOrCreate(
            ['slug' => 'faq'],
            [
                'title' => 'FAQ',
                'content' => '<h2>Registration</h2><p><strong>How do monasteries register?</strong> Use the <em>Register monastery</em> option on the home page, then sign in after approval.</p><p><strong>How do Sangha candidates register?</strong> Choose <em>Register Sangha</em>, select your monastery, and complete the form.</p><h2>Examinations</h2><p><strong>Where is the schedule?</strong> Open <strong>Examination Schedule</strong> under Examinations—the list is pulled from the database.</p><p><strong>When are results published?</strong> After moderation in the admin panel; candidates see marks in the Sangha portal.</p><h2>Technical</h2><p><strong>Light or dark theme not updating?</strong> The header theme control updates instantly without a full page reload; your choice is saved for the next visit—ensure cookies are enabled for this domain.</p>',
                'type' => 'page',
                'is_published' => true,
                'sort_order' => 5,
            ]);
        Website::updateOrCreate(
            ['slug' => 'footer'],
            [
                'title' => 'Footer',
                'content' => '<p>© '.date('Y').' Sangha Exam. Examination services for monastic communities.</p><p class="text-sm opacity-90">Demo deployment — content and figures illustrate typical usage.</p>',
                'type' => 'section',
                'is_published' => true,
                'sort_order' => 10,
            ]);
        $demoExtraPages = [
            ['slug' => 'registration', 'title' => 'Registration', 'sort' => 13, 'body' => '<h2>Who can register</h2><p><strong>Monasteries</strong> create an account to manage candidates and exams. <strong>Sangha members</strong> register under their monastery to sit examinations and view results.</p><h2>How to start</h2><p>From the <strong>home page</strong>, use <em>Register monastery</em> or <em>Register Sangha</em>. After submission, monasteries may require admin approval before login is enabled.</p>'],
            ['slug' => 'guidelines', 'title' => 'Exam Guidelines', 'sort' => 14, 'body' => '<h2>Before examination day</h2><ul><li>Confirm your registration status with your monastery coordinator.</li><li>Bring a valid photo ID and admission slip (if issued).</li><li>Arrive at least <strong>30 minutes</strong> before the published start time.</li></ul><h2>During the exam</h2><p>Mobile phones and smart watches should be switched off and stored as directed. Raise your hand for invigilator assistance; do not communicate with other candidates.</p><h2>Conduct & dress</h2><p>Follow monastery dress and comportment standards. Invigilators may dismiss anyone who disrupts the hall.</p><p><em>Demo text—replace with your official regulations PDF link when available.</em></p>'],
            ['slug' => 'syllabus', 'title' => 'Syllabus', 'sort' => 15, 'body' => '<h2>Structure</h2><p>Examinations are organised by <strong>level</strong> and <strong>subject group</strong>. Typical papers include Pali grammar and composition, Vinaya case studies, and Dhamma (doctrinal) essays.</p><h2>Sample weighting (demo)</h2><div class="not-prose my-4 overflow-x-auto rounded-xl border border-stone-200 dark:border-slate-600"><table class="min-w-full text-sm text-left"><thead class="bg-stone-100 dark:bg-slate-800"><tr><th class="px-4 py-2">Component</th><th class="px-4 py-2">Focus</th></tr></thead><tbody><tr class="border-t border-stone-200 dark:border-slate-600"><td class="px-4 py-2">Pali</td><td class="px-4 py-2">Translation, parsing, prescribed texts</td></tr><tr class="border-t border-stone-200 dark:border-slate-600"><td class="px-4 py-2">Vinaya</td><td class="px-4 py-2">Khandhaka summaries, offence classes</td></tr><tr class="border-t border-stone-200 dark:border-slate-600"><td class="px-4 py-2">Dhamma</td><td class="px-4 py-2">Sutta themes, Abhidhamma basics</td></tr></tbody></table></div><h2>Official syllabi</h2><p>Topic outlines are distributed per cycle by the examination board. Contact your monastery for the PDF matching your sitting.</p>'],
            ['slug' => 'past-papers', 'title' => 'Past Papers', 'sort' => 16, 'body' => '<h2>Past papers</h2><p>Representative questions from previous cycles help candidates prepare. Contact your monastery coordinator for access.</p>'],
            ['slug' => 'news', 'title' => 'News', 'sort' => 17, 'body' => '<h2>Latest updates</h2><article class="not-prose my-6 space-y-4"><div class="rounded-2xl border border-stone-200 dark:border-slate-600 bg-stone-50/80 dark:bg-slate-800/50 p-5"><p class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wider">'.date('M j, Y', strtotime('first day of -1 month')).'</p><h3 class="font-heading text-lg text-stone-900 dark:text-slate-100 mt-1">Spring Pali Level 1 — registration window</h3><p class="text-sm text-stone-600 dark:text-slate-400 mt-2">Monasteries may begin submitting candidate lists. Closing date will be announced on the schedule page.</p></div><div class="rounded-2xl border border-stone-200 dark:border-slate-600 bg-stone-50/80 dark:bg-slate-800/50 p-5"><p class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wider">'.date('M j, Y', strtotime('first day of -2 month')).'</p><h3 class="font-heading text-lg text-stone-900 dark:text-slate-100 mt-1">Yangon region — venue confirmations</h3><p class="text-sm text-stone-600 dark:text-slate-400 mt-2">Several halls have been confirmed; coordinators received email summaries (demo narrative).</p></div></article>'],
            ['slug' => 'events', 'title' => 'Events', 'sort' => 18, 'body' => '<h2>Calendar highlights</h2><div class="not-prose grid gap-4 sm:grid-cols-2 my-6"><div class="rounded-2xl border border-stone-200 dark:border-slate-600 p-5"><p class="text-amber-700 dark:text-amber-400 text-sm font-semibold">Briefing</p><p class="font-medium text-stone-900 dark:text-slate-100 mt-1">Pre-exam orientation</p><p class="text-sm text-stone-600 dark:text-slate-400 mt-2">For registered candidates; times vary by monastery.</p></div><div class="rounded-2xl border border-stone-200 dark:border-slate-600 p-5"><p class="text-amber-700 dark:text-amber-400 text-sm font-semibold">Results</p><p class="font-medium text-stone-900 dark:text-slate-100 mt-1">Publication ceremony</p><p class="text-sm text-stone-600 dark:text-slate-400 mt-2">Public pass lists may follow on the website after moderation.</p></div></div><p>See <strong>Examination Schedule</strong> for exact dates tied to your database.</p>'],
            ['slug' => 'gallery', 'title' => 'Gallery', 'sort' => 19, 'body' => '<h2>Gallery</h2><p>Photo highlights from recent ceremonies and examination days will appear here.</p>'],
            ['slug' => 'terms-of-use', 'title' => 'Terms of Use', 'sort' => 90, 'body' => '<h2>Terms of use</h2><p>Use of this platform is subject to fair use and respect for monastic privacy. Administrative accounts are issued to authorized staff only.</p>'],
            ['slug' => 'accessibility', 'title' => 'Accessibility', 'sort' => 91, 'body' => '<h2>Our commitment</h2><p>We strive for readable type sizes, sufficient colour contrast in <strong>light</strong> and <strong>dark</strong> themes, and keyboard-accessible navigation across public pages.</p><h2>Features</h2><ul><li>Semantic headings and landmark regions</li><li>Focus-visible styles on interactive controls</li><li>Reduced-motion friendly animations where applicable</li></ul><h2>Feedback</h2><p>If you encounter a barrier, please contact us via the <strong>Contact</strong> page with the page URL and your browser or assistive technology (demo process).</p>'],
            ['slug' => 'donate', 'title' => 'Donate', 'sort' => 92, 'body' => '<h2>Support the program</h2><p>Donations help cover examination materials, hall costs, and platform maintenance. This is <strong>demo copy</strong>—replace with your official donation channels.</p><ul><li><strong>Bank transfer:</strong> Demo Bank — Account name: Sangha Exam Trust — Reference: DONATION</li><li><strong>Contact:</strong> Use the Contact page for receipts and enquiries.</li></ul>'],
            ['slug' => 'resources', 'title' => 'Resources', 'sort' => 93, 'body' => '<h2>Downloads & links</h2><ul><li>Candidate handbook (PDF) — available from your monastery coordinator</li><li>Sample timetable template for hall supervisors</li><li>Regional contact list — issued per examination cycle</li></ul><p>Administrators can attach real files through custom fields or future media modules.</p>'],
            ['slug' => 'volunteer', 'title' => 'Volunteer', 'sort' => 94, 'body' => '<h2>Volunteer with us</h2><p>We welcome lay supporters for registration desks, timekeeping, and hall setup. Training is provided before each examination season.</p><p><strong>To apply:</strong> email volunteer@sanghaexam.org with your city and availability (demo address).</p>'],
            ['slug' => 'partners', 'title' => 'Partners', 'sort' => 95, 'body' => '<h2>Partner monasteries</h2><p>Partner institutions host sittings, nominate invigilators, and validate candidate lists. The map and directory below can be replaced with your real partner list.</p><p>Current demo data includes monasteries across Yangon, Mandalay, and Mon regions.</p>'],
            ['slug' => 'sitemap', 'title' => 'Sitemap', 'sort' => 96, 'body' => '<h2>Site overview</h2><p>Use the navigation menus for the full structure. Quick entry points: <strong>Home</strong>, <strong>Examination Schedule</strong>, <strong>FAQ</strong>, <strong>Contact</strong>, and <strong>Log in</strong> for portals.</p>'],
        ];
        foreach ($demoExtraPages as $row) {
            Website::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'title' => $row['title'],
                    'content' => $row['body'],
                    'type' => 'page',
                    'is_published' => true,
                    'sort_order' => $row['sort'],
                ]
            );
        }

        // Custom fields (non-built-in) - sync will add built-ins on first visit
        CustomField::updateOrCreate(
            ['entity_type' => 'monastery', 'slug' => 'founded_year'],
            [
                'name' => 'Founded Year',
                'type' => 'number',
                'required' => false,
                'sort_order' => 100,
                'is_built_in' => false,
            ]);
        CustomField::updateOrCreate(
            ['entity_type' => 'monastery', 'slug' => 'student_capacity'],
            [
                'name' => 'Student Capacity',
                'type' => 'number',
                'required' => false,
                'sort_order' => 101,
                'is_built_in' => false,
            ]);
        CustomField::updateOrCreate(
            ['entity_type' => 'sangha', 'slug' => 'ordination_date'],
            [
                'name' => 'Ordination Date',
                'type' => 'date',
                'required' => false,
                'sort_order' => 100,
                'is_built_in' => false,
            ]);
        CustomField::updateOrCreate(
            ['entity_type' => 'sangha', 'slug' => 'teacher_name'],
            [
                'name' => 'Teacher Name',
                'type' => 'text',
                'required' => false,
                'sort_order' => 101,
                'is_built_in' => false,
            ]);
        CustomField::updateOrCreate(
            ['entity_type' => 'exam', 'slug' => 'max_participants'],
            [
                'name' => 'Max Participants',
                'type' => 'number',
                'required' => false,
                'sort_order' => 100,
                'is_built_in' => false,
            ]);
        $moreCustomFields = [
            ['entity_type' => 'monastery', 'name' => 'Contact Person', 'slug' => 'contact_person', 'type' => 'text'],
            ['entity_type' => 'monastery', 'name' => 'Website', 'slug' => 'website_url', 'type' => 'text'],
            ['entity_type' => 'sangha', 'name' => 'Grade', 'slug' => 'grade', 'type' => 'text'],
            ['entity_type' => 'sangha', 'name' => 'Notes', 'slug' => 'notes', 'type' => 'textarea'],
            ['entity_type' => 'exam', 'name' => 'Room', 'slug' => 'room', 'type' => 'text'],
            ['entity_type' => 'exam', 'name' => 'Duration (minutes)', 'slug' => 'duration_min', 'type' => 'number'],
        ];
        foreach ($moreCustomFields as $i => $f) {
            CustomField::updateOrCreate(
                ['entity_type' => $f['entity_type'], 'slug' => $f['slug']],
                ['name' => $f['name'], 'type' => $f['type'], 'required' => false, 'sort_order' => 200 + $i, 'is_built_in' => false]
            );
        }

        // Custom field values
        $foundedField = CustomField::where('slug', 'founded_year')->first();
        if ($foundedField) {
            CustomFieldValue::updateOrCreate(['custom_field_id' => $foundedField->id, 'entity_type' => 'monastery', 'entity_id' => $monasteries[0]->id], ['value' => '1950']);
            CustomFieldValue::updateOrCreate(['custom_field_id' => $foundedField->id, 'entity_type' => 'monastery', 'entity_id' => $monasteries[1]->id], ['value' => '1890']);
            CustomFieldValue::updateOrCreate(['custom_field_id' => $foundedField->id, 'entity_type' => 'monastery', 'entity_id' => $monasteries[3]->id], ['value' => '1925']);
        }
        $capacityField = CustomField::where('slug', 'student_capacity')->first();
        if ($capacityField) {
            CustomFieldValue::updateOrCreate(['custom_field_id' => $capacityField->id, 'entity_type' => 'monastery', 'entity_id' => $monasteries[0]->id], ['value' => '200']);
            CustomFieldValue::updateOrCreate(['custom_field_id' => $capacityField->id, 'entity_type' => 'monastery', 'entity_id' => $monasteries[3]->id], ['value' => '150']);
        }
        $ordinationField = CustomField::where('slug', 'ordination_date')->first();
        if ($ordinationField) {
            CustomFieldValue::updateOrCreate(['custom_field_id' => $ordinationField->id, 'entity_type' => 'sangha', 'entity_id' => $sanghas[0]->id], ['value' => '2020-05-15']);
            CustomFieldValue::updateOrCreate(['custom_field_id' => $ordinationField->id, 'entity_type' => 'sangha', 'entity_id' => $sanghas[1]->id], ['value' => '2021-03-20']);
            CustomFieldValue::updateOrCreate(['custom_field_id' => $ordinationField->id, 'entity_type' => 'sangha', 'entity_id' => $sanghas[7]->id], ['value' => '2018-11-10']);
        }
        $teacherField = CustomField::where('slug', 'teacher_name')->first();
        if ($teacherField) {
            CustomFieldValue::updateOrCreate(['custom_field_id' => $teacherField->id, 'entity_type' => 'sangha', 'entity_id' => $sanghas[0]->id], ['value' => 'Sayadaw U Paṇḍita']);
            CustomFieldValue::updateOrCreate(['custom_field_id' => $teacherField->id, 'entity_type' => 'sangha', 'entity_id' => $sanghas[7]->id], ['value' => 'Sayadaw U Silānanda']);
        }
    }
}
