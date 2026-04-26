<?php

use App\Models\CustomFieldValue;
use App\Models\Sangha;
use App\Models\SiteSetting;
use App\Support\MonasteryPortalResultsSnapshot;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:seed-demo-dataset', function () {
    $this->warn('Seeding full demo data (users, monasteries, exams, websites) — same as local `php artisan db:seed`.');

    $this->call('db:seed', [
        '--class' => DatabaseSeeder::class,
        '--force' => true,
    ]);

    $this->info('Done. Use admin@sanghaexam.org (demo password on login page); change passwords after verifying production.');

    return self::SUCCESS;
})->purpose('Seed the same demo data as local `php artisan db:seed` (cPanel / SSH after migrate)');

Artisan::command('sangha:purge-all {--force : Run without confirmation}', function () {
    if (! $this->option('force')) {
        if (! $this->confirm('Permanently delete ALL sanghas, their scores, related custom field values, and published pass / monastery results snapshots?')) {
            $this->info('Aborted.');

            return self::SUCCESS;
        }
    }

    /** @var list<string> */
    $entityTypes = [
        'sangha',
        'programme_primary',
        'programme_intermediate',
        'programme_level_1',
        'programme_level_2',
        'programme_level_3',
    ];

    $deletedSanghas = 0;
    $deletedCfv = 0;

    DB::transaction(function () use ($entityTypes, &$deletedSanghas, &$deletedCfv): void {
        $ids = Sangha::query()->pluck('id')->all();
        if ($ids !== []) {
            $deletedCfv = CustomFieldValue::query()
                ->whereIn('entity_id', $ids)
                ->whereIn('entity_type', $entityTypes)
                ->delete();

            $deletedSanghas = Sangha::query()->whereIn('id', $ids)->delete();
        }

        SiteSetting::query()->where('key', 'pass_sanghas_snapshot')->delete();
        SiteSetting::query()->where('key', MonasteryPortalResultsSnapshot::KEY)->delete();
    });

    $this->info("Removed {$deletedSanghas} sangha(s), {$deletedCfv} custom field value row(s), and cleared pass / portal result snapshots.");

    return self::SUCCESS;
})->purpose('Delete every sangha and related published list data (irreversible)');
