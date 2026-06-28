@php
    $brandHead = <<<'HTML'
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
<script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','ui-sans-serif']},colors:{brand:{50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b'}}}}}</script>
HTML;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar · WebWA</title>
    {!! $brandHead !!}
</head>
<body class="font-sans bg-white text-slate-800 antialiased">
<div class="min-h-screen grid lg:grid-cols-2">
    <div class="hidden lg:flex relative bg-gradient-to-br from-brand-500 via-brand-700 to-slate-900 p-12 flex-col justify-between overflow-hidden">
        <div class="absolute inset-0 opacity-30" style="background-image:radial-gradient(circle at 20% 30%,rgba(255,255,255,.25),transparent 40%),radial-gradient(circle at 80% 70%,rgba(255,255,255,.15),transparent 40%)"></div>
        <div class="absolute -bottom-24 -right-24 text-[22rem] opacity-10 select-none">🚀</div>
        <a href="{{ route('home') }}" class="relative flex items-center gap-2 text-white">
            <span class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center font-bold text-lg">W</span>
            <span class="font-extrabold text-2xl">WebWA</span>
        </a>
        <div class="relative text-white">
            <h2 class="text-4xl font-extrabold leading-tight mb-4">Mulai gratis<br>dalam 2 menit.</h2>
            <p class="text-brand-50/90 text-lg mb-8 max-w-md">Daftar, hubungkan WhatsApp dengan QR, buat API key, dan langsung kirim pesan via REST API.</p>
            <ul class="space-y-3 max-w-md">
                @foreach(['1 device gratis selamanya','1.000 pesan / bulan','REST API + webhook','Tanpa kartu kredit'] as $f)
                    <li class="flex items-center gap-3 text-brand-50"><span class="w-6 h-6 rounded-full bg-white/15 flex items-center justify-center text-xs">✓</span>{{ $f }}</li>
                @endforeach
            </ul>
        </div>
        <div class="relative text-brand-100/70 text-xs">© {{ date('Y') }} WebWA · Powered by Laravel</div>
    </div>

    <div class="flex items-center justify-center p-8 lg:p-16">
        <div class="w-full max-w-md">
            <a href="{{ route('home') }}" class="lg:hidden flex items-center gap-2 mb-8 font-extrabold text-2xl text-slate-900"><span class="w-9 h-9 rounded-xl bg-brand-600 text-white flex items-center justify-center">W</span>WebWA</a>
            <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Buat Akun</h1>
            <p class="text-slate-500 mb-8">Sudah punya akun? <a href="{{ route('login') }}" class="text-brand-600 font-semibold hover:underline">Masuk</a></p>

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full px-4 py-3 rounded-xl border-[1.5px] border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition" placeholder="Nama Anda">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl border-[1.5px] border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition" placeholder="anda@email.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Perusahaan <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="text" name="company" value="{{ old('company') }}" class="w-full px-4 py-3 rounded-xl border-[1.5px] border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition" placeholder="PT Contoh">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border-[1.5px] border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ulangi</label>
                        <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-xl border-[1.5px] border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition" placeholder="••••••••">
                    </div>
                </div>
                <button class="w-full py-3 rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 text-white font-semibold shadow-lg shadow-brand-600/25 hover:-translate-y-0.5 transition mt-2">Daftar Gratis</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
