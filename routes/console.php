<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Submit URL baru ke IndexNow setiap hari 02:45
Schedule::command('seo:indexnow --new')->dailyAt('02:45')->withoutOverlapping();
