@extends('layouts.resepsionis')

@section('header', 'Detail Mobil: ' . $mobil->tipe . ' ' . $mobil->merek)

@section('content')

    <div class="max-w-4xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Detail Mobil</h1>
            <a href="{{ route('resepsionis.mobil.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition ease-in-out duration-150 shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Kolom Kiri: Foto Mobil -->
                <div class="md:col-span-1">
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">Foto Kendaraan</h2>
                    @if ($mobil->foto)
                        <img src="{{ asset('storage/' . $mobil->foto) }}" 
                             alt="{{ $mobil->tipe }} {{ $mobil->merek }}" 
                             class="w-full h-auto object-cover rounded-xl shadow-md border-4 border-gray-100">
                    @else
                        <div class="w-full h-48 bg-gray-200 rounded-xl flex items-center justify-center text-gray-500 font-medium">
                            <i class="fas fa-camera text-4xl mr-2"></i> Tidak Ada Foto
                        </div>
                    @endif
                </div>

                <!-- Kolom Kanan: Spesifikasi -->
                <div class="md:col-span-2 space-y-4">
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">Spesifikasi Umum</h2>

                    <!-- Tipe & Merek -->
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-medium text-gray-500">Tipe / Merek:</span>
                        <span class="font-bold text-gray-900">{{ $mobil->tipe }} / {{ $mobil->merek }}</span>
                    </div>

                    <!-- Warna -->
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-medium text-gray-500">Warna:</span>
                        <span class="font-bold text-gray-900">{{ $mobil->warna }}</span>
                    </div>

                    <!-- Transmisi -->
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-medium text-gray-500">Transmisi:</span>
                        <span class="font-bold text-gray-900">{{ ucfirst($mobil->transmisi) }}</span>
                    </div>

                    <!-- Jumlah Kursi -->
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-medium text-gray-500">Jumlah Kursi:</span>
                        <span class="font-bold text-gray-900">{{ $mobil->kursi }}</span>
                    </div>

                    <!-- Harga -->
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-medium text-gray-500">Harga Sewa Harian:</span>
                        <span class="font-bold text-lg text-cyan-600">Rp. {{ number_format($mobil->harga, 0, ',', '.') }}</span>
                    </div>

                    <!-- Status -->
                    <div class="pt-4">
                        <span class="font-medium text-gray-500 block mb-2">Status Ketersediaan:</span>
                        @if ($mobil->status == 'tersedia')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-2"></i> Tersedia
                            </span>
                        @elseif ($mobil->status == 'disewa')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-2"></i> Disewa
                            </span>
                        @elseif ($mobil->status == 'pemeliharaan')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-tools mr-2"></i> Pemeliharaan
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-tint mr-2"></i> Dibersihkan
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bagian Footer Aksi -->
            <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end">
                <a href="{{ route('resepsionis.mobil.edit', $mobil) }}" 
                   class="inline-flex items-center px-6 py-3 bg-cyan-600 border border-transparent rounded-lg font-semibold text-white uppercase tracking-widest hover:bg-cyan-700 transition ease-in-out duration-150 shadow-md transform hover:scale-[1.02]">
                    <i class="fas fa-edit mr-2"></i> Edit Data Mobil
                </a>
            </div>

        </div>
    </div>
@endsection