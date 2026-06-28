<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\MessageLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'users' => User::count(),
            'devices' => Device::count(),
            'devices_connected' => Device::whereIn('status', Device::READY_STATES)->count(),
            'messages_total' => MessageLog::count(),
            'messages_today' => MessageLog::where('created_at', '>=', now()->startOfDay())->count(),
            'messages_failed' => MessageLog::where('status', 'failed')->count(),
        ];

        $recentUsers = User::latest()->limit(6)->get();
        $recentLogs = MessageLog::with(['user', 'device'])->latest()->limit(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentLogs'));
    }

    public function users(Request $request): View
    {
        $users = User::withCount(['devices', 'messageLogs'])
            ->latest()
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:user,admin'],
            'plan' => ['required', 'string', 'max:50'],
            'device_limit' => ['required', 'integer', 'min:0', 'max:1000'],
            'monthly_quota' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'role' => $data['role'],
            'plan' => $data['plan'],
            'device_limit' => $data['device_limit'],
            'monthly_quota' => $data['monthly_quota'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', "User {$user->name} diperbarui.");
    }

    public function devices(): View
    {
        $devices = Device::with('user')->latest()->paginate(20);

        return view('admin.devices', compact('devices'));
    }

    public function logs(): View
    {
        $logs = MessageLog::with(['user', 'device'])->latest()->paginate(30);

        return view('admin.logs', compact('logs'));
    }
}
