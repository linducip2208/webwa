@extends('layouts.app')
@section('title','Admin · Semua Log')
@section('content')
<div class="max-w-6xl mx-auto">
    <h2 class="text-xl font-extrabold text-slate-900 mb-6">Semua Log Pesan</h2>
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                    <tr><th class="text-left px-5 py-3 font-semibold">Tujuan</th><th class="text-left px-5 py-3 font-semibold">Pemilik</th><th class="text-left px-5 py-3 font-semibold">Device</th><th class="text-left px-5 py-3 font-semibold">Tipe</th><th class="text-left px-5 py-3 font-semibold">Status</th><th class="text-left px-5 py-3 font-semibold">Waktu</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $log->to_number }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $log->user?->name }}</td>
                            <td class="px-5 py-3 text-slate-400 text-xs">{{ $log->device?->name ?? '—' }}</td>
                            <td class="px-5 py-3 capitalize text-slate-500">{{ $log->type }}</td>
                            <td class="px-5 py-3"><span class="text-xs px-2 py-1 rounded-full font-semibold bg-{{ $log->statusColor() }}-100 text-{{ $log->statusColor() }}-700 capitalize">{{ $log->status }}</span></td>
                            <td class="px-5 py-3 text-slate-400 whitespace-nowrap">{{ $log->created_at->format('d/m H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">Belum ada log.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</div>
@endsection
