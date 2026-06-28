<?php

namespace App\Http\Controllers;

use App\Services\GatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SendController extends Controller
{
    public function create(Request $request): View
    {
        $devices = $request->user()->devices()->get();

        return view('app.send', compact('devices'));
    }

    public function store(Request $request, GatewayService $gateway): RedirectResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'to' => ['required', 'string', 'max:64'],
            'type' => ['required', 'in:text,image,video,audio,document'],
            'body' => ['nullable', 'string', 'max:4096'],
            'media_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $device = $request->user()->devices()->findOrFail($data['device_id']);

        if ($request->user()->remainingQuota() <= 0) {
            return back()->withErrors(['to' => 'Kuota pesan bulanan habis.'])->withInput();
        }

        if ($data['type'] === 'text') {
            $request->validate(['body' => ['required', 'string', 'max:4096']]);
            $log = $gateway->sendText($device, $data['to'], $data['body'], ['source' => 'dashboard']);
        } else {
            $request->validate(['media_url' => ['required', 'url']]);
            $log = $gateway->sendMedia($device, $data['to'], $data['type'], $data['media_url'], $data['body'] ?? null, ['source' => 'dashboard']);
        }

        if ($log->status === 'failed') {
            return back()->withErrors(['to' => 'Gagal mengirim: '.$log->error])->withInput();
        }

        return back()->with('status', 'Pesan terkirim ke '.$data['to'].'.');
    }
}
