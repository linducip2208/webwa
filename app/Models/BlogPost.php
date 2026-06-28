<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'category_id', 'author_id', 'title', 'slug', 'excerpt', 'content',
    'featured_image', 'meta_title', 'meta_description', 'is_published',
    'published_at', 'views',
])]
class BlogPost extends Model
{
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (BlogPost $post) {
            if (! $post->is_published || app()->runningInConsole()) {
                return;
            }

            $url = url('/blog/'.$post->slug);
            dispatch(function () use ($url) {
                app(\App\Services\Seo\IndexNowService::class)->submitSingle($url);
            })->afterResponse();
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
