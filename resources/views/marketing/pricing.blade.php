@extends('layouts.public')
@section('title','Harga — WebWA WhatsApp Gateway')
@section('meta_description','Paket harga WebWA: Free, Growth, dan Enterprise. Mulai gratis 1 device & 1.000 pesan/bulan. Upgrade kapan saja.')
@section('content')
<section class="pt-32 pb-16 text-center grid-bg">
    <div class="max-w-3xl mx-auto px-6">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 mb-4">Harga sederhana, tanpa kejutan</h1>
        <p class="text-lg text-slate-600">Bayar sesuai skala bisnis Anda. Semua paket termasuk REST API & webhook.</p>
    </div>
</section>

<section class="pb-20 max-w-6xl mx-auto px-6">
    <div class="grid md:grid-cols-3 gap-6">
        @php
            $plans = [
                ['Free','0',['1 device WhatsApp','1.000 pesan / bulan','REST API + webhook','Log 7 hari','Community support'],false],
                ['Growth','149.000',['5 device WhatsApp','50.000 pesan / bulan','Webhook prioritas','Log 90 hari','Email support','Cloud API ready'],true],
                ['Enterprise','Hubungi kami',['Unlimited device','Pesan unlimited','Cloud API Meta resmi','Whitelabel & on-premise','SLA 99.9% + dedicated','Priority 24/7'],false],
            ];
        @endphp
        @foreach($plans as $p)
            @php $pop = $p[3]; @endphp
            <div class="reveal rounded-2xl p-8 card-lift {{ $pop ? 'bg-slate-900 text-white ring-2 ring-brand-500' : 'bg-white border border-slate-200' }}">
                @if($pop)<span class="inline-block px-3 py-1 rounded-full bg-brand-500 text-white text-xs font-bold mb-4">PALING POPULER</span>@endif
                <h3 class="text-xl font-bold {{ $pop?'text-white':'text-slate-900' }}">{{ $p[0] }}</h3>
                <p class="text-4xl font-extrabold my-4 {{ $pop?'text-white':'text-slate-900' }}">{{ Str::startsWith($p[1],'Hubungi') ? $p[1] : 'Rp'.$p[1] }}<span class="text-sm font-normal text-slate-400">{{ Str::startsWith($p[1],'Hubungi') ? '' : '/bln' }}</span></p>
                <ul class="space-y-3 my-6">
                    @foreach($p[2] as $feat)
                        <li class="flex items-center gap-2.5 text-sm {{ $pop?'text-slate-200':'text-slate-600' }}"><svg class="w-5 h-5 text-brand-{{ $pop?'400':'500' }} shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>{{ $feat }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="block text-center py-3 rounded-xl font-semibold {{ $pop?'bg-brand-500 text-white hover:bg-brand-400':'bg-slate-900 text-white hover:bg-slate-800' }} transition">{{ Str::startsWith($p[1],'Hubungi') ? 'Hubungi Sales' : 'Pilih '.$p[0] }}</a>
            </div>
        @endforeach
    </div>

    <div class="mt-16 max-w-3xl mx-auto">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-6 text-center">Pertanyaan Umum</h2>
        <div class="space-y-3" x-data="{ open:0 }">
            @foreach([
                ['Apakah benar-benar gratis?','Ya. Paket Free gratis selamanya untuk 1 device dan 1.000 pesan per bulan. Tanpa kartu kredit.'],
                ['Apa beda backend Web dan Cloud API?','Web (whatsapp-web.js) memakai nomor pribadi via QR — fleksibel, bisa kirim ke grup & pesan bebas. Cloud API (Meta resmi) cocok untuk skala besar tanpa risiko ban, butuh akun WhatsApp Business.'],
                ['Apakah ada risiko nomor diblokir?','Backend Web adalah otomasi browser (area abu-abu ToS). Gunakan wajar & hindari spam. Untuk volume besar, pakai Cloud API resmi.'],
                ['Bisa upgrade/downgrade kapan saja?','Bisa. Perubahan paket berlaku langsung dan kuota disesuaikan.'],
            ] as $i=>$faq)
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                    <button @click="open = open==={{ $i }} ? null : {{ $i }}" class="w-full flex items-center justify-between px-5 py-4 text-left font-semibold text-slate-800">{{ $faq[0] }}<span x-text="open==={{ $i }} ? '−' : '+'" class="text-brand-600 text-xl"></span></button>
                    <div x-show="open==={{ $i }}" x-cloak class="px-5 pb-4 text-sm text-slate-600">{{ $faq[1] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
