<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WebWA — WhatsApp Gateway API untuk Bisnis')</title>
    <meta name="description" content="@yield('meta_description', 'WebWA: gateway WhatsApp multi-device dengan REST API. Kirim notifikasi, OTP, dan blast WhatsApp lewat nomor pribadi (whatsapp-web.js) atau Cloud API Meta resmi.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'WebWA — WhatsApp Gateway API')">
    <meta property="og:description" content="@yield('meta_description', 'Gateway WhatsApp multi-device dengan REST API untuk bisnis.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    @stack('schema')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
                    colors: {
                        brand: {
                            50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',
                            500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b',950:'#022c22'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes floatSlow { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-14px)} }
        @keyframes fadeSlideUp { 0%{transform:translateY(40px);opacity:0} 100%{transform:translateY(0);opacity:1} }
        @keyframes scaleIn { 0%{transform:scale(.9);opacity:0} 100%{transform:scale(1);opacity:1} }
        @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
        @keyframes pulseRing { 0%{transform:scale(.95);opacity:.7} 70%{transform:scale(1.3);opacity:0} 100%{opacity:0} }
        .animate-float-slow { animation:floatSlow 6s ease-in-out infinite }
        .animate-scale-in { animation:scaleIn .6s cubic-bezier(.16,1,.3,1) both }
        .card-lift { transition:transform .35s, box-shadow .35s }
        .card-lift:hover { transform:translateY(-6px); box-shadow:0 24px 48px -12px rgba(5,150,105,.18) }
        .reveal { opacity:0; transform:translateY(30px); transition:opacity .7s, transform .7s cubic-bezier(.16,1,.3,1) }
        .reveal.visible { opacity:1; transform:translateY(0) }
        .grid-bg { background-image:radial-gradient(circle at 1px 1px, rgba(16,185,129,.12) 1px, transparent 0); background-size:32px 32px }
        @media (prefers-reduced-motion: reduce){ *{animation-duration:.01ms!important;transition-duration:.01ms!important} }
    </style>
    @stack('head')
</head>
<body class="font-sans text-slate-800 bg-white antialiased">
    {{-- Header --}}
    <header x-data="{ open:false, scrolled:false }" x-init="window.addEventListener('scroll',()=>scrolled=window.scrollY>20)"
            :class="scrolled ? 'bg-white/90 shadow-sm backdrop-blur-md' : 'bg-transparent'"
            class="fixed top-0 inset-x-0 z-50 transition-all">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-extrabold text-xl text-slate-900">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm5.8 14.16c-.24.68-1.42 1.31-1.96 1.36-.5.05-1.14.07-1.84-.12-.42-.13-.97-.31-1.67-.61-2.94-1.27-4.86-4.23-5.01-4.43-.15-.2-1.2-1.59-1.2-3.03 0-1.44.76-2.15 1.03-2.44.27-.29.58-.37.78-.37.19 0 .39 0 .56.01.18.01.42-.07.66.5.24.59.82 2.03.89 2.18.07.15.12.32.02.52-.1.2-.15.32-.29.5-.15.17-.31.39-.44.52-.15.15-.3.31-.13.6.17.29.76 1.25 1.63 2.03 1.12 1 2.07 1.31 2.36 1.46.29.15.46.12.63-.07.17-.2.73-.85.92-1.14.2-.29.39-.24.66-.15.27.1 1.71.81 2 .96.29.15.49.22.56.34.07.12.07.71-.17 1.39z"/></svg>
                </span>
                Web<span class="text-brand-600">WA</span>
            </a>
            <div class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-600">
                <a href="{{ route('home') }}#fitur" class="hover:text-brand-600 transition">Fitur</a>
                <a href="{{ route('pricing') }}" class="hover:text-brand-600 transition">Harga</a>
                <a href="{{ route('docs') }}" class="hover:text-brand-600 transition">Dokumentasi</a>
                <a href="{{ route('blog.index') }}" class="hover:text-brand-600 transition">Blog</a>
            </div>
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-brand-600">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-600/20">Coba Gratis</a>
                @endauth
            </div>
            <button @click="open=!open" class="md:hidden p-2 text-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </nav>
        <div x-show="open" x-cloak x-transition class="md:hidden bg-white border-t border-slate-100 px-4 py-4 space-y-2">
            <a href="{{ route('home') }}#fitur" class="block py-2 text-slate-700">Fitur</a>
            <a href="{{ route('pricing') }}" class="block py-2 text-slate-700">Harga</a>
            <a href="{{ route('docs') }}" class="block py-2 text-slate-700">Dokumentasi</a>
            <a href="{{ route('blog.index') }}" class="block py-2 text-slate-700">Blog</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block py-2 mt-2 text-center rounded-lg bg-brand-600 text-white font-semibold">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block py-2 text-slate-700">Masuk</a>
                <a href="{{ route('register') }}" class="block py-2 text-center rounded-lg bg-brand-600 text-white font-semibold">Coba Gratis</a>
            @endauth
        </div>
    </header>

    <main>@yield('content')</main>

    {{-- Footer --}}
    <footer class="bg-slate-950 text-slate-400">
        <div class="max-w-7xl mx-auto px-6 py-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
                <div class="flex items-center gap-2 font-extrabold text-xl text-white mb-3">
                    <span class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center text-sm">W</span>WebWA
                </div>
                <p class="text-sm leading-relaxed">Gateway WhatsApp multi-device dengan REST API. Kirim notifikasi, OTP & blast lewat nomor pribadi atau Cloud API Meta.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3">Produk</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}#fitur" class="hover:text-brand-400">Fitur</a></li>
                    <li><a href="{{ route('pricing') }}" class="hover:text-brand-400">Harga</a></li>
                    <li><a href="{{ route('docs') }}" class="hover:text-brand-400">Dokumentasi API</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3">Solusi</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('pseo.industry', 'toko-online') }}" class="hover:text-brand-400">Toko Online</a></li>
                    <li><a href="{{ route('pseo.industry', 'restoran') }}" class="hover:text-brand-400">Restoran</a></li>
                    <li><a href="{{ route('pseo.best') }}" class="hover:text-brand-400">Gateway Terbaik {{ config('webwa.year') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3">Mulai</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('register') }}" class="hover:text-brand-400">Daftar Gratis</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-brand-400">Masuk</a></li>
                    <li><a href="{{ route('blog.index') }}" class="hover:text-brand-400">Blog</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 py-6 text-center text-sm">© {{ date('Y') }} WebWA · Dibangun dengan Laravel · WhatsApp Gateway API</div>
    </footer>

    {{-- Scroll reveal --}}
    <script>
        const io = new IntersectionObserver((entries)=>{
            entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('visible'); io.unobserve(e.target);} });
        },{ threshold:.12 });
        document.querySelectorAll('.reveal').forEach(el=>io.observe(el));
    </script>
    <style>[x-cloak]{display:none!important}</style>
    @stack('scripts')
</body>
</html>
