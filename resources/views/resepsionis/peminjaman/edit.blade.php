@extends('layouts.resepsionis')

@section('content')
    <div class="container mx-auto p-6 mt-20">
        <h1 class="text-3xl font-semibold text-gray-800 mb-6">Edit Data Peminjaman</h1>

        <form action="{{ route('resepsionis.peminjaman.update', $peminjaman->id) }}" method="POST" 
              class="bg-white shadow-lg rounded-lg p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- User -->
                <div class="form-group">
                    <label for="user_id" class="text-sm font-medium text-gray-700">User</label>
                    <div x-data="{ open: false, selectedUser: '{{ $peminjaman->user->name }}' }" class="relative mt-2">
                        <button type="button"
                            @click="open = !open"
                            class="text-left w-full p-3 border border-gray-300 rounded-md bg-white text-gray-700 shadow-md focus:ring-2 focus:ring-cyan-500 hover:ring-cyan-500 focus:outline-none">
                            <span x-text="selectedUser || '--Pilih User--'"></span>
                        </button>

                        <input type="hidden" name="user_id" :value="selectedUserId">

                        <div x-show="open"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            @click.away="open = false"
                            class="absolute left-0 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg z-10 max-h-48 overflow-y-auto">
                            <ul class="py-2 text-sm">
                                @foreach($users as $user)
                                    <li>
                                        <button type="button"
                                            @click="selectedUser = '{{ $user->name }}'; selectedUserId = {{ $user->id }}; open = false"
                                            class="w-full text-left px-4 py-2 text-gray-700 hover:bg-cyan-100 hover:text-cyan-600 transition-colors">
                                            {{ $user->name }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Mobil -->
                <div class="form-group">
                    <label for="mobil_id" class="text-sm font-medium text-gray-700">Mobil</label>
                    <div x-data="{ open: false, selectedMobil: '{{ $peminjaman->mobil->merek }}', selectedMobilId: {{ $peminjaman->mobil_id }} }" class="relative mt-2">
                        <button type="button"
                            @click="open = !open"
                            class="text-left w-full p-3 border border-gray-300 rounded-md bg-white text-gray-700 shadow-md focus:ring-2 focus:ring-cyan-500 hover:ring-cyan-500 focus:outline-none">
                            <span x-text="selectedMobil || '--Pilih Mobil--'"></span>
                        </button>

                        <input type="hidden" name="mobil_id" :value="selectedMobilId">

                        <div x-show="open"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            @click.away="open = false"
                            class="absolute left-0 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg z-10 max-h-48 overflow-y-auto">
                            <ul class="py-2 text-sm">
                                @foreach($mobils as $mobil)
                                    <li>
                                        <button type="button"
                                            @click="selectedMobil = '{{ $mobil->merek }}'; selectedMobilId = {{ $mobil->id }}; open = false"
                                            class="w-full text-left px-4 py-2 text-gray-700 hover:bg-cyan-100 hover:text-cyan-600 transition-colors">
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
                    <input type="date" name="tanggal_sewa" id="tanggal_sewa"
                        value="{{ $peminjaman->tanggal_sewa }}"
                        class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-2 focus:ring-cyan-500">
                </div>

                <!-- Jam Sewa -->
                <div class="form-group">
                    <label for="jam_sewa" class="text-sm font-medium text-gray-700">Jam Sewa</label>
                    <input type="time" name="jam_sewa" id="jam_sewa"
                        value="{{ $peminjaman->jam_sewa }}"
                        class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-2 focus:ring-cyan-500">
                </div>

                <!-- Tanggal Kembali -->
                <div class="form-group">
                    <label for="tanggal_kembali" class="text-sm font-medium text-gray-700">Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali" id="tanggal_kembali"
                        value="{{ $peminjaman->tanggal_kembali }}"
                        class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-2 focus:ring-cyan-500">
                </div>

                <!-- Add-on Sopir -->
                <div class="form-group">
                    <label for="add_on_sopir" class="text-sm font-medium text-gray-700">Tambahan Sopir</label>
                    <select name="add_on_sopir" id="add_on_sopir" class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-2 focus:ring-cyan-500">
                        <option value="0" {{ $peminjaman->add_on_sopir == 0 ? 'selected' : '' }}>Tidak</option>
                        <option value="1" {{ $peminjaman->add_on_sopir == 1 ? 'selected' : '' }}>Ya</option>
                    </select>
                </div>

                <!-- Total Harga -->
                <div class="form-group">
                    <label for="total_harga" class="text-sm font-medium text-gray-700">Total Harga</label>
                    <input type="number" name="total_harga" id="total_harga"
                        value="{{ $peminjaman->total_harga }}"
                        class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-2 focus:ring-cyan-500">
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-2 focus:ring-cyan-500">
                        <option value="menunggu pembayaran" {{ $peminjaman->status == 'menunggu pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="pembayaran dp" {{ $peminjaman->status == 'pembayaran dp' ? 'selected' : '' }}>Pembayaran DP</option>
                        <option value="sudah dibayar lunas" {{ $peminjaman->status == 'sudah dibayar lunas' ? 'selected' : '' }}>Sudah Dibayar Lunas</option>
                        <option value="berlangsung" {{ $peminjaman->status == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                        <option value="selesai" {{ $peminjaman->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ $peminjaman->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <!-- Metode Pembayaran -->
                <div class="form-group">
                    <label for="metode_pembayaran" class="text-sm font-medium text-gray-700">Metode Pembayaran</label>
                    <input type="text" name="metode_pembayaran" id="metode_pembayaran"
                        value="{{ $peminjaman->metode_pembayaran }}"
                        class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-2 focus:ring-cyan-500">
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-6 flex justify-end gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md text-sm hover:bg-blue-700">Update</button>
                <a href="{{ route('resepsionis.peminjaman.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded-md text-sm hover:bg-gray-500">Batal</a>
            </div>
        </form>
    </div>
@endsection
