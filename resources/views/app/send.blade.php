@extends('layouts.app')
@section('title','Kirim Pesan')
@section('content')
<div class="max-w-3xl mx-auto" x-data="{ type:'text' }">
    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-slate-900">Kirim Pesan WhatsApp</h2>
        <p class="text-sm text-slate-500">Sisa kuota bulan ini: <b class="text-brand-600">{{ number_format(auth()->user()->remainingQuota()) }}</b> pesan</p>
    </div>

    @if($devices->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center">
            <div class="text-4xl mb-3">📱</div>
            <p class="text-slate-600 mb-4">Anda belum punya device. Tambah & hubungkan device terlebih dulu.</p>
            <a href="{{ route('devices.index') }}" class="px-4 py-2 rounded-lg bg-brand-600 text-white text-sm font-semibold">Tambah Device</a>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <form method="POST" action="{{ route('send.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Device</label>
                    <select name="device_id" required class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none">
                        @foreach($devices as $d)
                            <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->backend }}) — {{ $d->statusLabel() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor Tujuan</label>
                    <input name="to" value="{{ old('to') }}" required placeholder="628123456789 atau 628xxx@c.us" class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none">
                    <p class="text-xs text-slate-400 mt-1">Format internasional tanpa "+" (mis. 62 untuk Indonesia).</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tipe Pesan</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['text'=>'Teks','image'=>'Gambar','video'=>'Video','document'=>'Dokumen'] as $val=>$lbl)
                            <button type="button" @click="type='{{ $val }}'" :class="type==='{{ $val }}' ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-slate-600 border-slate-300'" class="px-4 py-2 rounded-lg border-[1.5px] text-sm font-semibold transition">{{ $lbl }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="type" :value="type">
                </div>

                <div x-show="type!=='text'" x-cloak>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">URL Media</label>
                    <input name="media_url" type="url" value="{{ old('media_url') }}" placeholder="https://contoh.com/file.jpg" class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5"><span x-text="type==='text' ? 'Isi Pesan' : 'Caption (opsional)'"></span></label>
                    <textarea name="body" rows="4" class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none resize-none" placeholder="Tulis pesan…">{{ old('body') }}</textarea>
                </div>

                <button class="w-full py-3 rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 text-white font-semibold shadow-lg shadow-brand-600/25 hover:-translate-y-0.5 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Kirim Pesan
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
