<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\GatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function __construct(protected GatewayService $gateway)
    {
    }

    public function sendText(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device' => ['required', 'string'],
            'to' => ['required', 'string', 'max:64'],
            'message' => ['required', 'string', 'max:4096'],
        ]);

        if ($resp = $this->quotaGuard($request)) {
            return $resp;
        }

        $device = $this->resolveDevice($request, $data['device']);

        $log = $this->gateway->sendText($device, $data['to'], $data['message'], [
            'source' => 'api',
            'api_key_id' => optional($request->attributes->get('api_key'))->id,
        ]);

        return $this->logResponse($log);
    }

    public function sendMedia(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device' => ['required', 'string'],
            'to' => ['required', 'string', 'max:64'],
            'type' => ['required', 'in:image,video,audio,document'],
            'url' => ['required', 'url', 'max:1000'],
            'caption' => ['nullable', 'string', 'max:1024'],
        ]);

        if ($resp = $this->quotaGuard($request)) {
            return $resp;
        }

        $device = $this->resolveDevice($request, $data['device']);

        $log = $this->gateway->sendMedia($device, $data['to'], $data['type'], $data['url'], $data['caption'] ?? null, [
            'source' => 'api',
            'api_key_id' => optional($request->attributes->get('api_key'))->id,
        ]);

        return $this->logResponse($log);
    }

    protected function resolveDevice(Request $request, string $identifier): Device
    {
        $device = $request->user()->devices()
            ->where(function ($q) use ($identifier) {
                $q->where('id', is_numeric($identifier) ? (int) $identifier : 0)
                    ->orWhere('session_name', $identifier)
                    ->orWhere('name', $identifier);
            })
            ->first();

        abort_unless($device, 404, 'Device tidak ditemukan.');

        return $device;
    }

    protected function quotaGuard(Request $request): ?JsonResponse
    {
        if ($request->user()->remainingQuota() <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota pesan bulanan habis.',
            ], 429);
        }

        return null;
    }

    protected function logResponse($log): JsonResponse
    {
        return response()->json([
            'success' => $log->status !== 'failed',
            'message_id' => $log->wa_message_id,
            'status' => $log->status,
            'error' => $log->error,
            'data' => [
                'id' => $log->id,
                'to' => $log->to_number,
                'type' => $log->type,
                'backend' => $log->backend,
                'created_at' => $log->created_at,
            ],
        ], $log->status === 'failed' ? 502 : 200);
    }
}
