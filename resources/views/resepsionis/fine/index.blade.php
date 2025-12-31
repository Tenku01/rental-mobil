@extends('layouts.resepsionis')

@section('header', 'Manajemen Denda')

@section('content')

<div class="container mx-auto px-4">
<div class="mb-6">
<p class="text-sm text-gray-500 font-medium italic">
Daftar denda keterlambatan atau kerusakan yang dikenakan kepada pelanggan.
</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-sm font-bold rounded-r-xl shadow-sm">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/50">
                <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">
                    <th class="px-6 py-5 text-left">Pelanggan & Mobil</th>
                    <th class="px-6 py-5 text-left">Rincian Denda</th>
                    <th class="px-6 py-5 text-center">Total</th>
                    <th class="px-6 py-5 text-center">Status</th>
                    <th class="px-6 py-5 text-right">Konfirmasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white">
                @forelse($fines as $fine)
                <tr class="hover:bg-cyan-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-800">{{ $fine->peminjaman->user->name }}</div>
                        <div class="text-[11px] text-cyan-600 font-bold uppercase">{{ $fine->peminjaman->mobil_id }}</div>
                    </td>
                    <td class="px-6 py-4 text-[11px] text-gray-500 font-medium">
                        <div>Keterlambatan: Rp {{ number_format($fine->denda_keterlambatan, 0, ',', '.') }}</div>
                        <div>Kerusakan: Rp {{ number_format($fine->denda_kerusakan, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="text-sm font-black text-gray-900">
                            Rp {{ number_format($fine->total_denda, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($fine->status === 'belum dibayar')
                            <span class="px-3 py-1 text-[10px] font-black uppercase rounded-lg bg-rose-100 text-rose-700 border border-rose-200">
                                Belum Bayar
                            </span>
                        @else
                            <span class="px-3 py-1 text-[10px] font-black uppercase rounded-lg bg-emerald-100 text-emerald-700 border border-emerald-200">
                                Lunas
                            </span>
                            <div class="text-[9px] text-gray-400 mt-1 uppercase">{{ $fine->metode_pembayaran }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($fine->status === 'belum dibayar')
                            <form action="{{ route('resepsionis.fine.updateStatus', $fine->id) }}" method="POST" class="flex items-center justify-end gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="metode_pembayaran" required class="text-[10px] border-gray-200 rounded-lg focus:ring-cyan-500 py-1">
                                    <option value="Tunai">Tunai</option>
                                    <option value="Transfer">Transfer</option>
                                </select>
                                <button type="submit" onclick="return confirm('Konfirmasi pelunasan denda ini?')" 
                                        class="bg-cyan-700 hover:bg-cyan-800 text-white p-2 rounded-lg transition-all shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            </form>
                        @else
                            <span class="text-[10px] text-gray-400 font-bold italic">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-20 text-center">
                        <p class="text-sm text-gray-400 font-medium">Tidak ada catatan denda saat ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


</div>
@endsection