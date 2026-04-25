<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

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
