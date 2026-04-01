<?php

namespace Database\Seeders;

use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Exam;
use Database\Seeders\LanguageSeeder;
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
                ['email' => 'staff' . ($i + 1) . '@sanghaexam.org'],
                ['name' => 'Staff User ' . ($i + 1), 'password' => Hash::make('password123'), 'role_id' => $roles[$i % 3]->id]
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
            $username = Str::slug($m['name']) . '_' . ($i + 10);
            $monasteries[] = Monastery::updateOrCreate(
                ['username' => $username],
                [
                'name' => $m['name'],
                'password' => Hash::make('password123'),
                'region' => $m['region'],
                'city' => $m['city'],
                'address' => 'Address ' . $m['city'],
                'phone' => '09-' . str_pad((string) (100000000 + $i), 9, '0'),
                'description' => 'Monastery in ' . $m['city'] . '.',
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
            $examTypes[] = ExamType::updateOrCreate(['name' => $name], ['description' => $name . ' description', 'is_active' => true, 'approved' => $i % 3 !== 0]);
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
            $subjects[] = Subject::updateOrCreate(['name' => $name], ['description' => $name . ' subject', 'moderation_mark' => 35 + ($i % 5), 'is_active' => true]);
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
            $examName = 'Exam ' . ($i + 7) . ' - ' . $mon->city . ' ' . now()->addMonths($i)->format('M Y');
            $exams[] = Exam::updateOrCreate(
                ['name' => $examName, 'monastery_id' => $mon->id],
                ['description' => 'Scheduled examination', 'exam_date' => now()->addMonths($i % 12)->addDays($i * 2), 'exam_type_id' => $et->id, 'location' => $mon->name . ' Hall', 'is_active' => true, 'approved' => $i % 3 !== 0]
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
            $username = 'sangha_extra_' . ($i + 10);
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
            if (!$exam) continue;
            $examSubjects = $exam->subjects;
            if ($examSubjects->isEmpty()) $examSubjects = collect($subjects)->take(2);
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
            'content' => '<h1>Welcome to Sangha Exam</h1><p>This is the home page content.</p>',
            'type' => 'page',
            'is_published' => true,
            'sort_order' => 0,
        ]);
        Website::updateOrCreate(
            ['slug' => 'about'],
            [
            'title' => 'About Us',
            'content' => '<h1>About Sangha Exam</h1><p>We organize Buddhist monastic examinations.</p>',
            'type' => 'page',
            'is_published' => true,
            'sort_order' => 1,
        ]);
        Website::updateOrCreate(
            ['slug' => 'contact'],
            [
            'title' => 'Contact',
            'content' => '<h1>Contact Us</h1><p>Email: contact@sanghaexam.org</p><p>Phone: +95 9 123 456 789</p>',
            'type' => 'page',
            'is_published' => true,
            'sort_order' => 2,
        ]);
        Website::updateOrCreate(
            ['slug' => 'exam-schedule'],
            [
            'title' => 'Examination Schedule',
            'content' => '<h1>Examination Schedule</h1><p>View upcoming examination dates and venues.</p>',
            'type' => 'page',
            'is_published' => true,
            'sort_order' => 3,
        ]);
        Website::updateOrCreate(
            ['slug' => 'privacy'],
            [
            'title' => 'Privacy Policy',
            'content' => '<h1>Privacy Policy</h1><p>We respect your privacy. This page explains how we collect, use, and protect your personal information.</p>',
            'type' => 'page',
            'is_published' => true,
            'sort_order' => 4,
        ]);
        Website::updateOrCreate(
            ['slug' => 'faq'],
            [
            'title' => 'FAQ',
            'content' => '<h1>Frequently Asked Questions</h1><p>Find answers to common questions about Sangha Exam registration, exams, and results.</p>',
            'type' => 'page',
            'is_published' => true,
            'sort_order' => 5,
        ]);
        Website::updateOrCreate(
            ['slug' => 'footer'],
            [
            'title' => 'Footer',
            'content' => '<p>© 2025 Sangha Exam. All rights reserved.</p>',
            'type' => 'section',
            'is_published' => true,
            'sort_order' => 10,
        ]);
        $morePages = ['Results', 'Registration', 'Guidelines', 'Syllabus', 'Past Papers', 'Resources', 'News', 'Events', 'Gallery', 'Donate', 'Volunteer', 'Partners', 'Terms of Use', 'Sitemap', 'Accessibility'];
        foreach ($morePages as $i => $title) {
            $slug = Str::slug($title);
            Website::updateOrCreate(
                ['slug' => $slug],
                [
                'title' => $title,
                'content' => '<h1>' . $title . '</h1><p>Content for ' . $title . ' page.</p>',
                'type' => 'page',
                'is_published' => $i % 4 !== 0,
                'sort_order' => 20 + $i,
            ]);
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
