@extends('layouts.app')
@section('title','Log Pesan')
@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Log Pesan</h2>
            <p class="text-sm text-slate-500">{{ number_format($logs->total()) }} pesan tercatat</p>
        </div>
        <form class="flex gap-2" method="GET">
            <input name="q" value="{{ request('q') }}" placeholder="Cari nomor/pesan…" class="px-4 py-2 rounded-lg border-[1.5px] border-slate-300 text-sm outline-none focus:border-brand-500 w-48">
            <select name="status" class="px-3 py-2 rounded-lg border-[1.5px] border-slate-300 text-sm outline-none">
                <option value="">Semua status</option>
                @foreach(['sent'=>'Terkirim','delivered'=>'Terkirim','read'=>'Dibaca','queued'=>'Antre','failed'=>'Gagal'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('status')===$v ? 'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                    <tr><th class="text-left px-5 py-3 font-semibold">Tujuan</th><th class="text-left px-5 py-3 font-semibold">Tipe</th><th class="text-left px-5 py-3 font-semibold">Device</th><th class="text-left px-5 py-3 font-semibold">Pesan</th><th class="text-left px-5 py-3 font-semibold">Status</th><th class="text-left px-5 py-3 font-semibold">Sumber</th><th class="text-left px-5 py-3 font-semibold">Waktu</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $log->to_number }}</td>
                            <td class="px-5 py-3 capitalize text-slate-500">{{ $log->type }}</td>
                            <td class="px-5 py-3 text-slate-400 text-xs">{{ $log->device?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-600 max-w-xs truncate" title="{{ $log->body }}">{{ Str::limit($log->body, 50) }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full font-semibold bg-{{ $log->statusColor() }}-100 text-{{ $log->statusColor() }}-700 capitalize">{{ $log->status }}</span>
                                @if($log->error)<p class="text-xs text-red-500 mt-0.5 max-w-[150px] truncate" title="{{ $log->error }}">{{ $log->error }}</p>@endif
                            </td>
                            <td class="px-5 py-3"><span class="text-[10px] uppercase px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 font-semibold">{{ $log->source }}</span></td>
                            <td class="px-5 py-3 text-slate-400 whitespace-nowrap">{{ $log->created_at->format('d/m H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-slate-400">Belum ada log pesan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
@endsection
