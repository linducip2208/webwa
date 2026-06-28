@props(['title' => '', 'desc' => '', 'content' => ''])
<div x-data="{ copied:false }">
    <div class="flex items-center justify-between gap-3 mb-2">
        <div>
            @if($title)<p class="font-semibold text-slate-800 text-sm">{{ $title }}</p>@endif
            @if($desc)<p class="text-xs text-slate-400">{{ $desc }}</p>@endif
        </div>
        <button type="button"
                @click="navigator.clipboard.writeText($refs.code.textContent.trim()); copied=true; setTimeout(()=>copied=false,1500)"
                class="shrink-0 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200 transition">
            <span x-text="copied ? '✓ Tersalin' : 'Salin'"></span>
        </button>
    </div>
    <pre x-ref="code" class="overflow-x-auto rounded-xl bg-slate-900 text-slate-100 text-xs leading-relaxed p-4 font-mono whitespace-pre">{{ $content }}</pre>
</div>
