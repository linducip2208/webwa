@extends('layouts.public')
@section('title', ($post->meta_title ?: $post->title).' — WebWA Blog')
@section('meta_description', $post->meta_description ?: $post->excerpt)
@push('schema')
@php($articleSchema = ['@context'=>'https://schema.org','@type'=>'Article','headline'=>$post->title,'description'=>$post->excerpt,'datePublished'=>optional($post->published_at)->toIso8601String(),'author'=>['@type'=>'Organization','name'=>'WebWA'],'publisher'=>['@type'=>'Organization','name'=>'WebWA']])
<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
@section('content')
<article class="max-w-3xl mx-auto px-6 pt-28 pb-16">
    <a href="{{ route('blog.index') }}" class="text-sm text-brand-600 font-semibold">← Semua artikel</a>
    @if($post->category)<p class="text-sm font-semibold text-brand-600 mt-6">{{ $post->category->name }}</p>@endif
    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2 mb-4 leading-tight">{{ $post->title }}</h1>
    <div class="flex items-center gap-3 text-sm text-slate-400 mb-8">
        <span>{{ $post->published_at?->translatedFormat('d F Y') }}</span><span>·</span><span>{{ $post->views }} dilihat</span>
    </div>
    <div class="h-56 rounded-2xl bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center text-6xl mb-8">💬</div>
    <div class="prose prose-slate max-w-none prose-headings:font-bold prose-a:text-brand-600 text-slate-700 leading-relaxed space-y-4">
        {!! $post->content !!}
    </div>

    <div class="mt-12 rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 p-6 text-white text-center">
        <h3 class="text-xl font-bold mb-2">Otomasi WhatsApp bisnis Anda</h3>
        <p class="text-brand-50 text-sm mb-4">Kirim notifikasi & OTP via REST API. Mulai gratis.</p>
        <a href="{{ route('register') }}" class="inline-block px-5 py-2.5 rounded-lg bg-white text-brand-700 text-sm font-semibold">Coba WebWA Gratis</a>
    </div>
</article>

@if($related->isNotEmpty())
<section class="max-w-5xl mx-auto px-6 pb-16">
    <h2 class="text-xl font-extrabold text-slate-900 mb-5">Artikel Terkait</h2>
    <div class="grid sm:grid-cols-3 gap-5">
        @foreach($related as $r)
            <a href="{{ route('blog.show',$r) }}" class="bg-white rounded-xl border border-slate-200 p-5 card-lift"><h3 class="font-bold text-slate-800 text-sm line-clamp-2">{{ $r->title }}</h3><p class="text-xs text-slate-400 mt-2">{{ $r->published_at?->translatedFormat('d M Y') }}</p></a>
        @endforeach
    </div>
</section>
@endif
@endsection
