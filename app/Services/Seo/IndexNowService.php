<?php

namespace App\Services\Seo;

use App\Support\SeoUrls;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * IndexNow — auto-submit URLs ke Bing, Yandex, Seznam, Naver.
 * Key file: public/indexnow-key.txt
 */
class IndexNowService
{
    protected string $key;
    protected string $keyLocation;

    protected array $searchEngines = [
        'https://www.bing.com/indexnow',
        'https://yandex.com/indexnow',
        'https://search.seznam.cz/indexnow',
        'https://indexnow.naver.com/indexnow',
    ];

    public function __construct()
    {
        $this->key = trim((string) @file_get_contents(public_path('indexnow-key.txt')))
            ?: (string) config('app.indexnow_key', env('INDEXNOW_KEY', ''));
        $this->keyLocation = rtrim((string) config('app.url'), '/').'/indexnow-key.txt';
    }

    public function submit(array $urls): array
    {
        $urls = array_values(array_unique(array_filter($urls)));

        if (empty($urls) || $this->key === '') {
            return ['success' => false, 'message' => 'No URLs or IndexNow key missing'];
        }

        $payload = [
            'host' => parse_url(config('app.url'), PHP_URL_HOST),
            'key' => $this->key,
            'keyLocation' => $this->keyLocation,
            'urlList' => $urls,
        ];

        $results = [];
        foreach ($this->searchEngines as $engine) {
            try {
                $response = Http::timeout(15)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($engine, $payload);
                $results[$engine] = ['status' => $response->status(), 'success' => $response->successful()];
            } catch (\Throwable $e) {
                $results[$engine] = ['status' => 0, 'error' => $e->getMessage()];
            }
        }

        Log::info('IndexNow: submitted '.count($urls).' URLs', $results);

        return ['success' => true, 'submitted' => count($urls), 'engines' => $results];
    }

    public function submitSingle(string $url): array
    {
        return $this->submit([$url]);
    }

    public function submitAll(): array
    {
        $total = 0;
        foreach (array_chunk(SeoUrls::locs(), 5000) as $chunk) {
            $result = $this->submit($chunk);
            if ($result['success']) {
                $total += count($chunk);
            }
            sleep(1);
        }

        return ['success' => true, 'submitted' => $total];
    }

    public function submitNewOnly(): array
    {
        $urls = SeoUrls::locs();
        $lastSubmit = cache('indexnow_last_submit', []);
        $newUrls = array_values(array_diff($urls, $lastSubmit));

        if (empty($newUrls)) {
            return ['success' => true, 'submitted' => 0, 'message' => 'No new URLs'];
        }

        $result = $this->submit($newUrls);

        $allSubmitted = array_values(array_unique(array_merge($lastSubmit, $newUrls)));
        if (count($allSubmitted) > 50000) {
            $allSubmitted = array_slice($allSubmitted, -50000);
        }
        cache(['indexnow_last_submit' => $allSubmitted], now()->addYear());

        return $result;
    }
}
