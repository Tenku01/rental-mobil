@extends('layouts.resepsionis')

@section('content')
<div class="container mx-auto p-6 mt-10">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6">Tambah Peminjaman Mobil</h1>

    <form action="{{ route('resepsionis.peminjaman.store') }}" method="POST" class="bg-white shadow-lg rounded-lg p-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Pilih User -->
            <div class="form-group">
                <label for="user_id" class="text-sm font-medium text-gray-700">Pilih Pelanggan</label>
                <div x-data="{ open: false, selectedUser: null, selectedName: '-- Pilih Pelanggan --' }" class="relative mt-2">
                    <input type="hidden" name="user_id" x-model="selectedUser">

                    <button 
                        type="button"
                        @click="open = !open"
                        class="text-left w-full p-3 border border-gray-300 rounded-md bg-white text-gray-700 shadow-md focus:ring-2 focus:ring-cyan-500 hover:ring-cyan-500 flex justify-between items-center"
                    >
                        <span x-text="selectedName"></span>
                        <svg class="w-4 h-4 ml-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        class="absolute left-0 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg z-10 max-h-56 overflow-y-auto"
                    >
                        <ul class="py-2 text-sm">
                            @foreach($users as $user)
                                <li>
                                    <button 
                                        type="button"
                                        @click="
                                            selectedUser = '{{ $user->id }}'; 
                                            selectedName = '{{ $user->name }}'; 
                                            open = false;
                                        "
                                        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-cyan-100 hover:text-cyan-600 transition-colors"
                                    >
                                        {{ $user->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Pilih Mobil -->
            <div class="form-group">
                <label for="mobil_id" class="text-sm font-medium text-gray-700">Pilih Mobil</label>
                <div x-data="{ open: false, selectedMobil: null, selectedMobilName: '-- Pilih Mobil --' }" class="relative mt-2">
                    <input type="hidden" name="mobil_id" x-model="selectedMobil">

                    <button 
                        type="button"
                        @click="open = !open"
                        class="text-left w-full p-3 border border-gray-300 rounded-md bg-white text-gray-700 shadow-md focus:ring-2 focus:ring-cyan-500 hover:ring-cyan-500 flex justify-between items-center"
                    >
                        <span x-text="selectedMobilName"></span>
                        <svg class="w-4 h-4 ml-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        class="absolute left-0 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg z-10 max-h-56 overflow-y-auto"
                    >
                        <ul class="py-2 text-sm">
                            @foreach($mobils as $mobil)
                                <li>
                                    <button 
                                        type="button"
                                        @click="
                                            selectedMobil = '{{ $mobil->id }}'; 
                                            selectedMobilName = '{{ $mobil->merek }}'; 
                                            open = false;
                                        "
                                        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-cyan-100 hover:text-cyan-600 transition-colors"
                                    >
                                        {{ $mobil->merek }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tanggal Sewa -->
            <div class="form-group">
                <label for="tanggal_sewa" class="text-sm font-medium text-gray-700">Tanggal Sewa</label>
                <input type="date" name="tanggal_sewa" id="tanggal_sewa" required
                       class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-1 focus:ring-cyan-500 shadow-md hover:shadow-cyan-200 transition">
            </div>

            <!-- Jam Sewa -->
            <div class="form-group">
                <label for="jam_sewa" class="text-sm font-medium text-gray-700">Jam Sewa</label>
                <input type="time" name="jam_sewa" id="jam_sewa" required
                       class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-1 focus:ring-cyan-500 shadow-md hover:shadow-cyan-200 transition">
            </div>

            <!-- Tanggal Kembali -->
            <div class="form-group">
                <label for="tanggal_kembali" class="text-sm font-medium text-gray-700">Tanggal Kembali</label>
                <input type="date" name="tanggal_kembali" id="tanggal_kembali" required
                       class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-1 focus:ring-cyan-500 shadow-md hover:shadow-cyan-200 transition">
            </div>

            <!-- Add On Sopir -->
            <div class="form-group">
                <label for="add_on_sopir" class="text-sm font-medium text-gray-700">Tambahan Sopir</label>
                <select name="add_on_sopir" id="add_on_sopir"
                        class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-1 focus:ring-cyan-500 shadow-md hover:shadow-cyan-200 transition">
                    <option value="0">Tidak</option>
                    <option value="1">Ya</option>
                </select>
            </div>

            <!-- Total Harga -->
            <div class="form-group">
                <label for="total_harga" class="text-sm font-medium text-gray-700">Total Harga (Rp)</label>
                <input type="number" name="total_harga" id="total_harga" min="0" step="1000"
                       class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-1 focus:ring-cyan-500 shadow-md hover:shadow-cyan-200 transition" required>
            </div>

            <!-- DP Dibayarkan -->
            <div class="form-group">
                <label for="dp_dibayarkan" class="text-sm font-medium text-gray-700">DP Dibayarkan (Rp)</label>
                <input type="number" name="dp_dibayarkan" id="dp_dibayarkan" min="0" step="1000"
                       class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-1 focus:ring-cyan-500 shadow-md hover:shadow-cyan-200 transition">
            </div>

            <!-- Metode Pembayaran -->
            <div class="form-group">
                <label for="metode_pembayaran" class="text-sm font-medium text-gray-700">Metode Pembayaran</label>
                <select name="metode_pembayaran" id="metode_pembayaran" required
                        class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-1 focus:ring-cyan-500 shadow-md hover:shadow-cyan-200 transition">
                    <option value="transfer">Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="midtrans">Midtrans (Online)</option>
                </select>
            </div>

            <!-- Tipe Pembayaran -->
            <div class="form-group">
                <label for="tipe_pembayaran" class="text-sm font-medium text-gray-700">Tipe Pembayaran</label>
                <select name="tipe_pembayaran" id="tipe_pembayaran" required
                        class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-1 focus:ring-cyan-500 shadow-md hover:shadow-cyan-200 transition">
                    <option value="dp">DP</option>
                    <option value="lunas">Lunas</option>
                </select>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" required
                        class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-1 focus:ring-cyan-500 shadow-md hover:shadow-cyan-200 transition">
                    <option value="menunggu pembayaran">Menunggu Pembayaran</option>
                    <option value="pembayaran dp">Pembayaran DP</option>
                    <option value="sudah dibayar lunas">Sudah Dibayar Lunas</option>
                    <option value="berlangsung">Berlangsung</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex justify-end gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md text-sm hover:bg-blue-700 hover:shadow-md transition">Simpan</button>
            <a href="{{ route('resepsionis.peminjaman.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded-md text-sm hover:bg-gray-500 hover:shadow-md transition">Batal</a>
        </div>
    </form>
</div>
@endsection
