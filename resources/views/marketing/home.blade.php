@extends('layouts.public')
@section('title','WebWA — WhatsApp Gateway API Multi-Device untuk Bisnis')
@section('meta_description','WebWA adalah gateway WhatsApp dengan REST API. Kirim notifikasi, OTP & blast via nomor pribadi (whatsapp-web.js) atau Cloud API Meta resmi. Multi-device, webhook, kuota fleksibel.')
@push('schema')
<script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org','@type'=>'SoftwareApplication','name'=>'WebWA','applicationCategory'=>'BusinessApplication','operatingSystem'=>'Web','offers'=>['@type'=>'Offer','price'=>'0','priceCurrency'=>'IDR'],'description'=>'Gateway WhatsApp multi-device dengan REST API untuk notifikasi, OTP, dan blast.','aggregateRating'=>['@type'=>'AggregateRating','ratingValue'=>'4.9','ratingCount'=>'218']], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
@section('content')

{{-- HERO --}}
<section class="relative pt-32 pb-20 grid-bg overflow-hidden">
    <div class="absolute top-20 -left-20 w-72 h-72 bg-brand-300/30 rounded-full blur-3xl animate-float-slow"></div>
    <div class="absolute bottom-0 -right-20 w-80 h-80 bg-brand-400/20 rounded-full blur-3xl"></div>
    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center relative">
        <div>
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-100 text-brand-700 text-sm font-semibold mb-6">
                <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span> Dual-backend · Web + Cloud API
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.1] mb-6">
                WhatsApp Gateway<br><span class="bg-gradient-to-r from-brand-600 to-brand-400 bg-clip-text text-transparent">untuk bisnis Anda.</span>
            </h1>
            <p class="text-lg text-slate-600 mb-8 max-w-lg">Kirim notifikasi, OTP, dan blast WhatsApp lewat REST API. Hubungkan nomor pribadi dengan QR atau pakai Cloud API Meta resmi — semua dalam satu dashboard.</p>
            <div class="flex flex-wrap gap-3 mb-8">
                <a href="{{ route('register') }}" class="px-6 py-3.5 rounded-xl bg-brand-600 text-white font-semibold shadow-xl shadow-brand-600/25 hover:-translate-y-0.5 transition">Coba Gratis Sekarang</a>
                <a href="{{ route('docs') }}" class="px-6 py-3.5 rounded-xl bg-white text-slate-700 font-semibold border border-slate-200 hover:border-brand-300 transition">Lihat Dokumentasi API</a>
            </div>
            <div class="flex items-center gap-6 text-sm text-slate-500">
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-brand-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Tanpa kartu kredit</span>
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-brand-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Setup 2 menit</span>
            </div>
        </div>
        {{-- Hero visual: API + chat mock --}}
        <div class="relative animate-scale-in">
            <div class="bg-slate-900 rounded-2xl shadow-2xl p-5 text-sm">
                <div class="flex items-center gap-1.5 mb-4"><span class="w-3 h-3 rounded-full bg-red-400"></span><span class="w-3 h-3 rounded-full bg-amber-400"></span><span class="w-3 h-3 rounded-full bg-green-400"></span><span class="ml-3 text-xs text-slate-500">POST /api/v1/messages/text</span></div>
                <pre class="text-xs leading-relaxed text-slate-300 overflow-x-auto"><code><span class="text-brand-400">curl</span> -X POST .../messages/text \
  -H <span class="text-amber-300">"Authorization: Bearer wwa_..."</span> \
  -d <span class="text-emerald-300">'{
    "device": "1",
    "to": "628123456789",
    "message": "Pesanan #1234 dikirim ✅"
  }'</span></code></pre>
                <div class="mt-4 flex items-center gap-2 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                    <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="text-emerald-300 text-xs font-mono">{ "success": true, "status": "sent" }</span>
                </div>
            </div>
            <div class="absolute -bottom-6 -left-6 bg-white rounded-xl shadow-xl p-4 w-48 hidden sm:block">
                <div class="flex items-center gap-2 mb-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span><span class="text-xs font-semibold text-slate-700">CS Utama</span></div>
                <div class="bg-brand-500 text-white text-xs rounded-lg rounded-bl-none p-2 ml-auto w-fit">Pesanan #1234 dikirim ✅</div>
            </div>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="border-y border-slate-100 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
        @foreach([['99.9%','Uptime gateway'],['2 detik','Latensi kirim'],['Multi','Device per akun'],['REST','API + Webhook']] as $s)
            <div class="reveal"><p class="text-3xl font-extrabold text-brand-600">{{ $s[0] }}</p><p class="text-sm text-slate-500 mt-1">{{ $s[1] }}</p></div>
        @endforeach
    </div>
