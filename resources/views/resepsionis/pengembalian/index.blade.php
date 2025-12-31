@extends('layouts.resepsionis')

@section('header', 'Manajemen Pengembalian')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500 font-medium italic">Kelola riwayat dan proses pengembalian mobil.</p>
        <a href="{{ route('resepsionis.pengembalian.create') }}" class="inline-flex items-center px-4 py-2 bg-cyan-700 text-white text-xs font-bold uppercase rounded-lg shadow hover:bg-cyan-800 transition-all">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Tambah Pengembalian
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-800 text-sm font-bold rounded-lg shadow-sm border-l-4 border-emerald-500">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50/50">
                <tr class="text-[11px] font-black text-gray-400 uppercase tracking-widest">
                    <th class="px-6 py-5 text-left">Kode / Tanggal</th>
                    <th class="px-6 py-5 text-left">Pelanggan & Unit</th>
                    <th class="px-6 py-5 text-center">Status</th>
                    <th class="px-6 py-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white">
                @forelse($pengembalian as $item)
                <tr class="hover:bg-cyan-50/20 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $item->kode_pengembalian }}</div>
                        <div class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-700">{{ $item->peminjaman->user->name }}</div>
                        <div class="text-[11px] text-cyan-700 font-black uppercase">{{ $item->peminjaman->mobil_id }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 text-[10px] font-black uppercase rounded-lg {{ $item->status == 'Selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right flex justify-end space-x-3">
                        <a href="{{ route('resepsionis.pengembalian.show', $item->kode_pengembalian) }}" class="text-gray-400 hover:text-cyan-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('resepsionis.pengembalian.edit', $item->kode_pengembalian) }}" class="text-gray-400 hover:text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('resepsionis.pengembalian.destroy', $item->kode_pengembalian) }}" method="POST" onsubmit="return confirm('Hapus data riwayat ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">Belum ada riwayat pengembalian.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection