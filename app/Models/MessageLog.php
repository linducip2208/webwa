<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'device_id', 'api_key_id', 'direction', 'backend',
    'to_number', 'from_number', 'type', 'body', 'media_url', 'status',
    'wa_message_id', 'error', 'payload', 'response', 'source',
])]
class MessageLog extends Model
{
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'response' => 'array',
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

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'sent', 'delivered', 'read' => 'emerald',
            'queued' => 'amber',
            'failed' => 'red',
            default => 'slate',
        };
    }
}
