<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'user_id', 'name', 'key_prefix', 'key_hash', 'abilities',
    'is_active', 'request_count', 'last_used_at', 'expires_at',
])]
#[Hidden(['key_hash'])]
class ApiKey extends Model
{
    protected function casts(): array
    {
        return [
            'abilities' => 'array',
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new API key. Returns [ApiKey $model, string $plainKey].
     * The plain key is shown to the user ONCE and never stored.
     *
     * @return array{0: self, 1: string}
     */
    public static function generate(User $user, string $name, array $abilities = ['*']): array
    {
        $prefix = 'wwa_'.Str::lower(Str::random(8));
        $secret = Str::random(40);
        $plain = $prefix.'.'.$secret;

        $model = static::create([
            'user_id' => $user->id,
            'name' => $name,
            'key_prefix' => $prefix,
            'key_hash' => hash('sha256', $plain),
            'abilities' => $abilities,
            'is_active' => true,
        ]);

        return [$model, $plain];
    }

    public static function findByPlain(string $plain): ?self
    {
        $hash = hash('sha256', $plain);

        return static::query()
            ->where('key_hash', $hash)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    public function markUsed(): void
    {
        $this->forceFill([
            'last_used_at' => now(),
            'request_count' => $this->request_count + 1,
        ])->saveQuietly();
    }
}
