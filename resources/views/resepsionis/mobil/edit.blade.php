@extends('layouts.resepsionis')

@section('header', 'Edit Mobil: ' . $mobil->tipe . ' ' . $mobil->merek)

@section('content')

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Perbarui Data Kendaraan</h1>

        <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">

            <form action="{{ route('resepsionis.mobil.update', $mobil) }}" 
                method="POST" 
                enctype="multipart/form-data" 
                x-data
                @submit.prevent="
                    $swal.fire({
                        title: 'Konfirmasi Perubahan?',
                        text: 'Apakah Anda yakin ingin menyimpan perubahan data mobil ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Perbarui',
                        cancelButtonText: 'Batal',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'bg-cyan-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-cyan-700 mx-2',
                            cancelButton: 'bg-gray-300 text-gray-800 px-4 py-2 rounded-lg shadow-md hover:bg-gray-400 mx-2'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $el.submit(); // Lanjutkan submit form
                        }
                    })
                "
                class="space-y-6"
            >

                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- 1. Tipe --}}
                    <div>
                        <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe (Contoh: Avanza)</label>
                        <input type="text" name="tipe" id="tipe" value="{{ old('tipe', $mobil->tipe) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500 @error('tipe') border-red-500 @enderror" required>
                        @error('tipe') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- 2. Merek --}}
                    <div>
                        <label for="merek" class="block text-sm font-medium text-gray-700 mb-1">Merek (Contoh: Toyota)</label>
                        <input type="text" name="merek" id="merek" value="{{ old('merek', $mobil->merek) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500 @error('merek') border-red-500 @enderror" required>
                        @error('merek') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- 3. Warna --}}
                    <div>
                        <label for="warna" class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                        <input type="text" name="warna" id="warna" value="{{ old('warna', $mobil->warna) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500 @error('warna') border-red-500 @enderror" required>
                        @error('warna') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- 4. Transmisi --}}
                    <div>
                        <label for="transmisi" class="block text-sm font-medium text-gray-700 mb-1">Transmisi</label>
                        <select name="transmisi" id="transmisi" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500 @error('transmisi') border-red-500 @enderror" required>
                            <option value="manual" {{ old('transmisi', $mobil->transmisi) == 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="otomatis" {{ old('transmisi', $mobil->transmisi) == 'otomatis' ? 'selected' : '' }}>Otomatis</option>
                        </select>
                        @error('transmisi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- 5. Jumlah Kursi --}}
                    <div>
                        <label for="kursi" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kursi</label>
                        <select name="kursi" id="kursi" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500 @error('kursi') border-red-500 @enderror" required>
                            <option value="5" {{ old('kursi', $mobil->kursi) == 5 ? 'selected' : '' }}>5 Kursi</option>
                            <option value="7" {{ old('kursi', $mobil->kursi) == 7 ? 'selected' : '' }}>7 Kursi</option>
                            <option value="9" {{ old('kursi', $mobil->kursi) == 9 ? 'selected' : '' }}>9 Kursi</option>
                        </select>
                        @error('kursi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- 6. Harga --}}
                    <div>
                        <label for="harga" class="block text-sm font-medium text-gray-700 mb-1">Harga Sewa per Hari (IDR)</label>
                        <input type="number" name="harga" id="harga" value="{{ old('harga', $mobil->harga) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500 @error('harga') border-red-500 @enderror" required min="10000">
                        @error('harga') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- 7. Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Ketersediaan</label>
                        <select name="status" id="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500 @error('status') border-red-500 @enderror">
                            <option value="tersedia" {{ old('status', $mobil->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="disewa" {{ old('status', $mobil->status) == 'disewa' ? 'selected' : '' }}>Disewa</option>
                            <option value="pemeliharaan" {{ old('status', $mobil->status) == 'pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
                            <option value="dibersihkan" {{ old('status', $mobil->status) == 'dibersihkan' ? 'selected' : '' }}>Dibersihkan</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    {{-- 8. Foto Mobil (full width) --}}
                    <div class="md:col-span-2">
                        <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto Mobil (Kosongkan jika tidak ingin diubah)</label>
                        
                        @if ($mobil->foto)
                            <p class="text-xs text-gray-500 mb-2">Foto saat ini:</p>
                            <img src="{{ asset('storage/' . $mobil->foto) }}" alt="{{ $mobil->tipe }}" class="w-32 h-20 object-cover rounded-lg shadow mb-4">
                        @endif
                        
                        <input type="file" name="foto" id="foto" class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-cyan-50 file:text-cyan-700
                                hover:file:bg-cyan-100
                                focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2
                            ">
                        <p class="mt-1 text-xs text-gray-500">Maksimal 2MB, format JPG atau PNG.</p>
                        @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>
                
                <!-- Tombol Submit -->
                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" class="w-full md:w-auto bg-cyan-600 hover:bg-cyan-700 text-white font-semibold px-6 py-3 rounded-lg shadow-lg transition duration-150 transform hover:scale-[1.02]">
                        <i class="fas fa-sync-alt mr-2"></i> Perbarui Data
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection