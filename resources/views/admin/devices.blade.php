@extends('layouts.app')
@section('title','Admin · Semua Device')
@section('content')
<div class="max-w-6xl mx-auto">
    <h2 class="text-xl font-extrabold text-slate-900 mb-6">Semua Device</h2>
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                    <tr><th class="text-left px-5 py-3 font-semibold">Device</th><th class="text-left px-5 py-3 font-semibold">Pemilik</th><th class="text-left px-5 py-3 font-semibold">Backend</th><th class="text-left px-5 py-3 font-semibold">Nomor</th><th class="text-left px-5 py-3 font-semibold">Status</th><th class="text-left px-5 py-3 font-semibold">Dibuat</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($devices as $d)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3"><p class="font-semibold text-slate-800">{{ $d->name }}</p><code class="text-xs text-slate-400">{{ $d->session_name }}</code></td>
                            <td class="px-5 py-3 text-slate-600">{{ $d->user?->name }}</td>
                            <td class="px-5 py-3"><span class="text-[10px] uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-bold">{{ $d->backend }}</span></td>
                            <td class="px-5 py-3 text-slate-500">{{ $d->phone ?? '—' }}</td>
                            <td class="px-5 py-3"><span class="text-xs px-2 py-1 rounded-full font-semibold bg-{{ $d->statusColor() }}-100 text-{{ $d->statusColor() }}-700">{{ $d->statusLabel() }}</span></td>
                            <td class="px-5 py-3 text-slate-400">{{ $d->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">Belum ada device.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $devices->links() }}</div>
</div>
@endsection
