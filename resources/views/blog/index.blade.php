@extends('layouts.public')
@section('title', isset($category) ? 'Blog: '.$category->name.' — WebWA' : 'Blog — Tips WhatsApp Gateway & Otomasi Bisnis')
@section('meta_description','Artikel & tutorial seputar WhatsApp gateway, REST API, OTP, notifikasi otomatis, dan otomasi bisnis dengan WebWA.')
@section('content')
<section class="pt-28 pb-10 grid-bg">
    <div class="max-w-6xl mx-auto px-6">
        <h1 class="text-4xl font-extrabold text-slate-900 mb-3">{{ isset($category) ? $category->name : 'Blog WebWA' }}</h1>
        <p class="text-lg text-slate-600 max-w-2xl">{{ isset($category) ? $category->description : 'Tips, tutorial, dan insight seputar otomasi WhatsApp untuk bisnis Anda.' }}</p>
    </div>
</section>

<div class="max-w-6xl mx-auto px-6 py-12 grid lg:grid-cols-4 gap-10">
    <div class="lg:col-span-3">
        @if($posts->isEmpty())
            <div class="bg-white border border-dashed border-slate-300 rounded-2xl p-12 text-center text-slate-400">Belum ada artikel.</div>
        @else
            <div class="grid sm:grid-cols-2 gap-6">
                @foreach($posts as $post)
                    <a href="{{ route('blog.show',$post) }}" class="reveal bg-white rounded-2xl border border-slate-200 overflow-hidden card-lift">
                        <div class="h-44 bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center text-5xl">💬</div>
                        <div class="p-5">
                            @if($post->category)<span class="text-xs font-semibold text-brand-600">{{ $post->category->name }}</span>@endif
                            <h2 class="font-bold text-slate-900 mt-1 mb-2 line-clamp-2">{{ $post->title }}</h2>
                            <p class="text-sm text-slate-500 line-clamp-3">{{ $post->excerpt }}</p>
                            <p class="text-xs text-slate-400 mt-3">{{ $post->published_at?->translatedFormat('d M Y') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
        @endif
    </div>

    <aside class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-bold text-slate-900 mb-3">Kategori</h3>
            <ul class="space-y-2 text-sm">
                @foreach($categories as $cat)
                    <li><a href="{{ route('blog.category',$cat) }}" class="flex justify-between text-slate-600 hover:text-brand-600"><span>{{ $cat->name }}</span><span class="text-slate-400">{{ $cat->posts_count }}</span></a></li>
                @endforeach
            </ul>
        </div>
        <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-2xl p-5 text-white">
            <h3 class="font-bold mb-2">Coba WebWA</h3>
            <p class="text-sm text-brand-50 mb-4">Gateway WhatsApp dengan REST API. Gratis untuk mulai.</p>
            <a href="{{ route('register') }}" class="block text-center py-2.5 rounded-lg bg-white text-brand-700 text-sm font-semibold">Daftar Gratis</a>
        </div>
    </aside>
</div>
@endsection
