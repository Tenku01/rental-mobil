@extends('layouts.resepsionis')

@section('header', 'Tambah Mobil Baru')

@section('content')

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Registrasi Kendaraan Baru</h1>

        <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">

            <form action="{{ route('resepsionis.mobil.store') }}" 
                method="POST" 
                enctype="multipart/form-data" 
                x-data
                @submit.prevent="
                    $swal.fire({
                        title: 'Konfirmasi Simpan?',
                        text: 'Apakah data mobil yang dimasukkan sudah benar?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Simpan',
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
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- 1. Tipe --}}
                    <div>
                        <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe (Contoh: Avanza)</label>
                        <input type="text" name="tipe" id="tipe" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500" required>
                    </div>

                    {{-- 2. Merek --}}
                    <div>
                        <label for="merek" class="block text-sm font-medium text-gray-700 mb-1">Merek (Contoh: Toyota)</label>
                        <input type="text" name="merek" id="merek" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500" required>
                    </div>

                    {{-- 3. Warna --}}
                    <div>
                        <label for="warna" class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                        <input type="text" name="warna" id="warna" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500" required>
                    </div>

                    {{-- 4. Transmisi --}}
                    <div>
                        <label for="transmisi" class="block text-sm font-medium text-gray-700 mb-1">Transmisi</label>
                        <select name="transmisi" id="transmisi" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500" required>
                            <option value="manual">Manual</option>
                            <option value="otomatis">Otomatis</option>
                        </select>
                    </div>

                    {{-- 5. Jumlah Kursi --}}
                    <div>
                        <label for="kursi" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kursi</label>
                        <select name="kursi" id="kursi" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500" required>
                            <option value="5">5 Kursi</option>
                            <option value="7">7 Kursi</option>
                            <option value="9">9 Kursi</option>
                        </select>
                    </div>

                    {{-- 6. Harga --}}
                    <div>
                        <label for="harga" class="block text-sm font-medium text-gray-700 mb-1">Harga Sewa per Hari (IDR)</label>
                        <input type="number" name="harga" id="harga" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500" required min="10000">
                    </div>

                    {{-- 7. Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Ketersediaan</label>
                        <select name="status" id="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                            <option value="tersedia">Tersedia</option>
                            <option value="disewa">Disewa</option>
                            <option value="pemeliharaan">Pemeliharaan</option>
                            <option value="dibersihkan">Dibersihkan</option>
                        </select>
                    </div>
                    
                    {{-- 8. Foto Mobil (full width) --}}
                    <div class="md:col-span-2">
                        <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto Mobil</label>
                        <input type="file" name="foto" id="foto" class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-cyan-50 file:text-cyan-700
                                hover:file:bg-cyan-100
                                focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2
                            ">
                        <p class="mt-1 text-xs text-gray-500">Maksimal 2MB, format JPG atau PNG.</p>
                    </div>

                </div>
                
                <!-- Tombol Submit -->
                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" class="w-full md:w-auto bg-cyan-600 hover:bg-cyan-700 text-white font-semibold px-6 py-3 rounded-lg shadow-lg transition duration-150 transform hover:scale-[1.02]">
                        <i class="fas fa-save mr-2"></i> Simpan Mobil
                    </button>
                </div>

            </form>
        </div>
    </div>
    
    {{-- Notifikasi Toast (jika ada pesan sukses atau error setelah redirect) --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Gunakan Alpine.js store untuk menampilkan Toast
                Alpine.store('toast').show('success', '{{ session('success') }}');
            });
        </script>
    @endif
@endsection

<script>
    // --- Alpine.js Toast Store Initialization (Jika belum ada) ---
    // Dipastikan ini hanya berjalan jika Alpine.js tersedia
    document.addEventListener('alpine:init', () => {
        if (!Alpine.store('toast')) {
            Alpine.store('toast', {
                visible: false,
                message: '',
                type: 'success', // success, error, warning
                timeout: null,

                show(type, message, duration = 3000) {
                    clearTimeout(this.timeout);
                    this.type = type;
                    this.message = message;
                    this.visible = true;

                    this.timeout = setTimeout(() => {
                        this.visible = false;
                    }, duration);
                }
            });
        }
    });

    // --- SweetAlert/Toast Library Mockup (Hanya untuk demonstrasi) ---
    // Di lingkungan nyata, Anda perlu memasukkan library SweetAlert2 atau serupa.
    document.addEventListener('alpine:init', () => {
        Alpine.magic('swal', () => {
            return {
                fire(options) {
                    // Mockup SweetAlert: Di lingkungan nyata, ini akan memanggil SweetAlert2.
                    return new Promise(resolve => {
                        if (confirm(options.title + '\n' + options.text)) {
                            resolve({ isConfirmed: true });
                        } else {
                            resolve({ isConfirmed: false });
                        }
                    });
                }
            }
        });
    });
</script>