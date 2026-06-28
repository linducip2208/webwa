@extends('layouts.public')
@section('title','WhatsApp Gateway '.$cityName.' — API Notifikasi & OTP | WebWA')
@section('meta_description','WebWA: layanan WhatsApp gateway untuk bisnis di '.$cityName.'. Kirim notifikasi, OTP, & blast via REST API. Multi-device, harga terjangkau, mulai gratis.')
@push('schema')
@php($citySchema = ['@context'=>'https://schema.org','@type'=>'Service','serviceType'=>'WhatsApp Gateway API','areaServed'=>$cityName,'provider'=>['@type'=>'Organization','name'=>'WebWA']])
<script type="application/ld+json">{!! json_encode($citySchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
@section('content')
<section class="pt-32 pb-12 grid-bg">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <span class="inline-block px-3 py-1 rounded-full bg-brand-100 text-brand-700 text-sm font-semibold mb-4">📍 {{ $cityName }}</span>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 mb-5">WhatsApp Gateway {{ $cityName }}</h1>
        <p class="text-lg text-slate-600 mb-7">Solusi notifikasi WhatsApp untuk bisnis di {{ $cityName }} — kirim OTP, invoice, resi, dan promo otomatis lewat REST API.</p>
        <a href="{{ route('register') }}" class="px-6 py-3.5 rounded-xl bg-brand-600 text-white font-semibold shadow-xl shadow-brand-600/25 hover:-translate-y-0.5 transition inline-block">Coba Gratis di {{ $cityName }}</a>
    </div>
</section>

<section class="max-w-3xl mx-auto px-6 py-12 prose prose-slate">
    <p>Bisnis di <strong>{{ $cityName }}</strong> kini semakin mengandalkan WhatsApp sebagai kanal komunikasi utama dengan pelanggan. Mulai dari toko online, restoran, klinik, hingga lembaga pendidikan — semuanya membutuhkan cara cepat dan terukur untuk mengirim pesan dalam jumlah besar. <strong>WebWA</strong> hadir sebagai gateway WhatsApp yang memungkinkan Anda mengirim notifikasi, OTP, dan blast langsung dari sistem atau aplikasi yang sudah Anda miliki melalui REST API yang sederhana.</p>
    <p>Dengan WebWA, pelaku usaha di {{ $cityName }} tidak perlu lagi mengirim pesan satu per satu secara manual. Cukup hubungkan nomor WhatsApp Anda lewat pemindaian QR (backend Web) atau gunakan Cloud API Meta resmi untuk skala besar tanpa risiko pemblokiran. Setiap pesan tercatat lengkap statusnya — terkirim, gagal, atau dibaca — sehingga Anda selalu tahu kondisi pengiriman secara real-time.</p>
    <h2>Mengapa bisnis {{ $cityName }} memilih WebWA?</h2>
    <ul>
        <li><strong>Multi-device</strong> — kelola banyak nomor WhatsApp dalam satu dashboard.</li>
        <li><strong>REST API & webhook</strong> — integrasi mudah ke website, aplikasi, atau ERP Anda.</li>
        <li><strong>Dual-backend</strong> — pilih nomor pribadi (fleksibel) atau Cloud API resmi (skalabel).</li>
        <li><strong>Harga terjangkau</strong> — mulai gratis, upgrade sesuai pertumbuhan bisnis.</li>
        <li><strong>Log & monitoring</strong> — audit lengkap setiap pesan yang dikirim.</li>
    </ul>
    <p>Baik Anda menjalankan UMKM kecil maupun perusahaan besar di {{ $cityName }}, WebWA memberikan fondasi otomasi WhatsApp yang andal, aman, dan mudah dikembangkan. Mulai gratis hari ini dan rasakan efisiensi komunikasi pelanggan yang lebih baik.</p>
</section>

@include('pseo._cta')
@endsection
