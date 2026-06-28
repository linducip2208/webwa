@extends('layouts.public')
@section('title', $aName.' vs '.$bName.' — Perbandingan WhatsApp Gateway | WebWA')
@section('meta_description','Perbandingan '.$aName.' vs '.$bName.': fitur, harga, dan keandalan WhatsApp gateway. Plus alternatif terbaik: WebWA.')
@section('content')
<section class="pt-32 pb-12 grid-bg">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 mb-5">{{ $aName }} vs {{ $bName }}</h1>
        <p class="text-lg text-slate-600">Bandingkan dua WhatsApp gateway populer — dan temukan alternatif yang lebih fleksibel.</p>
    </div>
</section>

<section class="max-w-4xl mx-auto px-6 py-10">
    <div class="overflow-x-auto rounded-2xl border border-slate-200">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase"><tr><th class="text-left px-5 py-3 font-semibold">Kriteria</th><th class="text-left px-5 py-3 font-semibold">{{ $aName }}</th><th class="text-left px-5 py-3 font-semibold">{{ $bName }}</th><th class="text-left px-5 py-3 font-semibold bg-brand-50 text-brand-700">WebWA</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @foreach([
                    ['REST API','Ya','Ya','Ya, ringkas'],
                    ['Multi-device','Terbatas','Terbatas','Ya'],
                    ['Backend Web (nomor pribadi)','Sebagian','Sebagian','Ya'],
                    ['Cloud API Meta resmi','Tidak','Sebagian','Ya'],
                    ['Webhook pesan masuk','Sebagian','Ya','Ya'],
                    ['Harga mulai','Berbayar','Berbayar','Gratis'],
                ] as $row)
                    <tr><td class="px-5 py-3 font-medium text-slate-700">{{ $row[0] }}</td><td class="px-5 py-3 text-slate-500">{{ $row[1] }}</td><td class="px-5 py-3 text-slate-500">{{ $row[2] }}</td><td class="px-5 py-3 font-semibold text-brand-700 bg-brand-50/40">{{ $row[3] }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<section class="max-w-3xl mx-auto px-6 pb-10 prose prose-slate">
    <p>Saat membandingkan <strong>{{ $aName }}</strong> dan <strong>{{ $bName }}</strong>, keduanya memiliki kelebihan masing-masing untuk pengiriman pesan WhatsApp. Namun bagi bisnis yang menginginkan fleksibilitas dual-backend, REST API yang sederhana, dan harga yang transparan, <strong>WebWA</strong> menjadi alternatif yang patut dipertimbangkan. WebWA menggabungkan kemampuan nomor pribadi (whatsapp-web.js) dan Cloud API Meta resmi dalam satu platform, sehingga Anda bisa menyesuaikan strategi sesuai skala dan kebutuhan.</p>
</section>

@include('pseo._cta')
@endsection
