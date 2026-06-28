<?php

namespace App\Listeners;

use App\Models\AutoReply;
use App\Models\Device;
use App\Services\GatewayService;
use Kstmostofa\LaravelWhatsApp\Events\Web\MessageReceived;

/**
 * Listens to inbound WhatsApp Web messages (dispatched by
 * `php artisan whatsapp:web:listen {session}`) and sends a configured
 * auto-reply when an active rule matches the message body.
 */
class AutoReplyListener
{
    public function __construct(protected GatewayService $gateway)
    {
    }

    public function handle(MessageReceived $event): void
    {
        if ($event->fromMe()) {
            return;
        }

        $body = trim((string) $event->body());

        if ($body === '') {
            return;
        }

        $from = $event->from();

        if (! $from) {
            return;
        }

        $device = Device::query()->where('session_name', $event->sessionId)->first();

        if (! $device) {
            return;
        }

        $user = $device->user;

        if (! $user || ($user->is_active ?? true) === false) {
            return;
        }

        $isGroup = $event->isGroup();

        $rules = AutoReply::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->where(function ($q) use ($device) {
                $q->whereNull('device_id')->orWhere('device_id', $device->id);
            })
            ->orderByDesc('priority')
            ->orderBy('id')
            ->get();

        foreach ($rules as $rule) {
            if ($isGroup && $rule->skip_groups) {
                continue;
            }

            if (! $rule->matches($body)) {
                continue;
            }

            if ($user->remainingQuota() <= 0) {
                return;
            }

            $this->gateway->sendText($device, $from, $rule->reply_text, ['source' => 'auto_reply']);

            $rule->forceFill([
                'triggered_count' => $rule->triggered_count + 1,
                'last_triggered_at' => now(),
            ])->saveQuietly();

            return; // first matching rule wins
        }
    }
}
