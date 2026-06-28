@extends('layouts.app')
@section('title','Admin · Statistik')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        @php
            $cards = [
                ['Total Pengguna',$stats['users'],'sky'],
                ['Total Device',$stats['devices'],'violet'],
                ['Device Terhubung',$stats['devices_connected'],'emerald'],
                ['Total Pesan',number_format($stats['messages_total']),'brand'],
                ['Pesan Hari Ini',number_format($stats['messages_today']),'amber'],
                ['Pesan Gagal',number_format($stats['messages_failed']),'red'],
            ];
        @endphp
        @foreach($cards as $c)
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <p class="text-3xl font-extrabold text-{{ $c[2]==='brand'?'brand-600':$c[2].'-600' }}">{{ $c[1] }}</p>
                <p class="text-sm text-slate-500 mt-1">{{ $c[0] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4"><h3 class="font-bold text-slate-900">Pengguna Terbaru</h3><a href="{{ route('admin.users') }}" class="text-xs text-brand-600 font-semibold">Semua</a></div>
            <div class="space-y-3">
                @foreach($recentUsers as $u)
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-semibold text-sm">{{ strtoupper(substr($u->name,0,1)) }}</span>
                        <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-slate-800 truncate">{{ $u->name }}</p><p class="text-xs text-slate-400">{{ $u->email }}</p></div>
                        <span class="text-[10px] uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-bold">{{ $u->plan }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4"><h3 class="font-bold text-slate-900">Pesan Terbaru</h3><a href="{{ route('admin.logs') }}" class="text-xs text-brand-600 font-semibold">Semua</a></div>
            <div class="space-y-2">
                @foreach($recentLogs as $log)
                    <div class="flex items-center gap-3 text-sm py-1.5 border-b border-slate-50 last:border-0">
                        <span class="w-2 h-2 rounded-full bg-{{ $log->statusColor() }}-500"></span>
                        <span class="font-medium text-slate-700">{{ $log->to_number }}</span>
                        <span class="text-slate-400 text-xs">{{ $log->user?->name }}</span>
                        <span class="ml-auto text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
