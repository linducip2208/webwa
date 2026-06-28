<?php

namespace App\Support;

use App\Models\BlogPost;

/**
 * Single source of truth for public SEO URLs — used by both the sitemap
 * and IndexNow submission so they never drift apart.
 */
class SeoUrls
{
    /**
     * @return array<int, array{loc:string, freq:string, priority:string}>
     */
    public static function all(): array
    {
        $urls = [];
        $add = function (string $path, string $freq = 'weekly', string $priority = '0.6') use (&$urls) {
            $urls[] = ['loc' => url($path), 'freq' => $freq, 'priority' => $priority];
        };

        $add('/', 'daily', '1.0');
        $add('/harga', 'monthly', '0.8');
        $add('/docs', 'weekly', '0.8');
        $add('/blog', 'daily', '0.7');
        $add('/best-whatsapp-gateway', 'weekly', '0.7');

        foreach (BlogPost::published()->latest('published_at')->get() as $post) {
            $add('/blog/'.$post->slug, 'weekly', '0.6');
        }

        foreach (config('webwa.cities') as $city) {
            $add('/whatsapp-gateway-'.$city, 'monthly', '0.6');
        }

        foreach (config('webwa.industries') as $industry) {
            $add('/whatsapp-gateway-untuk-'.$industry, 'monthly', '0.6');
        }

        foreach (config('webwa.competitors') as $competitor) {
            $add('/alternatif-'.$competitor, 'monthly', '0.6');
        }

        $competitors = config('webwa.competitors');
        $count = count($competitors);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $add('/bandingkan/'.$competitors[$i].'-vs-'.$competitors[$j], 'monthly', '0.5');
            }
        }

        return $urls;
    }

    /**
     * @return array<int, string>
     */
    public static function locs(): array
    {
        return array_map(fn ($u) => $u['loc'], static::all());
    }
}
