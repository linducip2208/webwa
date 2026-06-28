@extends('layouts.public')
@section('title','WhatsApp Gateway untuk '.$industryName.' — Otomasi Notifikasi | WebWA')
@section('meta_description','Gateway WhatsApp khusus '.$industryName.'. Otomatiskan notifikasi, reminder, & OTP via REST API. Multi-device, mulai gratis dengan WebWA.')
@section('content')
<section class="pt-32 pb-12 grid-bg">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <span class="inline-block px-3 py-1 rounded-full bg-brand-100 text-brand-700 text-sm font-semibold mb-4">🏢 {{ $industryName }}</span>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 mb-5">WhatsApp Gateway untuk {{ $industryName }}</h1>
        <p class="text-lg text-slate-600 mb-7">Otomatiskan komunikasi pelanggan {{ $industryName }} Anda dengan WhatsApp API yang andal dan mudah diintegrasikan.</p>
        <a href="{{ route('register') }}" class="px-6 py-3.5 rounded-xl bg-brand-600 text-white font-semibold shadow-xl shadow-brand-600/25 hover:-translate-y-0.5 transition inline-block">Mulai Gratis</a>
    </div>
</section>

<section class="max-w-3xl mx-auto px-6 py-12 prose prose-slate">
    <p>Industri <strong>{{ $industryName }}</strong> memiliki kebutuhan komunikasi yang unik — mulai dari konfirmasi, pengingat, hingga notifikasi transaksi. <strong>WebWA</strong> membantu bisnis {{ $industryName }} mengotomatiskan seluruh alur pesan WhatsApp tersebut melalui satu REST API yang ringkas, sehingga tim Anda bisa fokus pada pelayanan, bukan mengetik pesan manual.</p>
    <h2>Use case populer untuk {{ $industryName }}</h2>
    <ul>
        <li>Notifikasi otomatis saat status pesanan/transaksi berubah.</li>
        <li>OTP & verifikasi nomor pelanggan saat pendaftaran.</li>
        <li>Reminder jadwal, pembayaran, atau follow-up.</li>
        <li>Blast promo & informasi penting ke pelanggan terpilih.</li>
        <li>Auto-reply pesan masuk via webhook.</li>
    </ul>
    <p>Dengan dukungan multi-device dan dua pilihan backend (nomor pribadi via QR atau Cloud API Meta resmi), WebWA cocok untuk skala kecil hingga enterprise di sektor {{ $industryName }}. Semua pesan tercatat dengan status pengiriman yang jelas, lengkap dengan log dan monitoring untuk keperluan audit.</p>
    <p>Integrasikan WebWA ke sistem {{ $industryName }} Anda hari ini — gratis untuk memulai, dan dapat di-upgrade kapan saja sesuai volume pesan yang Anda butuhkan.</p>
</section>

@include('pseo._cta')
@endsection