</section>

{{-- TRUST / PERSONA --}}
<section class="py-20 max-w-7xl mx-auto px-6">
    <div class="text-center mb-12 reveal">
        <h2 class="text-3xl font-extrabold text-slate-900 mb-3">Cocok untuk siapa saja</h2>
        <p class="text-slate-500 max-w-2xl mx-auto">Dari toko online hingga enterprise — WebWA menyatu dengan alur bisnis Anda.</p>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach([['🛒','Toko Online','Notifikasi pesanan, resi, & promo otomatis'],['🏥','Klinik & RS','Reminder janji temu & hasil lab'],['🎓','Sekolah','Info SPP, pengumuman, & absensi'],['💼','SaaS & Startup','OTP login & notifikasi transaksional']] as $p)
            <div class="reveal bg-white rounded-2xl border border-slate-200 p-6 card-lift">
                <div class="text-3xl mb-3">{{ $p[0] }}</div>
                <h3 class="font-bold text-slate-900 mb-1">{{ $p[1] }}</h3>
                <p class="text-sm text-slate-500">{{ $p[2] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- PROBLEM / SOLUTION --}}
<section class="py-16 bg-slate-950 text-white">
    <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-2 gap-8 items-center">
        <div class="reveal">
            <h2 class="text-3xl font-extrabold mb-6">Berhenti kirim WhatsApp satu per satu.</h2>
            <div class="space-y-4">
                <div class="flex gap-3"><span class="text-red-400 text-xl">✕</span><p class="text-slate-300">Broadcast manual via HP — lambat, rawan ban, tidak terukur.</p></div>
                <div class="flex gap-3"><span class="text-red-400 text-xl">✕</span><p class="text-slate-300">Tidak ada log siapa terkirim, siapa gagal.</p></div>
                <div class="flex gap-3"><span class="text-red-400 text-xl">✕</span><p class="text-slate-300">Sulit integrasi ke sistem/aplikasi yang sudah ada.</p></div>
            </div>
        </div>
        <div class="reveal bg-gradient-to-br from-brand-600 to-brand-800 rounded-2xl p-8">
            <h3 class="text-xl font-bold mb-5">Dengan WebWA</h3>
            <div class="space-y-4">
                @foreach(['Kirim ribuan pesan via 1 endpoint REST API','Log lengkap status terkirim/gagal/dibaca','Multi-device & webhook pesan masuk','Pilih backend: nomor pribadi atau Cloud API resmi'] as $f)
                    <div class="flex gap-3"><span class="text-white text-xl">✓</span><p class="text-brand-50">{{ $f }}</p></div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- FEATURES with mock screenshots --}}
