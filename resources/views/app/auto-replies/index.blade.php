@extends('layouts.app')
@section('title','Balasan Otomatis')
@section('content')
<div class="max-w-6xl mx-auto" x-data="autoReplies()">
    <div class="flex items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Balasan Otomatis</h2>
            <p class="text-sm text-slate-500">Balas pesan masuk secara otomatis berdasarkan kata kunci.</p>
        </div>
        <button @click="openCreate()" class="px-4 py-2.5 rounded-lg bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-600/20 shrink-0">+ Tambah Balasan</button>
    </div>

    <div class="mb-6 flex items-start gap-3 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            Auto-reply hanya berjalan saat daemon <code class="font-mono bg-amber-100 px-1 rounded">php artisan whatsapp:web:listen {session}</code> aktif untuk device terkait, dan <code class="font-mono bg-amber-100 px-1 rounded">WHATSAPP_PERSIST_INCOMING=true</code>.
        </div>
    </div>

    <div class="space-y-3">
        @forelse($rules as $rule)
            @php($ruleData = [
                'id' => $rule->id,
                'name' => $rule->name,
                'device_id' => $rule->device_id,
                'match_type' => $rule->match_type,
                'keyword' => $rule->keyword,
                'reply_text' => $rule->reply_text,
                'priority' => $rule->priority,
                'case_sensitive' => $rule->case_sensitive,
                'skip_groups' => $rule->skip_groups,
                'is_active' => $rule->is_active,
            ])
            <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-col sm:flex-row sm:items-start gap-4 hover:shadow-lg hover:shadow-slate-200/60 transition">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-bold text-slate-900">{{ $rule->name }}</h3>
                        <span class="text-[10px] uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold tracking-wide">{{ $rule->matchTypeLabel() }}</span>
                        @if($rule->is_active)
                            <span class="text-[10px] uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold tracking-wide">Aktif</span>
                        @else
                            <span class="text-[10px] uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-400 font-bold tracking-wide">Nonaktif</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 mt-1.5">
                        Kata kunci: <span class="font-mono text-slate-700">{{ $rule->keyword }}</span>
                        <span class="text-slate-300">·</span> {{ $rule->device ? $rule->device->name : 'Semua device' }}
                        <span class="text-slate-300">·</span> prioritas {{ $rule->priority }}
                        <span class="text-slate-300">·</span> {{ $rule->triggered_count }}× terpicu
                    </p>
                    <p class="text-sm text-slate-700 mt-2 bg-slate-50 rounded-lg px-3 py-2 whitespace-pre-wrap break-words">{{ \Illuminate\Support\Str::limit($rule->reply_text, 200) }}</p>
                </div>
                <div class="flex sm:flex-col items-stretch gap-2 shrink-0 sm:w-32">
                    <button type="button"
                            @click="openEdit({{ \Illuminate\Support\Js::from($ruleData) }})"
                            class="flex-1 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Edit</button>
                    <form method="POST" action="{{ route('auto-replies.toggle',$rule) }}" class="flex-1">@csrf
                        <button class="w-full py-2 rounded-lg text-sm font-semibold transition {{ $rule->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">{{ $rule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                    </form>
                    <form method="POST" action="{{ route('auto-replies.destroy',$rule) }}" class="flex-1" onsubmit="return confirm('Hapus balasan ini?')">@csrf @method('DELETE')
                        <button class="w-full py-2 rounded-lg bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100 transition">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
                <div class="text-5xl mb-3">🤖</div>
                <h3 class="font-bold text-slate-800 mb-1">Belum ada balasan otomatis</h3>
                <p class="text-sm text-slate-500 mb-4">Buat aturan kata kunci → balasan untuk merespons pesan masuk otomatis.</p>
                <button @click="openCreate()" class="px-4 py-2 rounded-lg bg-brand-600 text-white text-sm font-semibold">+ Tambah Balasan Pertama</button>
            </div>
        @endforelse
    </div>

    {{-- Create / Edit modal --}}
    <div x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="show=false" class="absolute inset-0 bg-slate-900/50"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto" x-transition>
            <h3 class="text-lg font-bold text-slate-900 mb-4" x-text="form.id ? 'Edit Balasan Otomatis' : 'Tambah Balasan Otomatis'"></h3>
            <form method="POST" :action="form.id ? '{{ url('auto-replies') }}/'+form.id : '{{ route('auto-replies.store') }}'" class="space-y-4">
                @csrf
                <template x-if="form.id"><input type="hidden" name="_method" value="PUT"></template>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Aturan</label>
                    <input name="name" x-model="form.name" required maxlength="120" class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none" placeholder="mis. Salam pembuka">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tipe Cocok</label>
                        <select name="match_type" x-model="form.match_type" class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none">
                            <option value="contains">Mengandung</option>
                            <option value="exact">Sama persis</option>
                            <option value="starts_with">Diawali</option>
                            <option value="regex">Regex</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Device</label>
                        <select name="device_id" x-model="form.device_id" class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none">
                            <option value="">Semua device</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}">{{ $device->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kata Kunci / Pola</label>
                    <input name="keyword" x-model="form.keyword" required maxlength="255" class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none font-mono text-sm" placeholder="mis. halo">
                    <p class="text-xs text-slate-400 mt-1" x-show="form.match_type==='regex'">Untuk Regex, tulis pola tanpa pembatas. Contoh: <span class="font-mono">^(halo|hai)</span></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Teks Balasan</label>
                    <textarea name="reply_text" x-model="form.reply_text" required rows="4" maxlength="4096" class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none" placeholder="Halo! Terima kasih sudah menghubungi kami."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Prioritas</label>
                        <input name="priority" x-model="form.priority" type="number" min="0" max="9999" class="w-full px-4 py-2.5 rounded-lg border-[1.5px] border-slate-300 focus:border-brand-500 outline-none">
                        <p class="text-xs text-slate-400 mt-1">Lebih tinggi diperiksa lebih dulu.</p>
                    </div>
                    <div class="space-y-2 pt-1">
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="hidden" name="case_sensitive" value="0">
                            <input type="checkbox" name="case_sensitive" value="1" x-model="form.case_sensitive" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"> Peka huruf besar/kecil
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="hidden" name="skip_groups" value="0">
                            <input type="checkbox" name="skip_groups" value="1" x-model="form.skip_groups" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"> Abaikan pesan grup
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"> Aktif
                        </label>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="show=false" class="flex-1 py-2.5 rounded-lg bg-slate-100 text-slate-700 font-semibold">Batal</button>
                    <button class="flex-1 py-2.5 rounded-lg bg-brand-600 text-white font-semibold hover:bg-brand-700" x-text="form.id ? 'Simpan' : 'Buat'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function autoReplies() {
    const blank = { id:null, name:'', device_id:'', match_type:'contains', keyword:'', reply_text:'', priority:0, case_sensitive:false, skip_groups:true, is_active:true };
    return {
        show: false,
        form: { ...blank },
        openCreate() { this.form = { ...blank }; this.show = true; },
        openEdit(r) {
            this.form = {
                id: r.id,
                name: r.name ?? '',
                device_id: r.device_id ?? '',
                match_type: r.match_type ?? 'contains',
                keyword: r.keyword ?? '',
                reply_text: r.reply_text ?? '',
                priority: r.priority ?? 0,
                case_sensitive: !!r.case_sensitive,
                skip_groups: !!r.skip_groups,
                is_active: !!r.is_active,
            };
            this.show = true;
        },
    };
}
</script>
@endpush
