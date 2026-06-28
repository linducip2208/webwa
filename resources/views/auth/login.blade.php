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
    <title>Masuk · WebWA</title>
    {!! $brandHead !!}
</head>
<body class="font-sans bg-white text-slate-800 antialiased">
<div class="min-h-screen grid lg:grid-cols-2">
    {{-- Left hero --}}
    <div class="hidden lg:flex relative bg-gradient-to-br from-brand-500 via-brand-700 to-slate-900 p-12 flex-col justify-between overflow-hidden">
        <div class="absolute inset-0 opacity-30" style="background-image:radial-gradient(circle at 20% 30%,rgba(255,255,255,.25),transparent 40%),radial-gradient(circle at 80% 70%,rgba(255,255,255,.15),transparent 40%)"></div>
        <div class="absolute -bottom-24 -right-24 text-[22rem] opacity-10 select-none">💬</div>
        <a href="{{ route('home') }}" class="relative flex items-center gap-2 text-white">
            <span class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center font-bold text-lg">W</span>
            <span class="font-extrabold text-2xl">WebWA</span>
        </a>
        <div class="relative text-white">
            <h2 class="text-4xl font-extrabold leading-tight mb-4">WhatsApp Gateway<br>untuk bisnis Anda.</h2>
            <p class="text-brand-50/90 text-lg mb-8 max-w-md">Kirim notifikasi, OTP, dan blast WhatsApp lewat REST API. Multi-device, nomor pribadi atau Cloud API resmi.</p>
            <div class="grid grid-cols-3 gap-4 max-w-md">
                @foreach([['🔌','REST API'],['📱','Multi-Device'],['⚡','Realtime']] as $b)
                    <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                        <div class="text-2xl mb-1">{{ $b[0] }}</div>
                        <div class="text-xs font-medium text-brand-50">{{ $b[1] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="relative text-brand-100/70 text-xs">© {{ date('Y') }} WebWA · Powered by Laravel + whatsapp-web.js</div>
    </div>

    {{-- Right form --}}
    <div class="flex items-center justify-center p-8 lg:p-16">
        <div class="w-full max-w-md">
            <a href="{{ route('home') }}" class="lg:hidden flex items-center gap-2 mb-8 font-extrabold text-2xl text-slate-900"><span class="w-9 h-9 rounded-xl bg-brand-600 text-white flex items-center justify-center">W</span>WebWA</a>
            <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Masuk</h1>
            <p class="text-slate-500 mb-8">Belum punya akun? <a href="{{ route('register') }}" class="text-brand-600 font-semibold hover:underline">Daftar gratis</a></p>

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-3 rounded-xl border-[1.5px] border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition" placeholder="anda@email.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 rounded-xl border-[1.5px] border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition" placeholder="••••••••">
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-slate-600"><input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"> Ingat saya</label>
                </div>
                <button class="w-full py-3 rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 text-white font-semibold shadow-lg shadow-brand-600/25 hover:-translate-y-0.5 transition">Masuk</button>
            </form>

            <div class="flex items-center gap-3 my-7"><span class="flex-1 h-px bg-slate-200"></span><span class="text-xs text-slate-400 font-medium">DEMO</span><span class="flex-1 h-px bg-slate-200"></span></div>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm">
                <div class="font-semibold text-slate-800 mb-2">🧪 Akun Demo</div>
                <div class="space-y-1 text-slate-600 text-xs font-mono">
                    <div><span class="font-bold">Admin:</span> admin@webwa.test / password</div>
                    <div><span class="font-bold">User:</span> user@webwa.test / password</div>
                    <div><span class="font-bold">Demo:</span> demo@webwa.test / password</div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
