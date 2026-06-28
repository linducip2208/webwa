@extends('layouts.app')
@section('title','Admin · Pengguna')
@section('content')
<div class="max-w-6xl mx-auto" x-data="{ editId:null }">
    <h2 class="text-xl font-extrabold text-slate-900 mb-6">Kelola Pengguna</h2>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                    <tr><th class="text-left px-5 py-3 font-semibold">Nama</th><th class="text-left px-5 py-3 font-semibold">Role</th><th class="text-left px-5 py-3 font-semibold">Paket</th><th class="text-left px-5 py-3 font-semibold">Device</th><th class="text-left px-5 py-3 font-semibold">Kuota</th><th class="text-left px-5 py-3 font-semibold">Status</th><th class="px-5 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3"><p class="font-semibold text-slate-800">{{ $u->name }}</p><p class="text-xs text-slate-400">{{ $u->email }}</p></td>
                            <td class="px-5 py-3"><span class="text-xs px-2 py-1 rounded-full font-semibold {{ $u->role==='admin'?'bg-violet-100 text-violet-700':'bg-slate-100 text-slate-600' }}">{{ $u->role }}</span></td>
                            <td class="px-5 py-3 text-slate-600">{{ $u->plan }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $u->devices_count }}/{{ $u->device_limit }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $u->message_logs_count }}/{{ $u->monthly_quota }}</td>
                            <td class="px-5 py-3"><span class="text-xs px-2 py-1 rounded-full font-semibold {{ $u->is_active?'bg-emerald-100 text-emerald-700':'bg-red-100 text-red-700' }}">{{ $u->is_active?'Aktif':'Nonaktif' }}</span></td>
                            <td class="px-5 py-3 text-right"><button @click="editId = editId==={{ $u->id }} ? null : {{ $u->id }}" class="text-xs text-brand-600 font-semibold">Edit</button></td>
                        </tr>
                        <tr x-show="editId==={{ $u->id }}" x-cloak class="bg-slate-50">
                            <td colspan="7" class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.users.update',$u) }}" class="flex flex-wrap items-end gap-3">
                                    @csrf @method('PUT')
                                    <div><label class="block text-xs font-semibold text-slate-500 mb-1">Role</label>
                                        <select name="role" class="px-3 py-2 rounded-lg border border-slate-300 text-sm"><option value="user" {{ $u->role==='user'?'selected':'' }}>user</option><option value="admin" {{ $u->role==='admin'?'selected':'' }}>admin</option></select></div>
                                    <div><label class="block text-xs font-semibold text-slate-500 mb-1">Paket</label><input name="plan" value="{{ $u->plan }}" class="px-3 py-2 rounded-lg border border-slate-300 text-sm w-28"></div>
                                    <div><label class="block text-xs font-semibold text-slate-500 mb-1">Limit Device</label><input name="device_limit" type="number" value="{{ $u->device_limit }}" class="px-3 py-2 rounded-lg border border-slate-300 text-sm w-24"></div>
                                    <div><label class="block text-xs font-semibold text-slate-500 mb-1">Kuota/bulan</label><input name="monthly_quota" type="number" value="{{ $u->monthly_quota }}" class="px-3 py-2 rounded-lg border border-slate-300 text-sm w-28"></div>
                                    <label class="flex items-center gap-2 text-sm text-slate-600 mb-2"><input type="checkbox" name="is_active" value="1" {{ $u->is_active?'checked':'' }} class="rounded border-slate-300 text-brand-600"> Aktif</label>
                                    <button class="px-4 py-2 rounded-lg bg-brand-600 text-white text-sm font-semibold mb-0.5">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection
