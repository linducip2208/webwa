@extends('layouts.app')
@section('title','Kelola Device')
@push('head')<meta name="csrf-token" content="{{ csrf_token() }}">@endpush
@section('content')
<div class="max-w-5xl mx-auto" x-data="deviceManager({
        id: {{ $device->id }},
        backend: '{{ $device->backend }}',
        status: '{{ $device->status }}',
        connectUrl: '{{ route('devices.connect',$device) }}',
        stateUrl: '{{ route('devices.state',$device) }}',
        disconnectUrl: '{{ route('devices.disconnect',$device) }}',
        resetUrl: '{{ route('devices.reset',$device) }}'
    })">
    <a href="{{ route('devices.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-brand-600 mb-4"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg> Kembali ke Device</a>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Connect / QR panel --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">{{ $device->name }}</h2>
                    <p class="text-sm text-slate-400">Session: <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">{{ $device->session_name }}</code></p>
                </div>
                <span class="text-[10px] uppercase px-2.5 py-1 rounded bg-slate-100 text-slate-500 font-bold">{{ $device->backend }}</span>
            </div>

            @if($device->isCloud())
                <div class="rounded-xl bg-sky-50 border border-sky-200 p-5 text-sm text-sky-800">
                    <p class="font-semibold mb-1">Device Cloud API (Meta)</p>
                    <p>Device ini memakai Meta Cloud API. Set kredensial <code>WHATSAPP_ACCESS_TOKEN</code> & <code>WHATSAPP_PHONE_NUMBER_ID</code> di <code>.env</code>, lalu kirim pesan via API. Tidak perlu QR.</p>
                </div>
            @else
                <div class="flex flex-col items-center text-center py-4">
                    {{-- Status badge --}}
                    <div class="flex items-center gap-2 mb-5">
                        <span class="w-2.5 h-2.5 rounded-full" :class="{
                            'bg-emerald-500': connected,
                            'bg-amber-500 animate-pulse': ['qr','connecting','authenticated'].includes(status),
                            'bg-red-500': ['error','auth_failure'].includes(status),
                            'bg-slate-400': status==='disconnected'
                        }"></span>
                        <span class="text-sm font-semibold" x-text="statusLabel()"></span>
                    </div>

                    {{-- Connected state --}}
                    <template x-if="connected">
                        <div class="py-8">
                            <div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p class="text-lg font-bold text-slate-900">WhatsApp Terhubung 🎉</p>
                            <p class="text-sm text-slate-500 mt-1" x-text="phone ? ('Nomor: '+phone) : 'Siap mengirim pesan.'"></p>
                            <a href="{{ route('send.create') }}" class="inline-block mt-5 px-5 py-2.5 rounded-lg bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700">Kirim Pesan →</a>
                        </div>
                    </template>

                    {{-- QR state --}}
                    <template x-if="!connected && qr">
                        <div>
                            <div class="inline-block p-4 bg-white border-2 border-slate-200 rounded-2xl">
                                <img :src="qr" alt="QR Code" class="w-56 h-56">
                            </div>
                            <p class="text-sm text-slate-600 mt-4 max-w-xs">Buka <b>WhatsApp</b> → <b>Perangkat Tertaut</b> → <b>Tautkan Perangkat</b>, lalu pindai QR ini.</p>
                            <p class="text-xs text-slate-400 mt-2">QR diperbarui otomatis…</p>
                        </div>
                    </template>

                    {{-- Disconnected / start state --}}
                    <template x-if="!connected && !qr">
                        <div class="py-8">
                            <div class="w-20 h-20 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-slate-600 mb-1" x-text="loading ? 'Menyiapkan sesi…' : 'Device belum terhubung.'"></p>
                            <p x-show="error" x-cloak class="text-sm text-red-600 max-w-sm mx-auto mt-2" x-text="error"></p>
                            <button @click="connect()" :disabled="loading" class="mt-5 px-6 py-2.5 rounded-lg bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 disabled:opacity-50">
                                <span x-show="!loading">Hubungkan (Tampilkan QR)</span>
                                <span x-show="loading" x-cloak>Memuat…</span>
                            </button>
                        </div>
                    </template>

                    {{-- Controls --}}
                    <div class="flex gap-3 mt-6" x-show="connected || qr" x-cloak>
                        <button @click="disconnect()" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Putuskan</button>
                        <button @click="reset()" class="px-4 py-2 rounded-lg bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100">Reset & QR Baru</button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Settings --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <h3 class="font-bold text-slate-900 mb-4">Pengaturan</h3>
                <form method="POST" action="{{ route('devices.update',$device) }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama</label>
                        <input name="name" value="{{ $device->name }}" class="w-full px-3.5 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Webhook URL</label>
                        <input name="webhook_url" type="url" value="{{ $device->webhook_url }}" placeholder="https://…/webhook" class="w-full px-3.5 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none text-sm">
                        <p class="text-xs text-slate-400 mt-1">URL untuk menerima pesan masuk.</p>
                    </div>
                    <button class="w-full py-2.5 rounded-lg bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Simpan</button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-red-200 p-6">
                <h3 class="font-bold text-red-600 mb-2">Zona Bahaya</h3>
                <p class="text-sm text-slate-500 mb-4">Menghapus device akan memutus sesi WhatsApp secara permanen.</p>
                <form method="POST" action="{{ route('devices.destroy',$device) }}" onsubmit="return confirm('Hapus device ini?')">
                    @csrf @method('DELETE')
                    <button class="w-full py-2.5 rounded-lg bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100">Hapus Device</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deviceManager(cfg){
    return {
        ...cfg,
        qr: null,
        phone: @json($device->phone),
        loading: false,
        error: null,
        connected: {{ $device->isConnected() ? 'true':'false' }},
        timer: null,
        csrf: document.querySelector('meta[name=csrf-token]').content,
        init(){ if(this.backend==='web' && !this.connected){ this.poll(); this.timer=setInterval(()=>this.poll(), 3000);} },
        statusLabel(){
            return {ready:'Terhubung',connected:'Terhubung',authenticated:'Terautentikasi',qr:'Menunggu Scan QR',connecting:'Menghubungkan…',auth_failure:'Gagal Autentikasi',error:'Error'}[this.status] || 'Terputus';
        },
        async connect(){
            this.loading=true; this.error=null;
            try{
                const r = await fetch(this.connectUrl,{method:'POST',headers:{'X-CSRF-TOKEN':this.csrf,'Accept':'application/json'}});
                const d = await r.json();
                if(!d.ok){ this.error=d.message||'Gagal menghubungkan.'; this.loading=false; return; }
                this.status=d.status; this.qr=d.qr;
                if(!this.timer) this.timer=setInterval(()=>this.poll(),3000);
            }catch(e){ this.error='Tidak dapat menjangkau server.'; }
            this.loading=false;
        },
        async poll(){
            try{
                const r = await fetch(this.stateUrl,{headers:{'Accept':'application/json'}});
                const d = await r.json();
                if(d.offline){ return; }
                this.status=d.status; this.connected=d.connected;
                if(d.qr) this.qr=d.qr;
                if(this.connected){ this.qr=null; clearInterval(this.timer); this.timer=null; }
            }catch(e){}
        },
        async disconnect(){
            await fetch(this.disconnectUrl,{method:'POST',headers:{'X-CSRF-TOKEN':this.csrf}});
            this.connected=false; this.qr=null; this.status='disconnected';
            clearInterval(this.timer); this.timer=null;
        },
        async reset(){
            if(!confirm('Reset sesi & buat QR baru?')) return;
            await fetch(this.resetUrl,{method:'POST',headers:{'X-CSRF-TOKEN':this.csrf}});
            this.connected=false; this.qr=null; this.status='disconnected';
            this.connect();
        }
    }
}
</script>
@endpush
@endsection
