@extends('layouts.app')
@section('title','Admin · Pengaturan Daemon')
@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Peringatan scheduler --}}
    @unless($schedulerHealthy)
        <div class="flex items-start gap-3 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800">
            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <div class="text-sm">
                <p class="font-semibold">Scheduler belum terdeteksi berjalan.</p>
                <p>Auto-reply tidak akan pulih otomatis sampai <code class="font-mono bg-amber-100 px-1 rounded">schedule:work</code> (Supervisor) atau cron <code class="font-mono bg-amber-100 px-1 rounded">schedule:run</code> dipasang di server. Lihat <a href="#deploy" class="underline font-semibold">Config Deploy</a> di bawah.</p>
            </div>
        </div>
    @endunless

    {{-- Status cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $statusCard = function (string $label, string $value, bool $ok, ?string $hint = null) {
                $tone = $ok ? 'emerald' : 'red';
                return [$label, $value, $tone, $hint];
            };
            $cards = [
                $statusCard('Daemon Auto-reply', $enabled ? 'AKTIF' : 'NONAKTIF', $enabled),
                $statusCard('Scheduler', $schedulerHealthy ? 'Jalan' : 'Tidak jalan', $schedulerHealthy, $lastRun ? 'Heartbeat '.$lastRun->diffForHumans() : 'Belum ada heartbeat'),
                $statusCard('Sidecar Node', $sidecarUp ? 'Online' : 'Offline', $sidecarUp, 'WhatsApp Web engine'),
                $statusCard('Persist Incoming', $persistIncoming ? 'true' : 'false', $persistIncoming, $persistIncoming ? null : 'Wajib true untuk auto-reply'),
            ];
        @endphp
        @foreach($cards as [$label,$value,$tone,$hint])
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-{{ $tone }}-500"></span>
                    <p class="text-lg font-extrabold text-{{ $tone }}-600">{{ $value }}</p>
                </div>
                <p class="text-sm text-slate-600 font-medium mt-1">{{ $label }}</p>
                @if($hint)<p class="text-xs text-slate-400 mt-0.5">{{ $hint }}</p>@endif
            </div>
        @endforeach
    </div>

    {{-- Toggle + penjelasan manual/otomatis --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="font-bold text-slate-900 mb-1">Aktifkan Auto-reply Daemon</h3>
            <p class="text-sm text-slate-500 mb-4">Saat OFF, watchdog berhenti membangkitkan sesi & listener dihentikan pada sinkron berikutnya.</p>
            <form method="POST" action="{{ route('admin.settings.update') }}" x-data="{ on: {{ $enabled ? 'true' : 'false' }} }">
                @csrf @method('PUT')
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="enabled" value="1" x-model="on" class="sr-only">
                    <span @click="on=!on" :class="on ? 'bg-brand-600' : 'bg-slate-300'" class="relative w-12 h-7 rounded-full transition">
                        <span :class="on ? 'translate-x-5' : 'translate-x-0.5'" class="absolute top-0.5 left-0 w-6 h-6 bg-white rounded-full shadow transition-transform"></span>
                    </span>
                    <span class="text-sm font-semibold text-slate-700" x-text="on ? 'Aktif' : 'Nonaktif'"></span>
                </label>
                <button type="submit" class="mt-5 px-4 py-2 rounded-lg bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition">Simpan</button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="font-bold text-slate-900 mb-2">Manual atau Otomatis?</h3>
            <div class="text-sm text-slate-600 space-y-2">
                <p><span class="font-semibold text-slate-800">Otomatis</span> — Laravel Scheduler menjalankan <code class="font-mono text-xs bg-slate-100 px-1 rounded">whatsapp:devices:ensure</code> + <code class="font-mono text-xs bg-slate-100 px-1 rounded">whatsapp:listen:all</code> tiap menit. Di server cukup pasang <span class="font-semibold">SATU</span> kali: Supervisor <code class="font-mono text-xs bg-slate-100 px-1 rounded">schedule:work</code> atau cron <code class="font-mono text-xs bg-slate-100 px-1 rounded">schedule:run</code> (lihat di bawah).</p>
                <p><span class="font-semibold text-slate-800">Manual</span> — tombol di bawah menjalankan sekali jalan dari panel ini.</p>
            </div>
            <div class="flex flex-wrap gap-2 mt-4">
                <form method="POST" action="{{ route('admin.settings.run-ensure') }}">@csrf
                    <button class="px-3.5 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900 transition">Jalankan Watchdog</button>
                </form>
                <form method="POST" action="{{ route('admin.settings.run-listen') }}">@csrf
                    <button class="px-3.5 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition border border-slate-200">Sinkron Listener</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Status per device --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="font-bold text-slate-900 mb-4">Status Listener per Device</h3>
        @if($devices->isEmpty())
            <p class="text-sm text-slate-500">Belum ada device Web. Hubungkan device dulu di menu Device.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-400 border-b border-slate-100">
                            <th class="py-2 pr-4 font-semibold">Device</th>
                            <th class="py-2 pr-4 font-semibold">Pemilik</th>
                            <th class="py-2 pr-4 font-semibold">Session</th>
                            <th class="py-2 pr-4 font-semibold">Status</th>
                            <th class="py-2 pr-4 font-semibold">Listener</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($devices as $row)
                            @php $d = $row['device']; @endphp
                            <tr>
                                <td class="py-2.5 pr-4 font-medium text-slate-800">{{ $d->name }}</td>
                                <td class="py-2.5 pr-4 text-slate-500">{{ $d->user?->name ?? '—' }}</td>
                                <td class="py-2.5 pr-4 font-mono text-xs text-slate-500">{{ $d->session_name }}</td>
                                <td class="py-2.5 pr-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $d->statusColor() }}-100 text-{{ $d->statusColor() }}-700">{{ $d->statusLabel() }}</span>
                                </td>
                                <td class="py-2.5 pr-4">
                                    @if($row['listener'])
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hidup</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Mati</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Config deploy --}}
    <div id="deploy" class="bg-white rounded-2xl border border-slate-200 p-6" x-data="{ tab: '{{ $deploy['aapanel']['on'] ? 'aapanel' : 'linux' }}' }">
        <div class="mb-4">
            <h3 class="font-bold text-slate-900">Config Deploy (VPS)</h3>
            <p class="text-sm text-slate-500 mt-1">Path sudah disesuaikan dengan instalasi ini — copy-paste ke server produksi.</p>
        </div>

        <div class="inline-flex rounded-xl bg-slate-100 p-1 mb-5">
            <button type="button" @click="tab='linux'" :class="tab==='linux' ? 'bg-white shadow text-slate-900' : 'text-slate-500'" class="px-4 py-1.5 rounded-lg text-sm font-semibold transition">Linux / Supervisor</button>
            <button type="button" @click="tab='aapanel'" :class="tab==='aapanel' ? 'bg-white shadow text-slate-900' : 'text-slate-500'" class="px-4 py-1.5 rounded-lg text-sm font-semibold transition">aaPanel</button>
        </div>

        {{-- TAB: Linux / Supervisor --}}
        @php
            $linuxBlocks = [
                ['Supervisor — sidecar + scheduler', $deploy['linux']['supervisor'], 'Node sidecar (Chromium headless) + scheduler auto-restart 24/7.'],
                ['Pasang config Supervisor', $deploy['linux']['apply'], 'Setelah menempel config di atas.'],
                ['Cron (alternatif scheduler)', $deploy['linux']['cron'], 'Bila tak memakai program webwa-scheduler.'],
                ['.env produksi (potongan WhatsApp)', $deploy['linux']['env'], 'Sesuaikan di .env server.'],
            ];
        @endphp
        <div x-show="tab==='linux'" x-cloak class="space-y-6">
            @foreach($linuxBlocks as $b)
                <x-copy-block :title="$b[0]" :desc="$b[2]" :content="$b[1]" />
            @endforeach
        </div>

        {{-- TAB: aaPanel --}}
        <div x-show="tab==='aapanel'" x-cloak class="space-y-6">
            @if($deploy['aapanel']['on'])
                <div class="text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg p-3">Server aaPanel terdeteksi — path sudah otomatis benar. Sesuaikan hanya path <span class="font-semibold">Node</span> &amp; token.</div>
            @else
                <div class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">Digenerate dari mesin non-aaPanel — path memakai contoh <code class="font-mono">/www/wwwroot/&lt;domain&gt;</code>. Buka halaman ini <span class="font-semibold">dari server aaPanel</span> agar path otomatis benar.</div>
            @endif

            <div>
                <p class="font-semibold text-slate-800 text-sm mb-2">1. Prasyarat (Terminal/SSH) — sekali saja</p>
                <x-copy-block desc="Library Chromium + install sidecar + permission storage" :content="$deploy['aapanel']['predeps']" />
            </div>

            <div>
                <p class="font-semibold text-slate-800 text-sm mb-1">2. Aktifkan fungsi PHP</p>
                <p class="text-xs text-slate-500 mb-2">Software Store → PHP versi terkait → <span class="font-semibold">Disabled functions</span> → hapus fungsi berikut (dibutuhkan untuk spawn listener):</p>
                <x-copy-block :content="$deploy['aapanel']['disable_fn']" />
            </div>

            <div>
                <p class="font-semibold text-slate-800 text-sm mb-2">3. Supervisor Manager → tambah 2 daemon</p>
                <div class="space-y-3">
                    <div class="rounded-xl border border-slate-200 p-3 space-y-2">
                        <p class="text-xs font-semibold text-slate-700">Daemon A — <span class="text-brand-700">webwa-sidecar</span></p>
                        <p class="text-xs text-slate-500">Run user: <code class="font-mono">www</code> · Run dir: <code class="font-mono break-all">{{ $deploy['aapanel']['sidecar_dir'] }}</code></p>
                        <x-copy-block desc="Start command (ganti path Node bila perlu)" :content="$deploy['aapanel']['sidecar_cmd']" />
                    </div>
                    <div class="rounded-xl border border-slate-200 p-3 space-y-2">
                        <p class="text-xs font-semibold text-slate-700">Daemon B — <span class="text-brand-700">webwa-scheduler</span></p>
                        <p class="text-xs text-slate-500">Run user: <code class="font-mono">www</code> · Run dir: <code class="font-mono break-all">{{ $deploy['aapanel']['scheduler_dir'] }}</code></p>
                        <x-copy-block desc="Start command" :content="$deploy['aapanel']['scheduler_cmd']" />
                    </div>
                </div>
            </div>

            <div>
                <p class="font-semibold text-slate-800 text-sm mb-1">4. Alternatif scheduler — Cron bawaan aaPanel</p>
                <p class="text-xs text-slate-500 mb-2">计划任务/Cron → Shell Script → interval <span class="font-semibold">1 menit</span>. Pakai ini bila tak memakai daemon webwa-scheduler.</p>
                <x-copy-block :content="$deploy['aapanel']['cron']" />
            </div>

            <div>
                <p class="font-semibold text-slate-800 text-sm mb-2">5. .env produksi</p>
                <x-copy-block :content="$deploy['aapanel']['env']" />
            </div>
        </div>

        <div class="text-xs text-slate-500 border-t border-slate-100 pt-4 mt-6">
            Catatan: WhatsApp Web dijalankan oleh <span class="font-semibold">Chromium headless</span> — sebuah proses di <span class="font-semibold">mesin yang menjalankan sidecar</span> (VPS/aaPanel di produksi, atau PC ini bila lokal), BUKAN di tab browser. Menutup tab aman — auto-reply tetap jalan. Tapi bila <span class="font-semibold">mesin host-nya dimatikan</span>, semua berhenti; produksi wajib server 24/7 + Supervisor. HP juga harus online berkala (WhatsApp multi-device unlink bila HP offline &gt; ~14 hari).
        </div>
    </div>
</div>
@endsection
