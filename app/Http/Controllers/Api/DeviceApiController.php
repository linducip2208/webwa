<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kstmostofa\LaravelWhatsApp\Exceptions\SidecarException;

class DeviceApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $devices = $request->user()->devices()->get()->map(fn (Device $d) => [
            'id' => $d->id,
            'name' => $d->name,
            'session' => $d->session_name,
            'backend' => $d->backend,
            'status' => $d->status,
            'phone' => $d->phone,
            'connected' => $d->isConnected(),
        ]);

        return response()->json(['success' => true, 'data' => $devices]);
    }

    public function status(Request $request, string $device): JsonResponse
    {
        $model = $this->resolve($request, $device);

        if ($model->isCloud()) {
            return response()->json(['success' => true, 'status' => $model->status, 'connected' => true]);
        }

        try {
            $state = $model->webSession()->state();
            $status = $state['status'] ?? 'disconnected';
            $model->update(['status' => $status]);

            return response()->json([
                'success' => true,
                'status' => $status,
                'connected' => in_array($status, Device::READY_STATES, true),
            ]);
        } catch (SidecarException $e) {
            return response()->json(['success' => false, 'message' => 'Sidecar offline', 'status' => $model->status], 503);
        }
    }

    public function qr(Request $request, string $device): JsonResponse
    {
        $model = $this->resolve($request, $device);

        if ($model->isCloud()) {
            return response()->json(['success' => false, 'message' => 'Cloud API tidak memakai QR.'], 422);
        }

        try {
            $model->webSession()->start();
            $qr = $model->webSession()->qr();

            return response()->json([
                'success' => true,
                'status' => $qr['status'] ?? null,
                'qr' => $qr['qr'] ?? null,
            ]);
        } catch (SidecarException $e) {
            return response()->json(['success' => false, 'message' => 'Sidecar offline'], 503);
        }
    }

    protected function resolve(Request $request, string $identifier): Device
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
}
