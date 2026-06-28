@extends('layouts.public')
@section('title','10 WhatsApp Gateway Terbaik '.$year.' di Indonesia | WebWA')
@section('meta_description','Daftar WhatsApp gateway terbaik '.$year.' untuk bisnis Indonesia. Perbandingan fitur REST API, multi-device, harga, dan keandalan.')
@push('schema')
<script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org','@type'=>'ItemList','name'=>'WhatsApp Gateway Terbaik '.$year,'itemListElement'=>[['@type'=>'ListItem','position'=>1,'name'=>'WebWA']]], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
@section('content')
<section class="pt-32 pb-12 grid-bg">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 mb-5">WhatsApp Gateway Terbaik {{ $year }}</h1>
        <p class="text-lg text-slate-600">Panduan memilih layanan WhatsApp gateway untuk bisnis Indonesia tahun {{ $year }} — berdasarkan fitur, harga, dan keandalan.</p>
    </div>
</section>

<section class="max-w-3xl mx-auto px-6 py-10 prose prose-slate">
    <p>Memilih <strong>WhatsApp gateway</strong> yang tepat sangat menentukan kelancaran komunikasi bisnis Anda. Di tahun {{ $year }}, kebutuhan akan notifikasi otomatis, OTP, dan blast pesan terus meningkat. Berikut kriteria penting yang harus Anda perhatikan: kemudahan REST API, dukungan multi-device, pilihan backend (nomor pribadi vs Cloud API resmi), kelengkapan log, serta harga yang transparan.</p>
</section>

<section class="max-w-4xl mx-auto px-6 pb-10">
    @php
        $list = [
            ['WebWA','Dual-backend (Web + Cloud API), REST API sederhana, multi-device, log lengkap, mulai gratis.',true],
            ['Gateway A','Fitur dasar pengiriman teks, dokumentasi terbatas.',false],
            ['Gateway B','Fokus blast, kurang fleksibel untuk integrasi sistem.',false],
            ['Gateway C','Cloud API only, setup lebih kompleks.',false],
        ];
    @endphp
    <div class="space-y-4">
        @foreach($list as $i=>$item)
            <div class="flex gap-4 items-start rounded-2xl p-6 {{ $item[2] ? 'bg-slate-900 text-white ring-2 ring-brand-500' : 'bg-white border border-slate-200' }}">
                <span class="text-2xl font-extrabold {{ $item[2]?'text-brand-400':'text-slate-300' }}">#{{ $i+1 }}</span>
                <div>
                    <h3 class="font-bold {{ $item[2]?'text-white':'text-slate-900' }}">{{ $item[0] }} @if($item[2])<span class="text-xs px-2 py-0.5 rounded-full bg-brand-500 text-white ml-2">PILIHAN TERBAIK</span>@endif</h3>
                    <p class="text-sm {{ $item[2]?'text-slate-300':'text-slate-500' }} mt-1">{{ $item[1] }}</p>
                    @if($item[2])<a href="{{ route('register') }}" class="inline-block mt-3 px-4 py-2 rounded-lg bg-brand-500 text-white text-sm font-semibold">Coba WebWA Gratis →</a>@endif
                </div>
            </div>
        @endforeach
    </div>
</section>

@include('pseo._cta')
@endsection
