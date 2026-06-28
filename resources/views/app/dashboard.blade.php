@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- Welcome --}}
    <div class="rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 p-6 sm:p-8 text-white relative overflow-hidden">
        <div class="absolute -right-10 -top-10 text-[12rem] opacity-10 select-none">💬</div>
        <h2 class="text-2xl font-extrabold mb-1">Halo, {{ auth()->user()->name }} 👋</h2>
        <p class="text-brand-50/90">Kelola device WhatsApp, kirim pesan, dan pantau API Anda dari sini.</p>
        <div class="mt-5 flex flex-wrap gap-3">
            <a href="{{ route('devices.index') }}" class="px-4 py-2 rounded-lg bg-white text-brand-700 text-sm font-semibold hover:bg-brand-50 transition">+ Tambah Device</a>
            <a href="{{ route('api-keys.index') }}" class="px-4 py-2 rounded-lg bg-white/15 backdrop-blur text-white text-sm font-semibold hover:bg-white/25 transition">Buat API Key</a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $cards = [
                ['Device Aktif', $stats['devices_connected'].' / '.$stats['devices_total'], 'emerald', 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                ['Pesan Hari Ini', $stats['messages_today'], 'sky', 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                ['Pesan Bulan Ini', $stats['messages_month'].' / '.$stats['quota'], 'violet', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['API Key Aktif', $stats['api_keys'], 'amber', 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
            ];
        @endphp
        @foreach($cards as $c)
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="w-10 h-10 rounded-xl bg-{{ $c[2] }}-100 text-{{ $c[2] }}-600 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $c[3] }}"/></svg>
                </div>
                <p class="text-2xl font-extrabold text-slate-900">{{ $c[1] }}</p>
                <p class="text-sm text-slate-500 mt-0.5">{{ $c[0] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="font-bold text-slate-900 mb-4">Pesan 7 Hari Terakhir</h3>
            <div class="flex items-end gap-3 h-48">
                @php $max = max(1, collect($series)->max('value')); @endphp
                @foreach($series as $s)
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-brand-100 rounded-t-lg relative group" style="height:{{ max(4, ($s['value']/$max)*100) }}%">
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-brand-600 to-brand-400 rounded-t-lg transition-all" style="height:100%"></div>
                            <span class="absolute -top-6 inset-x-0 text-center text-xs font-semibold text-slate-600">{{ $s['value'] }}</span>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">{{ $s['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Devices quick --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-900">Device Anda</h3>
                <a href="{{ route('devices.index') }}" class="text-xs text-brand-600 font-semibold">Lihat semua</a>
            </div>
            @forelse($devices->take(4) as $device)
                <a href="{{ route('devices.show',$device) }}" class="flex items-center gap-3 py-2.5 border-b border-slate-100 last:border-0">
                    <span class="w-2.5 h-2.5 rounded-full bg-{{ $device->statusColor() }}-500"></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $device->name }}</p>
                        <p class="text-xs text-slate-400">{{ $device->statusLabel() }}{{ $device->phone ? ' · '.$device->phone : '' }}</p>
                    </div>
                    <span class="text-[10px] uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-semibold">{{ $device->backend }}</span>
                </a>
            @empty
                <p class="text-sm text-slate-400 py-6 text-center">Belum ada device.<br><a href="{{ route('devices.index') }}" class="text-brand-600 font-semibold">Tambah sekarang →</a></p>
            @endforelse
        </div>
    </div>

    {{-- Recent logs --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="flex items-center justify-between p-6 pb-3">
            <h3 class="font-bold text-slate-900">Aktivitas Terbaru</h3>
            <a href="{{ route('logs.index') }}" class="text-xs text-brand-600 font-semibold">Semua log</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                    <tr><th class="text-left px-6 py-3 font-semibold">Tujuan</th><th class="text-left px-6 py-3 font-semibold">Tipe</th><th class="text-left px-6 py-3 font-semibold">Device</th><th class="text-left px-6 py-3 font-semibold">Status</th><th class="text-left px-6 py-3 font-semibold">Waktu</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-brand-50/40">
                            <td class="px-6 py-3 font-medium text-slate-700">{{ $log->to_number }}</td>
                            <td class="px-6 py-3 text-slate-500 capitalize">{{ $log->type }}</td>
                            <td class="px-6 py-3 text-slate-500">{{ $log->device?->name ?? '—' }}</td>
                            <td class="px-6 py-3"><span class="text-xs px-2 py-1 rounded-full bg-{{ $log->statusColor() }}-100 text-{{ $log->statusColor() }}-700 font-semibold capitalize">{{ $log->status }}</span></td>
                            <td class="px-6 py-3 text-slate-400">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada aktivitas. <a href="{{ route('send.create') }}" class="text-brand-600 font-semibold">Kirim pesan pertama →</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
