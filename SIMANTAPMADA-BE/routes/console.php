<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// JRA (ANRI): tandai arsip yang masa retensinya sudah lewat — tiap hari 01:00.
// Aktifkan scheduler: `php artisan schedule:work` (dev) / cron `schedule:run` (prod).
// Bisa juga dijalankan manual kapan saja: `php artisan arsip:proses-retensi`.
Schedule::command('arsip:proses-retensi')->dailyAt('01:00');
