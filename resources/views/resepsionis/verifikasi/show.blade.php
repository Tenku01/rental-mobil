@extends('layouts.resepsionis')

@section('header', 'Detail Dokumen Pelanggan')

@section('content')

<div class="container mx-auto">
<!-- Back Navigation -->
<div class="mb-4">
<a href="{{ route('resepsionis.verifikasi.index') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
Kembali ke Antrean
</a>
</div>

<div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden">
    <!-- Header Detail -->
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:justify-between md:items-center bg-gray-50/50 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $identity->user_name }}</h2>
            <p class="text-sm text-gray-500">ID Identitas: #{{ str_pad($identity->id, 5, '0', STR_PAD_LEFT) }} • Diunggah: {{ \Carbon\Carbon::parse($identity->tanggal_upload)->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Status:</span>
            <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-tighter {{ $identity->status_approval == 'disetujui' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : ($identity->status_approval == 'menunggu' ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-rose-100 text-rose-700 border border-rose-200') }}">
                {{ $identity->status_approval }}
            </span>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Foto KTP Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-700 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Kartu Tanda Penduduk (KTP)
                    </h3>
                </div>
                <div class="group relative border-2 border-dashed border-gray-200 rounded-xl overflow-hidden bg-gray-50 aspect-video flex items-center justify-center">
                    @if($identity->ktp)
                        <img src="{{ asset('storage/' . $identity->ktp) }}" alt="KTP" class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105 cursor-zoom-in">
                    @else
                        <div class="text-center p-6">
                            <p class="text-xs text-gray-400 font-medium">Gambar KTP tidak ditemukan</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Foto SIM Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-700 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Surat Izin Mengemudi (SIM)
                    </h3>
                </div>
                <div class="group relative border-2 border-dashed border-gray-200 rounded-xl overflow-hidden bg-gray-50 aspect-video flex items-center justify-center">
                    @if($identity->sim)
                        <img src="{{ asset('storage/' . $identity->sim) }}" alt="SIM" class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105 cursor-zoom-in">
                    @else
                        <div class="text-center p-6">
                            <p class="text-xs text-gray-400 font-medium">Gambar SIM tidak ditemukan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Validation Controls -->
        <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row justify-center items-center gap-4">
            <form action="{{ route('resepsionis.verifikasi.updateStatus', $identity->id) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="ditolak">
                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin MENOLAK identitas ini?')" 
                    class="w-full px-10 py-3 bg-white border-2 border-rose-500 text-rose-500 font-bold rounded-xl hover:bg-rose-50 transition-all focus:ring-4 focus:ring-rose-100 uppercase text-xs tracking-widest">
                    Tolak Dokumen
                </button>
            </form>

            <form action="{{ route('resepsionis.verifikasi.updateStatus', $identity->id) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="disetujui">
                <button type="submit" onclick="return confirm('Setujui identitas ini untuk memungkinkan pelanggan menyewa mobil?')" 
                    class="w-full px-10 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all focus:ring-4 focus:ring-indigo-100 uppercase text-xs tracking-widest">
                    Setujui Identitas
                </button>
            </form>
        </div>
    </div>
</div>


</div>
@endsection