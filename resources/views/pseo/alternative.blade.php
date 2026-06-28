@extends('layouts.public')
@section('title','Alternatif '.$competitorName.' Terbaik — WebWA WhatsApp Gateway')
@section('meta_description','Mencari alternatif '.$competitorName.'? WebWA menawarkan WhatsApp gateway dual-backend, REST API sederhana, multi-device, dan harga transparan. Mulai gratis.')
@section('content')
<section class="pt-32 pb-12 grid-bg">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <span class="inline-block px-3 py-1 rounded-full bg-brand-100 text-brand-700 text-sm font-semibold mb-4">Alternatif {{ $competitorName }}</span>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 mb-5">Alternatif {{ $competitorName }} yang Lebih Fleksibel</h1>
        <p class="text-lg text-slate-600 mb-7">WebWA adalah pilihan WhatsApp gateway dengan dual-backend, REST API yang mudah, dan harga transparan — alternatif modern untuk {{ $competitorName }}.</p>
        <a href="{{ route('register') }}" class="px-6 py-3.5 rounded-xl bg-brand-600 text-white font-semibold shadow-xl shadow-brand-600/25 hover:-translate-y-0.5 transition inline-block">Pindah ke WebWA Gratis</a>
    </div>
</section>

<section class="max-w-3xl mx-auto px-6 py-12 prose prose-slate">
    <p>Banyak pengguna mencari <strong>alternatif {{ $competitorName }}</strong> karena membutuhkan fleksibilitas lebih, integrasi yang lebih mudah, atau harga yang lebih sesuai. <strong>WebWA</strong> dirancang untuk menjawab kebutuhan tersebut dengan pendekatan dual-backend: Anda bisa memakai nomor WhatsApp pribadi (via pemindaian QR dengan whatsapp-web.js) untuk fleksibilitas penuh, atau Cloud API Meta resmi untuk volume besar tanpa risiko pemblokiran.</p>
    <h2>Kenapa pindah ke WebWA?</h2>
    <ul>
        <li><strong>REST API ringkas</strong> — satu endpoint untuk teks dan media, autentikasi Bearer token.</li>
        <li><strong>Multi-device</strong> dalam satu dashboard terpusat.</li>
        <li><strong>Webhook</strong> untuk menerima & membalas pesan masuk otomatis.</li>
        <li><strong>Log lengkap</strong> dengan status terkirim/gagal/dibaca.</li>
        <li><strong>Mulai gratis</strong> tanpa kartu kredit, upgrade sesuai kebutuhan.</li>
    </ul>
    <p>Migrasi dari {{ $competitorName }} ke WebWA tidak rumit. Cukup daftar, hubungkan device WhatsApp Anda, buat API key, dan perbarui endpoint di sistem Anda. Tim Anda akan langsung merasakan dashboard yang lebih bersih dan dokumentasi yang lebih jelas.</p>
</section>

@include('pseo._cta')
@endsection
