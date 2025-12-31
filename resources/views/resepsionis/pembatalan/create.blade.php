@extends('layouts.resepsionis')

@section('content')
<div class="container mx-auto p-6 mt-20">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Form Pembatalan Peminjaman</h1>

    <form method="POST" action="{{ route('resepsionis.pembatalan.store') }}" class="space-y-4 bg-white shadow p-6 rounded-lg">
        @csrf

        <!-- Input Peminjaman -->
        @if(isset($selectedPeminjaman))
            <div>
                <label for="peminjaman_id" class="block text-gray-700 font-semibold mb-1">Peminjaman</label>
                <input type="text" 
                       value="{{ $selectedPeminjaman->id }} - {{ $selectedPeminjaman->user->name ?? 'Tidak diketahui' }} ({{ $selectedPeminjaman->status }})"
                       class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed focus:ring-0 focus:border-gray-300" 
                       readonly>
                <input type="hidden" name="peminjaman_id" value="{{ $selectedPeminjaman->id }}">
            </div>
        @else
            <div>
                <label for="peminjaman_id" class="block text-gray-700 font-semibold mb-1">Pilih Peminjaman</label>
                <select id="peminjaman_id" 
                        name="peminjaman_id" 
                        required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500">
                    <option value="">-- Pilih Peminjaman --</option>
                    @foreach($peminjaman as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->id }} - {{ $item->user->name ?? 'Tidak diketahui' }} ({{ $item->status }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <!-- Persentase Refund -->
        <div>
            <label for="persentase_refund" class="block text-gray-700 font-semibold mb-1">Persentase Refund</label>
            <input type="number" step="0.01" min="0" max="1" name="persentase_refund" id="persentase_refund" required
                   placeholder="Contoh: 0.5 untuk 50%"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500">
            <p class="text-sm text-gray-500 mt-1">Masukkan dalam bentuk desimal, misal 0.5 = 50%.</p>
        </div>

        <!-- Alasan Pembatalan -->
        <div>
            <label for="alasan" class="block text-gray-700 font-semibold mb-1">Alasan Pembatalan</label>
            <textarea id="alasan" name="alasan" rows="4" required
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500"
                      placeholder="Tuliskan alasan pembatalan..."></textarea>
        </div>

        <!-- Tombol Submit -->
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('resepsionis.pembatalan.index') }}" 
               class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
               Batal
            </a>

            <button type="submit" 
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                Konfirmasi Pembatalan
            </button>
        </div>
    </form>
</div>
@endsection