<section id="fitur" class="py-20 max-w-7xl mx-auto px-6 space-y-20">
    <div class="text-center reveal">
        <h2 class="text-3xl font-extrabold text-slate-900 mb-3">Fitur Lengkap</h2>
        <p class="text-slate-500 max-w-2xl mx-auto">Semua yang Anda butuhkan untuk otomasi WhatsApp di satu tempat.</p>
    </div>

    @php
        $features = [
            ['title'=>'Pairing QR Multi-Device','desc'=>'Hubungkan banyak nomor WhatsApp lewat scan QR. Sesi tetap tersimpan — reconnect tanpa scan ulang.','img'=>'device-detail.png','path'=>'devices/1','bullets'=>['Scan QR langsung dari dashboard','Status realtime (qr → ready)','Reconnect otomatis','Reset & ganti nomor kapan saja']],
            ['title'=>'REST API Sederhana','desc'=>'Kirim teks, gambar, video, dokumen lewat satu endpoint. Autentikasi pakai API key Bearer token.','img'=>'api-keys.png','path'=>'api-keys','bullets'=>['Endpoint /messages/text & /messages/media','Bearer token / X-Api-Key','Response JSON konsisten','Rate-limit & kuota per paket']],
            ['title'=>'Log & Monitoring','desc'=>'Pantau setiap pesan: terkirim, gagal, dibaca. Filter & cari berdasarkan nomor atau status.','img'=>'logs.png','path'=>'logs','bullets'=>['Status delivery realtime','Filter & pencarian','Sumber: API / dashboard / blast','Export & audit']],
        ];
    @endphp

    @foreach($features as $i => $f)
        <div class="grid lg:grid-cols-2 gap-10 items-center reveal {{ $i%2==1 ? 'lg:[direction:rtl]' : '' }}">
            <div class="lg:[direction:ltr]">
                <div class="rounded-2xl border border-slate-200 shadow-xl overflow-hidden bg-white">
                    <div class="flex items-center gap-1.5 px-4 py-3 bg-slate-100 border-b border-slate-200"><span class="w-3 h-3 rounded-full bg-red-400"></span><span class="w-3 h-3 rounded-full bg-amber-400"></span><span class="w-3 h-3 rounded-full bg-green-400"></span><span class="ml-2 text-xs text-slate-400">webwa.app/{{ $f['path'] }}</span></div>
                    <img src="{{ asset('marketing/screens/'.$f['img']) }}" alt="Tampilan {{ $f['title'] }} WebWA" loading="lazy" class="w-full block">
                </div>
            </div>
            <div class="lg:[direction:ltr]">
                <h3 class="text-2xl font-extrabold text-slate-900 mb-3">{{ $f['title'] }}</h3>
                <p class="text-slate-600 mb-5">{{ $f['desc'] }}</p>
                <ul class="space-y-2.5">
                    @foreach($f['bullets'] as $b)
                        <li class="flex items-center gap-2.5 text-slate-700"><svg class="w-5 h-5 text-brand-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>{{ $b }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endforeach
</section>

{{-- SCREENSHOT GALLERY --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-10 reveal">
            <h2 class="text-3xl font-extrabold text-slate-900 mb-3">Lihat WebWA dari dalam</h2>
            <p class="text-slate-500 max-w-2xl mx-auto">Dashboard bersih, device manager, kirim pesan, dan panel admin — semua tampilan asli aplikasi.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['dashboard.png','Dashboard'],
                ['devices.png','Kelola Device'],
                ['send.png','Kirim Pesan'],
                ['api-keys.png','API Key'],
                ['logs.png','Log Pesan'],
                ['admin.png','Panel Admin'],
            ] as $g)
                <figure class="reveal rounded-xl border border-slate-200 overflow-hidden shadow-sm card-lift bg-white">
                    <div class="flex items-center gap-1 px-3 py-2 bg-slate-100 border-b border-slate-200"><span class="w-2 h-2 rounded-full bg-red-400"></span><span class="w-2 h-2 rounded-full bg-amber-400"></span><span class="w-2 h-2 rounded-full bg-green-400"></span></div>
                    <img src="{{ asset('marketing/screens/'.$g[0]) }}" alt="{{ $g[1] }} WebWA" loading="lazy" class="w-full block">
                    <figcaption class="px-4 py-3 text-sm font-semibold text-slate-700 border-t border-slate-100">{{ $g[1] }}</figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>

