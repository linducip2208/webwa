@extends('layouts.public')
@section('title','Dokumentasi API — WebWA WhatsApp Gateway')
@section('meta_description','Dokumentasi lengkap WebWA: endpoint REST API, autentikasi API key, contoh cURL, tutorial pairing QR, dan akun demo.')
@section('content')
<section class="pt-28 pb-10 grid-bg">
    <div class="max-w-5xl mx-auto px-6">
        <span class="text-brand-600 font-semibold text-sm">DOKUMENTASI</span>
        <h1 class="text-4xl font-extrabold text-slate-900 mt-2 mb-3">Dokumentasi WebWA API</h1>
        <p class="text-lg text-slate-600 max-w-2xl">Panduan integrasi gateway WhatsApp: pairing, autentikasi, dan pengiriman pesan via REST API.</p>
    </div>
</section>

{{-- Jump nav --}}
<div class="sticky top-16 z-30 bg-white/90 backdrop-blur border-y border-slate-100">
    <div class="max-w-5xl mx-auto px-6 flex gap-5 overflow-x-auto text-sm py-3 font-medium text-slate-600">
        <a href="#akun" class="hover:text-brand-600 whitespace-nowrap">Akun Demo</a>
        <a href="#mulai" class="hover:text-brand-600 whitespace-nowrap">Mulai Cepat</a>
        <a href="#auth" class="hover:text-brand-600 whitespace-nowrap">Autentikasi</a>
        <a href="#endpoint" class="hover:text-brand-600 whitespace-nowrap">Endpoint</a>
        <a href="#tutorial" class="hover:text-brand-600 whitespace-nowrap">Tutorial</a>
        <a href="#tampilan" class="hover:text-brand-600 whitespace-nowrap">Tampilan</a>
    </div>
</div>

