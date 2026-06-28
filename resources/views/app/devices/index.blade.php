@extends('layouts.app')
@section('title','Device')
@section('content')
<div class="max-w-6xl mx-auto" x-data="{ create:false }">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Device WhatsApp</h2>
            <p class="text-sm text-slate-500">{{ $devices->count() }} dari {{ auth()->user()->device_limit }} device terpakai</p>
        </div>
        <button @click="create=true" class="px-4 py-2.5 rounded-lg bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-600/20">+ Tambah Device</button>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($devices as $device)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-lg hover:shadow-slate-200/60 transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-{{ $device->statusColor() }}-100 text-{{ $device->statusColor() }}-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[10px] uppercase px-2 py-1 rounded bg-slate-100 text-slate-500 font-bold tracking-wide">{{ $device->backend }}</span>
                </div>
                <h3 class="font-bold text-slate-900">{{ $device->name }}</h3>
                <p class="text-sm text-slate-400 mb-3">{{ $device->phone ?? 'Belum tertaut' }}</p>
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 rounded-full bg-{{ $device->statusColor() }}-500"></span>
                    <span class="text-xs font-semibold text-{{ $device->statusColor() }}-600">{{ $device->statusLabel() }}</span>
                    <span class="ml-auto text-xs text-slate-400">{{ $device->message_logs_count }} pesan</span>
                </div>
                <a href="{{ route('devices.show',$device) }}" class="block text-center py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Kelola</a>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
                <div class="text-5xl mb-3">📱</div>
                <h3 class="font-bold text-slate-800 mb-1">Belum ada device</h3>
                <p class="text-sm text-slate-500 mb-4">Tambah device lalu pindai QR untuk menghubungkan WhatsApp.</p>
                <button @click="create=true" class="px-4 py-2 rounded-lg bg-brand-600 text-white text-sm font-semibold">+ Tambah Device Pertama</button>
            </div>
        @endforelse
    </div>

    {{-- Create modal --}}
    <div x-show="create" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="create=false" class="absolute inset-0 bg-slate-900/50"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" x-transition>
            <h3 class="text-lg font-bold text-slate-900 mb-4">Tambah Device</h3>
            <form method="POST" action="{{ route('devices.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Device</label>
                    <input name="name" required class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none" placeholder="mis. CS Utama">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Backend</label>
                    <select name="backend" class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none">
                        <option value="web">Web (nomor pribadi · QR)</option>
                        <option value="cloud">Cloud API (Meta resmi)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Webhook URL <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input name="webhook_url" type="url" class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none" placeholder="https://app.anda.com/webhook">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="create=false" class="flex-1 py-2.5 rounded-lg bg-slate-100 text-slate-700 font-semibold">Batal</button>
                    <button class="flex-1 py-2.5 rounded-lg bg-brand-600 text-white font-semibold hover:bg-brand-700">Buat</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
