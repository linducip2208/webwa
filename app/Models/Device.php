<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Kstmostofa\LaravelWhatsApp\Facades\WhatsApp;
use Kstmostofa\LaravelWhatsApp\Web\WebSession;

#[Fillable([
    'user_id', 'name', 'session_name', 'backend', 'status', 'phone',
    'push_name', 'webhook_url', 'webhook_events', 'cloud_phone_number_id',
    'cloud_access_token', 'meta', 'last_connected_at', 'last_activity_at',
])]
class Device extends Model
{
    public const READY_STATES = ['ready', 'authenticated', 'connected'];

    protected function casts(): array
    {
        return [
            'webhook_events' => 'array',
            'meta' => 'array',
            'cloud_access_token' => 'encrypted',
            'last_connected_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Device $device) {
            if (empty($device->session_name)) {
                $device->session_name = 'u'.($device->user_id ?? 'x').'_'.Str::lower(Str::random(12));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messageLogs(): HasMany
    {
        return $this->hasMany(MessageLog::class);
    }

    public function isConnected(): bool
    {
        return in_array($this->status, self::READY_STATES, true);
    }

    public function isCloud(): bool
    {
        return $this->backend === 'cloud';
    }

    public function webSession(): WebSession
    {
        return WhatsApp::web($this->session_name);
    }

    public function statusColor(): string
    {
        return match (true) {
            $this->isConnected() => 'emerald',
            in_array($this->status, ['qr', 'connecting', 'authenticated'], true) => 'amber',
            in_array($this->status, ['auth_failure', 'error'], true) => 'red',
            default => 'slate',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'ready', 'connected' => 'Terhubung',
            'authenticated' => 'Terautentikasi',
            'qr' => 'Menunggu Scan QR',
            'connecting' => 'Menghubungkan',
            'auth_failure' => 'Gagal Autentikasi',
            'error' => 'Error',
            default => 'Terputus',
        };
    }
}
