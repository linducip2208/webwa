<?php

namespace App\Http\Controllers;

use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $devices = $user->devices()->latest()->get();

        $today = now()->startOfDay();

        $stats = [
            'devices_total' => $devices->count(),
            'devices_connected' => $devices->where('status', 'ready')->count()
                + $devices->whereIn('status', ['authenticated', 'connected'])->count(),
            'messages_today' => $user->messageLogs()
                ->where('direction', 'outbound')
                ->where('created_at', '>=', $today)->count(),
            'messages_month' => $user->messagesUsedThisMonth(),
            'quota' => $user->monthly_quota,
            'remaining' => $user->remainingQuota(),
            'api_keys' => $user->apiKeys()->where('is_active', true)->count(),
        ];

        $recentLogs = $user->messageLogs()
            ->with('device')
            ->latest()
            ->limit(8)
            ->get();

        $chart = $user->messageLogs()
            ->where('direction', 'outbound')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->get()
            ->groupBy(fn (MessageLog $l) => $l->created_at->format('Y-m-d'));

        $series = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $key = $day->format('Y-m-d');
            $series[] = [
                'label' => $day->translatedFormat('D'),
                'value' => isset($chart[$key]) ? $chart[$key]->count() : 0,
            ];
        }

        return view('app.dashboard', compact('stats', 'devices', 'recentLogs', 'series'));
    }
}
