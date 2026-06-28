<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Kstmostofa\LaravelWhatsApp\Exceptions\SidecarException;

class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        $devices = $request->user()->devices()->withCount('messageLogs')->latest()->get();

        return view('app.devices.index', compact('devices'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->canCreateDevice()) {
            return back()->withErrors(['name' => "Batas device tercapai ({$user->device_limit}). Upgrade paket untuk menambah device."]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'backend' => ['required', 'in:web,cloud'],
            'webhook_url' => ['nullable', 'url', 'max:255'],
        ]);

        $device = $user->devices()->create([
            'name' => $data['name'],
            'backend' => $data['backend'],
            'webhook_url' => $data['webhook_url'] ?? null,
            'status' => 'disconnected',
        ]);

        return redirect()->route('devices.show', $device)
            ->with('status', 'Device dibuat. Klik "Hubungkan" untuk memindai QR.');
    }

    public function show(Request $request, Device $device): View
    {
        $this->authorizeDevice($request, $device);

        return view('app.devices.show', compact('device'));
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        $this->authorizeDevice($request, $device);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'webhook_url' => ['nullable', 'url', 'max:255'],
        ]);

        $device->update($data);

        return back()->with('status', 'Device diperbarui.');
    }

    public function destroy(Request $request, Device $device): RedirectResponse
    {
        $this->authorizeDevice($request, $device);

        if (! $device->isCloud()) {
            try {
                $device->webSession()->destroy();
            } catch (SidecarException) {
                // sidecar offline — nothing to clean up remotely
            }
        }

        $device->delete();

        return redirect()->route('devices.index')->with('status', 'Device dihapus.');
    }

    /** Start a Web session and return the first QR. */
    public function connect(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        if ($device->isCloud()) {
            return response()->json(['ok' => false, 'message' => 'Device Cloud API tidak memerlukan QR.'], 422);
        }

        try {
            $response = $device->webSession()->start();
            $device->update(['status' => $response['status'] ?? 'connecting']);

            return response()->json([
                'ok' => true,
                'status' => $response['status'] ?? 'connecting',
                'qr' => $response['qr'] ?? null,
            ]);
        } catch (SidecarException $e) {
            return response()->json([
                'ok' => false,
                'offline' => true,
                'message' => 'Sidecar WhatsApp belum berjalan. Windows: jalankan scripts\\start-sidecar.ps1 — Linux/macOS: php artisan whatsapp:sidecar:start',
                'detail' => $e->getMessage(),
            ], 503);
        }
    }

    /** Poll the live state + QR of a Web session. */
    public function state(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        if ($device->isCloud()) {
            return response()->json(['ok' => true, 'status' => $device->status, 'qr' => null]);
        }

        try {
            $state = $device->webSession()->state();
            $status = $state['status'] ?? 'disconnected';
            $qr = null;

            if ($status === 'qr') {
                $qr = $device->webSession()->qr()['qr'] ?? null;
            }

            $update = ['status' => $status];

            if (in_array($status, Device::READY_STATES, true)) {
                $update['last_connected_at'] = now();
                try {
                    $info = $device->webSession()->info();
                    $update['phone'] = $info['wid']['user'] ?? $info['me']['user'] ?? $device->phone;
                    $update['push_name'] = $info['pushname'] ?? $info['pushName'] ?? $device->push_name;
                } catch (SidecarException) {
                    // info not ready yet
                }
            }

            $device->update($update);

            return response()->json([
                'ok' => true,
                'status' => $status,
                'qr' => $qr,
                'connected' => $device->isConnected(),
            ]);
        } catch (SidecarException $e) {
            return response()->json([
                'ok' => false,
                'offline' => true,
                'status' => $device->status,
                'message' => 'Sidecar tidak terjangkau.',
            ], 503);
        }
    }

    public function disconnect(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        try {
            $device->webSession()->stop();
        } catch (SidecarException) {
            // already offline
        }

        $device->update(['status' => 'disconnected']);

        return response()->json(['ok' => true]);
    }

    public function reset(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        try {
            $device->webSession()->destroy();
        } catch (SidecarException) {
            //
        }

        $device->update(['status' => 'disconnected', 'phone' => null, 'push_name' => null]);

        return response()->json(['ok' => true]);
    }

    protected function authorizeDevice(Request $request, Device $device): void
    {
        abort_unless($device->user_id === $request->user()->id, 403);
    }
}
