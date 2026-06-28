<?php

namespace App\Console\Commands;

use App\Services\Seo\IndexNowService;
use Illuminate\Console\Command;

class IndexNowSubmit extends Command
{
    protected $signature = 'seo:indexnow
                            {--all : Submit semua URL sitemap}
                            {--new : Submit hanya URL baru sejak run terakhir}
                            {--url= : Submit satu URL}';

    protected $description = 'Submit URL ke IndexNow (Bing, Yandex, Seznam, Naver)';

    public function handle(IndexNowService $service): int
    {
        if ($url = $this->option('url')) {
            $r = $service->submitSingle($url);
            $this->info('Submitted 1 URL. Success: '.($r['success'] ? 'yes' : 'no'));

            return self::SUCCESS;
        }

        if ($this->option('new')) {
            $r = $service->submitNewOnly();
            $this->info('URL baru disubmit: '.$r['submitted']);

            return self::SUCCESS;
        }

        $this->info('Submitting semua URL ke IndexNow...');
        $r = $service->submitAll();
        $this->info($r['success'] ? "Berhasil submit {$r['submitted']} URL." : 'Gagal.');

        return self::SUCCESS;
    }
}
