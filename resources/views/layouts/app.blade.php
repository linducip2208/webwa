<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') · WebWA</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme:{ extend:{
            fontFamily:{ sans:['Inter','ui-sans-serif','system-ui'] },
            colors:{ brand:{50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b'} }
        }}}
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak]{display:none!important}
        .nav-link{transition:all .18s}
        .nav-link:hover{transform:translateX(2px)}
        @media (prefers-reduced-motion: reduce){*{animation-duration:.01ms!important;transition-duration:.01ms!important}}
        ::-webkit-scrollbar{width:9px;height:9px}::-webkit-scrollbar-thumb{background:#10b98155;border-radius:8px}
    </style>
    @stack('head')
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased" x-data="{ sidebar:false }">
@php
    $nav = [
        ['route'=>'dashboard','label'=>'Dashboard','icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['route'=>'devices.index','label'=>'Device','icon'=>'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z','active'=>'devices'],
        ['route'=>'send.create','label'=>'Kirim Pesan','icon'=>'M12 19l9 2-9-18-9 18 9-2zm0 0v-8'],
        ['route'=>'api-keys.index','label'=>'API Key','icon'=>'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
        ['route'=>'logs.index','label'=>'Log Pesan','icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['route'=>'auto-replies.index','label'=>'Balasan Otomatis','icon'=>'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l1.3-3.9A7.96 7.96 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z','active'=>'auto-replies'],
    ];
@endphp
    {{-- Mobile overlay --}}
    <div x-show="sidebar" x-cloak @click="sidebar=false" class="fixed inset-0 bg-slate-900/50 z-30 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed lg:fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 flex flex-col transition-transform">
        <div class="h-16 flex items-center gap-2 px-5 border-b border-white/5">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white font-bold">W</span>
            <a href="{{ route('home') }}" class="font-extrabold text-lg text-white">Web<span class="text-brand-400">WA</span></a>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-1">
            <p class="px-3 text-[11px] uppercase tracking-wider text-slate-500 font-semibold mb-2">Menu</p>
            @foreach($nav as $item)
                @php $isActive = request()->routeIs($item['route']) || (isset($item['active']) && request()->is($item['active'].'*')); @endphp
                <a href="{{ route($item['route']) }}"
                   class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ $isActive ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                    {{ $item['label'] }}
                </a>
            @endforeach

            @if(auth()->user()->isAdmin())
                <p class="px-3 text-[11px] uppercase tracking-wider text-slate-500 font-semibold mt-6 mb-2">Admin</p>
                <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-brand-600 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Statistik
                </a>
                <a href="{{ route('admin.users') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users') ? 'bg-brand-600 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-9.13a4 4 0 11-8 0 4 4 0 018 0zm6 3a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Pengguna
                </a>
                <a href="{{ route('admin.devices') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.devices') ? 'bg-brand-600 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM9 17h6m4 0a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Semua Device
                </a>
                <a href="{{ route('admin.logs') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.logs') ? 'bg-brand-600 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Semua Log
                </a>
                <a href="/whatsapp" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/5 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 3v-3z"/></svg>
                    Panel WhatsApp
                </a>
            @endif
        </nav>
        <div class="p-3 border-t border-white/5">
            <a href="{{ route('docs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm hover:bg-white/5 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Dokumentasi
            </a>
        </div>
    </aside>

    {{-- Main --}}
    <div class="lg:pl-64 min-h-screen flex flex-col">
        <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-20 flex items-center justify-between px-4 sm:px-6 backdrop-blur">
            <div class="flex items-center gap-3">
                <button @click="sidebar=true" class="lg:hidden p-2 -ml-2 text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                <h1 class="text-lg font-bold text-slate-900">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-4" x-data="{ menu:false }">
                <a href="{{ route('send.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Kirim
                </a>
                <button @click="menu=!menu" class="flex items-center gap-2">
                    <span class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-white flex items-center justify-center font-semibold text-sm">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                    <span class="hidden sm:block text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                </button>
                <div x-show="menu" x-cloak @click.outside="menu=false" x-transition class="absolute right-4 top-14 w-52 bg-white rounded-xl shadow-xl border border-slate-100 py-2">
                    <div class="px-4 py-2 border-b border-slate-100">
                        <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                        <span class="mt-1 inline-block text-[10px] px-2 py-0.5 rounded-full bg-brand-100 text-brand-700 font-semibold uppercase">{{ auth()->user()->plan }}</span>
                    </div>
                    <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Lihat Website</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</button>
                    </form>
                </div>
            </div>
        </header>

        @if(session('status'))
            <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)" class="mx-4 sm:mx-6 mt-4 flex items-start gap-3 p-4 rounded-xl bg-brand-50 border border-brand-200 text-brand-800">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="text-sm">{{ session('status') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mx-4 sm:mx-6 mt-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <main class="flex-1 p-4 sm:p-6">@yield('content')</main>
    </div>
    @stack('scripts')
</body>
</html>
