@extends('layouts.app')
@section('title','API Key')
@section('content')
<div class="max-w-4xl mx-auto" x-data="{ create:false }">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">API Key</h2>
            <p class="text-sm text-slate-500">Gunakan untuk autentikasi REST API.</p>
        </div>
        <button @click="create=true" class="px-4 py-2.5 rounded-lg bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 shadow-lg shadow-brand-600/20">+ Buat API Key</button>
    </div>

    @if(session('new_api_key'))
        <div x-data="{ copied:false, key:'{{ session('new_api_key') }}' }" class="mb-6 rounded-xl bg-brand-50 border border-brand-200 p-5">
            <p class="text-sm font-semibold text-brand-800 mb-2">✅ API key baru dibuat — salin sekarang, tidak akan ditampilkan lagi!</p>
            <div class="flex items-center gap-2">
                <code class="flex-1 px-3 py-2.5 bg-white border border-brand-200 rounded-lg text-sm font-mono text-slate-800 break-all">{{ session('new_api_key') }}</code>
                <button @click="navigator.clipboard.writeText(key); copied=true; setTimeout(()=>copied=false,2000)" class="px-4 py-2.5 rounded-lg bg-brand-600 text-white text-sm font-semibold shrink-0">
                    <span x-show="!copied">Salin</span><span x-show="copied" x-cloak>Tersalin!</span>
                </button>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                    <tr><th class="text-left px-6 py-3 font-semibold">Nama</th><th class="text-left px-6 py-3 font-semibold">Prefix</th><th class="text-left px-6 py-3 font-semibold">Request</th><th class="text-left px-6 py-3 font-semibold">Terakhir Dipakai</th><th class="text-left px-6 py-3 font-semibold">Status</th><th class="px-6 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($keys as $key)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 font-semibold text-slate-800">{{ $key->name }}</td>
                            <td class="px-6 py-3"><code class="text-xs bg-slate-100 px-2 py-0.5 rounded">{{ $key->key_prefix }}…</code></td>
                            <td class="px-6 py-3 text-slate-500">{{ number_format($key->request_count) }}</td>
                            <td class="px-6 py-3 text-slate-400">{{ $key->last_used_at?->diffForHumans() ?? 'Belum pernah' }}</td>
                            <td class="px-6 py-3">
                                <span class="text-xs px-2 py-1 rounded-full font-semibold {{ $key->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $key->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('api-keys.toggle',$key) }}" class="inline">@csrf<button class="text-xs text-slate-500 hover:text-brand-600 font-semibold">{{ $key->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
                                <form method="POST" action="{{ route('api-keys.destroy',$key) }}" class="inline ml-3" onsubmit="return confirm('Hapus API key ini?')">@csrf @method('DELETE')<button class="text-xs text-red-500 hover:text-red-700 font-semibold">Hapus</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada API key.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 bg-slate-900 rounded-2xl p-6 text-slate-300">
        <h3 class="text-white font-bold mb-3">Contoh Penggunaan</h3>
        <pre class="text-xs overflow-x-auto"><code>curl -X POST {{ rtrim(config('app.url'),'/') }}/api/v1/messages/text \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"device":"1","to":"628123456789","message":"Halo dari WebWA!"}'</code></pre>
        <a href="{{ route('docs') }}" class="inline-block mt-3 text-sm text-brand-400 font-semibold">Lihat dokumentasi lengkap →</a>
    </div>

    {{-- Create modal --}}
    <div x-show="create" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="create=false" class="absolute inset-0 bg-slate-900/50"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Buat API Key</h3>
            <form method="POST" action="{{ route('api-keys.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Key</label>
                    <input name="name" required class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none" placeholder="mis. Server Produksi">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="create=false" class="flex-1 py-2.5 rounded-lg bg-slate-100 text-slate-700 font-semibold">Batal</button>
                    <button class="flex-1 py-2.5 rounded-lg bg-brand-600 text-white font-semibold hover:bg-brand-700">Buat</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
