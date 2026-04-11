<?php

namespace Database\Seeders;

use App\Models\CustomField;
use Illuminate\Database\Seeder;

/**
 * Demo custom fields for monastery portal → Exam tab uploads (entity_type monastery_exam).
 * Safe to re-run (updateOrCreate). For existing DBs: php artisan db:seed --class=MonasteryExamDemoCustomFieldsSeeder
 */
class MonasteryExamDemoCustomFieldsSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            [
                'slug' => 'hall_registration_reference',
                'name' => 'Hall registration reference',
                'type' => 'text',
                'required' => false,
                'placeholder' => 'e.g. HR-2026-0001',
            ],
            [
                'slug' => 'candidate_headcount',
                'name' => 'Number of candidates',
                'type' => 'number',
                'required' => true,
                'placeholder' => 'Headcount for this programme',
            ],
            [
                'slug' => 'coordinator_phone',
                'name' => 'Coordinator phone',
                'type' => 'text',
                'required' => false,
                'placeholder' => '09xxxxxxxxx',
            ],
            [
                'slug' => 'submission_notes',
                'name' => 'Notes for secretariat',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Optional message with this upload',
            ],
            [
                'slug' => 'signed_hall_form',
                'name' => 'Signed hall form (PDF or scan)',
                'type' => 'document',
                'required' => true,
                'placeholder' => null,
            ],
        ];

        foreach ($fields as $i => $def) {
            CustomField::updateOrCreate(
                ['entity_type' => 'monastery_exam', 'slug' => $def['slug']],
                [
                    'name' => $def['name'],
                    'type' => $def['type'],
                    'required' => $def['required'],
                    'placeholder' => $def['placeholder'],
                    'sort_order' => 300 + $i,
                    'is_built_in' => false,
                ]
            );
        }
    }
}
