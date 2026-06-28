<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Submit URL baru ke IndexNow setiap hari 02:45
Schedule::command('seo:indexnow --new')->dailyAt('02:45')->withoutOverlapping();

// Watchdog: boot ulang sesi WhatsApp Web yang putus (browser/sidecar drop)
// supaya pesan masuk + auto-reply tidak mati. Jalan dulu sebelum listen:all.
Schedule::command('whatsapp:devices:ensure')->everyMinute()->withoutOverlapping();

// Jaga listener whatsapp:web:listen tetap hidup untuk tiap device terhubung.
// Listener yang mati dihidupkan lagi, device baru otomatis di-listen, device
// putus di-prune. Butuh `php artisan schedule:work` (atau cron) tetap berjalan.
Schedule::command('whatsapp:listen:all --prune')->everyMinute()->withoutOverlapping();
