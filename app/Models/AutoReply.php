<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'device_id', 'name', 'match_type', 'keyword', 'reply_text',
    'case_sensitive', 'skip_groups', 'is_active', 'priority',
])]
class AutoReply extends Model
{
    public const MATCH_TYPES = ['contains', 'exact', 'starts_with', 'regex'];

    protected function casts(): array
    {
        return [
            'case_sensitive' => 'boolean',
            'skip_groups' => 'boolean',
            'is_active' => 'boolean',
            'priority' => 'integer',
            'triggered_count' => 'integer',
            'last_triggered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Decide whether an incoming message body triggers this rule.
     */
    public function matches(string $body): bool
    {
        $keyword = (string) $this->keyword;

        if ($this->match_type === 'regex') {
            $flags = $this->case_sensitive ? '' : 'i';
            $pattern = '~'.str_replace('~', '\~', $keyword).'~'.$flags;

            return @preg_match($pattern, $body) === 1;
        }

        $haystack = $this->case_sensitive ? $body : mb_strtolower($body);
        $needle = $this->case_sensitive ? $keyword : mb_strtolower($keyword);

        return match ($this->match_type) {
            'exact' => trim($haystack) === trim($needle),
            'starts_with' => str_starts_with(trim($haystack), $needle),
            default => $needle !== '' && str_contains($haystack, $needle),
        };
    }

    public function matchTypeLabel(): string
    {
        return match ($this->match_type) {
            'exact' => 'Sama persis',
            'starts_with' => 'Diawali',
            'regex' => 'Regex',
            default => 'Mengandung',
        };
    }
}