{{-- PRICING TEASER --}}
<section class="py-20 bg-slate-50">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-12 reveal"><h2 class="text-3xl font-extrabold text-slate-900 mb-3">Harga transparan</h2><p class="text-slate-500">Mulai gratis, upgrade saat tumbuh.</p></div>
        <div class="grid md:grid-cols-3 gap-6">
            @php $plans = [['Free','0','1 device · 1.000 pesan/bln · REST API',false],['Growth','149rb','5 device · 50.000 pesan/bln · webhook · prioritas',true],['Enterprise','Custom','Unlimited device · Cloud API · whitelabel · SLA',false]]; @endphp
            @foreach($plans as $p)
                <div class="reveal rounded-2xl p-7 card-lift {{ $p[3] ? 'bg-slate-900 text-white ring-2 ring-brand-500' : 'bg-white border border-slate-200' }}">
                    @if($p[3])<span class="inline-block px-3 py-1 rounded-full bg-brand-500 text-white text-xs font-bold mb-3">POPULER</span>@endif
                    <h3 class="text-lg font-bold {{ $p[3]?'text-white':'text-slate-900' }}">{{ $p[0] }}</h3>
                    <p class="text-3xl font-extrabold my-3 {{ $p[3]?'text-white':'text-slate-900' }}">Rp{{ $p[1] }}<span class="text-sm font-normal text-slate-400">/bln</span></p>
                    <p class="text-sm {{ $p[3]?'text-slate-300':'text-slate-500' }} mb-5">{{ $p[2] }}</p>
                    <a href="{{ route('register') }}" class="block text-center py-2.5 rounded-lg font-semibold {{ $p[3]?'bg-brand-500 text-white hover:bg-brand-400':'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition">Pilih {{ $p[0] }}</a>
                </div>
            @endforeach
        </div>
        <p class="text-center mt-8"><a href="{{ route('pricing') }}" class="text-brand-600 font-semibold">Lihat detail harga →</a></p>
    </div>
</section>

{{-- DEMO ACCOUNTS --}}
<section class="py-16 max-w-4xl mx-auto px-6">
    <div class="reveal bg-gradient-to-br from-brand-600 to-brand-800 rounded-2xl p-8 text-white text-center">
        <h2 class="text-2xl font-extrabold mb-2">Coba langsung dengan akun demo</h2>
        <p class="text-brand-50 mb-6">Login dan jelajahi dashboard tanpa registrasi.</p>
        <div class="grid sm:grid-cols-3 gap-3 text-left text-sm font-mono">
            @foreach([['Admin','admin@webwa.test'],['User','user@webwa.test'],['Demo','demo@webwa.test']] as $a)
                <div class="bg-white/10 backdrop-blur rounded-xl p-4"><p class="font-bold text-white mb-1">{{ $a[0] }}</p><p class="text-brand-50 text-xs">{{ $a[1] }}</p><p class="text-brand-50 text-xs">password</p></div>
            @endforeach
        </div>
        <a href="{{ route('login') }}" class="inline-block mt-6 px-6 py-3 rounded-xl bg-white text-brand-700 font-semibold hover:bg-brand-50 transition">Masuk Sekarang</a>
    </div>
</section>

@if($posts->isNotEmpty())
{{-- BLOG --}}
<section class="py-16 max-w-7xl mx-auto px-6">
    <div class="flex items-center justify-between mb-8"><h2 class="text-2xl font-extrabold text-slate-900">Dari Blog</h2><a href="{{ route('blog.index') }}" class="text-brand-600 font-semibold text-sm">Semua artikel →</a></div>
    <div class="grid md:grid-cols-3 gap-6">
        @foreach($posts as $post)
            <a href="{{ route('blog.show',$post) }}" class="reveal bg-white rounded-2xl border border-slate-200 overflow-hidden card-lift">
                <div class="h-40 bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center text-4xl">📱</div>
                <div class="p-5"><h3 class="font-bold text-slate-900 mb-2 line-clamp-2">{{ $post->title }}</h3><p class="text-sm text-slate-500 line-clamp-2">{{ $post->excerpt }}</p></div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- FINAL CTA --}}
<section class="py-20 bg-slate-950">
    <div class="max-w-4xl mx-auto px-6 text-center reveal">
        <h2 class="text-4xl font-extrabold text-white mb-4">Siap otomasi WhatsApp Anda?</h2>
        <p class="text-slate-400 text-lg mb-8">Buat akun gratis hari ini. Hubungkan device, dapatkan API key, dan kirim pesan pertama dalam hitungan menit.</p>
        <div class="flex flex-wrap gap-3 justify-center">
            <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl bg-brand-600 text-white font-semibold shadow-xl shadow-brand-600/30 hover:-translate-y-0.5 transition">Mulai Gratis</a>
            <a href="{{ route('docs') }}" class="px-8 py-4 rounded-xl bg-white/10 text-white font-semibold hover:bg-white/20 transition">Baca Dokumentasi</a>
        </div>
    </div>
</section>
@endsection
