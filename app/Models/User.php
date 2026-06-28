<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'email', 'password', 'role', 'plan', 'device_limit',
    'monthly_quota', 'is_active', 'company', 'phone', 'last_login_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function messageLogs(): HasMany
    {
        return $this->hasMany(MessageLog::class);
    }

    public function autoReplies(): HasMany
    {
        return $this->hasMany(AutoReply::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function messagesUsedThisMonth(): int
    {
        return $this->messageLogs()
            ->where('direction', 'outbound')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
    }

    public function remainingQuota(): int
    {
        return max(0, $this->monthly_quota - $this->messagesUsedThisMonth());
    }

    public function canCreateDevice(): bool
    {
        return $this->devices()->count() < $this->device_limit;
    }
}
