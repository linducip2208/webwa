<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\Device;
use App\Models\MessageLog;
use Illuminate\Support\Str;
use Kstmostofa\LaravelWhatsApp\Exceptions\CloudApiException;
use Kstmostofa\LaravelWhatsApp\Exceptions\SidecarException;
use Kstmostofa\LaravelWhatsApp\Facades\WhatsApp;
use Throwable;

/**
 * WebWA gateway service — a thin multi-tenant wrapper over the
 * kstmostofa/laravel-whatsapp facade that scopes every send to a user's
 * Device, persists a MessageLog row, and normalizes errors.
 */
class GatewayService
{
    /**
     * Turn a raw recipient into a whatsapp-web.js chat id (xxxx@c.us) for the
     * Web backend, or keep group / pass-through ids as-is.
     */
    public function normalizeWebRecipient(string $to): string
    {
        $to = trim($to);

        if (Str::contains($to, '@')) {
            return $to; // already a chat id (@c.us / @g.us)
        }

        $digits = preg_replace('/\D+/', '', $to);

        return $digits.'@c.us';
    }

    public function normalizeCloudRecipient(string $to): string
    {
        $to = trim($to);
        $digits = preg_replace('/\D+/', '', $to);

        return '+'.$digits;
    }

    /**
     * Send a plain text message through a device and log it.
     */
    public function sendText(Device $device, string $to, string $body, array $context = []): MessageLog
    {
        return $this->dispatch($device, $to, 'text', $body, null, $context, function ($recipient) use ($device, $body) {
            if ($device->isCloud()) {
                return WhatsApp::messages()->sendText($this->normalizeCloudRecipient($recipient), $body);
            }

            return $device->webSession()->messages()->sendText($this->normalizeWebRecipient($recipient), $body);
        });
    }

    /**
     * Send a media message (image|video|audio|document) by URL.
     */
    public function sendMedia(Device $device, string $to, string $type, string $url, ?string $caption = null, array $context = []): MessageLog
    {
        return $this->dispatch($device, $to, $type, $caption, $url, $context, function ($recipient) use ($device, $type, $url, $caption) {
            if ($device->isCloud()) {
                $cloudType = $type === 'document' ? 'document' : $type;

                return WhatsApp::messages()->{'send'.ucfirst($cloudType)}(
                    $this->normalizeCloudRecipient($recipient),
                    array_filter(['link' => $url, 'caption' => $caption])
                );
            }

            $payload = array_filter(['url' => $url, 'caption' => $caption]);
            $method = 'send'.ucfirst($type);

            return $device->webSession()->messages()->{$method}($this->normalizeWebRecipient($recipient), $payload);
        });
    }

    /**
     * Shared dispatch + logging pipeline.
     */
    protected function dispatch(Device $device, string $to, string $type, ?string $body, ?string $mediaUrl, array $context, callable $send): MessageLog
    {
        $log = MessageLog::create([
            'user_id' => $device->user_id,
            'device_id' => $device->id,
            'api_key_id' => $context['api_key_id'] ?? null,
            'direction' => 'outbound',
            'backend' => $device->backend,
            'to_number' => $to,
            'type' => $type,
            'body' => $body,
            'media_url' => $mediaUrl,
            'status' => 'queued',
            'source' => $context['source'] ?? 'api',
        ]);

        try {
            $response = $send($to);

            $log->update([
                'status' => 'sent',
                'wa_message_id' => $this->extractMessageId($response),
                'response' => $response,
            ]);

            $device->forceFill(['last_activity_at' => now()])->saveQuietly();
        } catch (SidecarException|CloudApiException $e) {
            $log->update(['status' => 'failed', 'error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $log->update(['status' => 'failed', 'error' => $e->getMessage()]);
        }

        return $log->refresh();
    }

    protected function extractMessageId(array $response): ?string
    {
        return $response['id']
            ?? $response['messageId']
            ?? $response['key']['id']
            ?? ($response['messages'][0]['id'] ?? null);
    }

    /**
     * Record API-key usage for rate/quota tracking.
     */
    public function touchApiKey(?ApiKey $key): void
    {
        $key?->markUsed();
    }
}