<div class="max-w-5xl mx-auto px-6 py-12 space-y-16">
    {{-- Demo accounts --}}
    <section id="akun">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">Akun Demo</h2>
        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase"><tr><th class="text-left px-5 py-3 font-semibold">Role</th><th class="text-left px-5 py-3 font-semibold">Email</th><th class="text-left px-5 py-3 font-semibold">Password</th><th class="text-left px-5 py-3 font-semibold">Akses</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($demoAccounts as $a)
                        <tr><td class="px-5 py-3 font-semibold text-slate-800">{{ $a['role'] }}</td><td class="px-5 py-3 font-mono text-brand-600">{{ $a['email'] }}</td><td class="px-5 py-3 font-mono text-slate-600">{{ $a['password'] }}</td><td class="px-5 py-3 text-slate-500">{{ $a['scope'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a href="{{ route('login') }}" class="inline-block mt-4 px-5 py-2.5 rounded-lg bg-brand-600 text-white text-sm font-semibold">Masuk ke Dashboard →</a>
    </section>

    {{-- Quick start --}}
    <section id="mulai">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">Mulai Cepat</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([['1','Daftar / login','Buat akun di /register.'],['2','Tambah device','Pilih backend Web atau Cloud.'],['3','Scan QR','Tautkan WhatsApp Anda.'],['4','Buat API key','Gunakan untuk kirim via API.']] as $s)
                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <div class="w-8 h-8 rounded-lg bg-brand-600 text-white font-bold flex items-center justify-center mb-3">{{ $s[0] }}</div>
                    <h3 class="font-bold text-slate-800 text-sm">{{ $s[1] }}</h3><p class="text-xs text-slate-500 mt-1">{{ $s[2] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Auth --}}
    <section id="auth">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">Autentikasi</h2>
        <p class="text-slate-600 mb-4">Setiap request API harus menyertakan API key Anda. Dua cara yang didukung:</p>
        <div class="bg-slate-900 rounded-2xl p-6 text-slate-200 text-sm space-y-3">
            <p class="text-slate-400"># Header Bearer token (disarankan)</p>
            <code class="block">Authorization: Bearer wwa_xxxxxxxx.yyyyyyyyyyyy</code>
            <p class="text-slate-400 mt-3"># atau header khusus</p>
            <code class="block">X-Api-Key: wwa_xxxxxxxx.yyyyyyyyyyyy</code>
        </div>
        <p class="text-sm text-slate-500 mt-3">Base URL: <code class="bg-slate-100 px-2 py-0.5 rounded">{{ $baseUrl }}/api/v1</code></p>
    </section>

    {{-- Endpoints --}}
    <section id="endpoint">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">Endpoint API</h2>
        <div class="space-y-6">
            @foreach($endpoints as $ep)
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
                        <span class="text-xs font-bold px-2.5 py-1 rounded {{ $ep['method']==='GET' ? 'bg-sky-100 text-sky-700':'bg-brand-100 text-brand-700' }}">{{ $ep['method'] }}</span>
                        <code class="text-sm font-mono text-slate-800">{{ $ep['path'] }}</code>
                        <span class="ml-auto text-sm text-slate-400">{{ $ep['title'] }}</span>
                    </div>
                    <div class="p-5 space-y-4">
                        @if($ep['body'])
                            <div><p class="text-xs font-semibold text-slate-500 uppercase mb-2">Request Body</p><pre class="bg-slate-50 border border-slate-100 rounded-lg p-4 text-xs overflow-x-auto"><code>{{ $ep['body'] }}</code></pre></div>
                        @endif
                        <div><p class="text-xs font-semibold text-slate-500 uppercase mb-2">cURL</p><pre class="bg-slate-900 text-slate-200 rounded-lg p-4 text-xs overflow-x-auto"><code>{{ $ep['curl'] }}</code></pre></div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Tutorial --}}
    <section id="tutorial">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">Tutorial Lengkap</h2>
        <div class="space-y-6">
            @foreach($tutorial as $phase)
                <div class="bg-white border border-slate-200 rounded-2xl p-6">
                    <h3 class="font-bold text-brand-700 mb-3">{{ $phase['phase'] }}</h3>
                    <ol class="space-y-2">
                        @foreach($phase['steps'] as $n=>$step)
                            <li class="flex gap-3 text-sm text-slate-600"><span class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 text-xs font-bold flex items-center justify-center shrink-0">{{ $n+1 }}</span>{{ $step }}</li>
                        @endforeach
                    </ol>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Screenshots --}}
    <section id="tampilan">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">Tampilan Aplikasi</h2>
        <div class="grid sm:grid-cols-2 gap-6">
            @foreach([
                ['dashboard.png','Dashboard','Statistik device, pesan, dan kuota dalam satu layar.'],
                ['device-detail.png','Pairing QR Device','Hubungkan WhatsApp dengan scan QR, status realtime.'],
                ['send.png','Kirim Pesan','Kirim teks & media ke nomor tujuan langsung dari panel.'],
                ['api-keys.png','API Key','Buat & kelola API key untuk integrasi REST.'],
                ['logs.png','Log Pesan','Riwayat pengiriman lengkap dengan status & filter.'],
                ['admin.png','Panel Admin','Pantau seluruh user, device, dan log secara global.'],
            ] as $sc)
                <figure class="rounded-2xl border border-slate-200 overflow-hidden shadow-sm bg-white">
                    <div class="flex items-center gap-1 px-3 py-2 bg-slate-100 border-b border-slate-200"><span class="w-2 h-2 rounded-full bg-red-400"></span><span class="w-2 h-2 rounded-full bg-amber-400"></span><span class="w-2 h-2 rounded-full bg-green-400"></span></div>
                    <img src="{{ asset('marketing/screens/'.$sc[0]) }}" alt="{{ $sc[1] }} WebWA" loading="lazy" class="w-full block">
                    <figcaption class="px-4 py-3 border-t border-slate-100"><p class="text-sm font-bold text-slate-800">{{ $sc[1] }}</p><p class="text-xs text-slate-500">{{ $sc[2] }}</p></figcaption>
                </figure>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 p-8 text-center text-white">
        <h2 class="text-2xl font-extrabold mb-2">Siap mulai integrasi?</h2>
        <p class="text-brand-50 mb-5">Buat API key dan kirim pesan pertama Anda sekarang.</p>
        <a href="{{ route('register') }}" class="inline-block px-6 py-3 rounded-xl bg-white text-brand-700 font-semibold hover:bg-brand-50">Daftar Gratis</a>
    </section>
</div>
@endsection
