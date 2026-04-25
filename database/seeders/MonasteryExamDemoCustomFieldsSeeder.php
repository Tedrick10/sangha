<?php

namespace Database\Seeders;

use App\Models\CustomField;
use Illuminate\Database\Seeder;

/**
 * Demo custom fields for monastery portal → Exam tab uploads (entity_type monastery_exam).
 * Rows use is_built_in = false so admins can delete or reorder them individually.
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
                'options' => null,
            ],
            [
                'slug' => 'candidate_headcount',
                'name' => 'Number of candidates',
                'type' => 'number',
                'required' => true,
                'placeholder' => 'Headcount for this programme',
                'options' => null,
            ],
            [
                'slug' => 'coordinator_phone',
                'name' => 'Coordinator phone',
                'type' => 'text',
                'required' => false,
                'placeholder' => '09xxxxxxxxx',
                'options' => null,
            ],
            [
                'slug' => 'preferred_session_demo',
                'name' => 'Preferred session (demo)',
                'type' => 'select',
                'required' => false,
                'placeholder' => 'Select session',
                'options' => ['Morning', 'Afternoon'],
            ],
            [
                'slug' => 'submission_notes',
                'name' => 'Notes for secretariat',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Optional message with this upload',
                'options' => null,
            ],
            [
                'slug' => 'signed_hall_form',
                'name' => 'Signed hall form (PDF or scan)',
                'type' => 'document',
                'required' => true,
                'placeholder' => null,
                'options' => null,
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
                    'options' => $def['options'] ?? null,
                    'sort_order' => 300 + $i,
                    'is_built_in' => false,
                ]
            );
        }
    }
}
