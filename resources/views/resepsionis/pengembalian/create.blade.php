@extends('layouts.resepsionis')

@section('header', 'Tambah Pengembalian')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-8">
        <form action="{{ route('resepsionis.pengembalian.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-1">
                <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Pilih Peminjaman (Status: Berlangsung)</label>
                <select name="peminjaman_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-cyan-500 outline-none text-sm">
                    <option value="">-- Pilih Transaksi --</option>
                    @foreach($peminjamans as $p)
                        <option value="{{ $p->id }}">{{ $p->id }} - {{ $p->user->name }} ({{ $p->mobil_id }})</option>
                    @endforeach
                </select>
                @error('peminjaman_id') <p class="text-red-500 text-[10px] mt-1 italic">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Tanggal Kembali</label>
                    <input type="datetime-local" name="tanggal_pengembalian" value="{{ now()->format('Y-m-d\TH:i') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-cyan-500 outline-none text-sm">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Status Unit</label>
                    <select name="status" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-cyan-500 outline-none text-sm">
                        <option value="Selesai">Selesai</option>
                        <option value="Perlu Inspeksi">Perlu Inspeksi</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('resepsionis.pengembalian.index') }}" class="px-6 py-3 text-gray-400 font-bold uppercase text-[10px] tracking-widest">Batal</a>
                <button type="submit" class="px-8 py-3 bg-cyan-700 text-white font-black uppercase text-[10px] tracking-widest rounded-xl shadow-lg hover:bg-cyan-800 transition-all">
                    Simpan Pengembalian
                </button>
            </div>
        </form>
    </div>
</div>
@endsection