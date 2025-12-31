@extends('layouts.resepsionis')

@section('content')
    <div class="container mx-auto p-6 mt-20" x-data="{ searchQuery: '', filteredPembatalanPesanan: @js($pembatalanPesanan) }">
        <!-- Card Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-semibold text-gray-800">Pembatalan Pesanan</h1>

            <div class="flex items-center space-x-4">
                <!-- Search Bar -->
                <input type="text" x-model="searchQuery" placeholder="Cari pembatalan pesanan..." class="border border-gray-300 rounded-md px-4 py-2 text-sm w-80">

                <!-- Add New Button -->
                <a href="{{ route('resepsionis.pembatalan.create') }}" class="bg-green-600 text-white rounded-md px-4 py-2 text-sm">Tambah Pembatalan Pesanan</a>
            </div>
        </div>

        <!-- Table of Pembatalan Pesanan -->
        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full table-auto text-sm text-gray-700">
                <thead class="w-auto bg-blue-100">
                    <tr>
                        <th class="px-4 py-2 text-left">No</th>
                        <th class="px-4 py-2 text-left">ID Pembatalan</th>
                        <th class="px-4 py-2 text-left">ID Peminjaman</th>
                        <th class="px-4 py-2 text-left">Alasan</th>
                        <th class="px-4 py-2 text-left">Tanggal Pembatalan</th>
                        <th class="px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loop through filteredPembatalanPesanan -->
                    <template x-for="(pembatalan, index) in filteredPembatalanPesanan.filter(p => p.peminjaman_id.toString().includes(searchQuery.toLowerCase()) || p.alasan.toLowerCase().includes(searchQuery.toLowerCase()))" :key="pembatalan.id">
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2" x-text="index + 1"></td>
                            <td class="px-4 py-2" x-text="pembatalan.id"></td>
                            <td class="px-4 py-2" x-text="pembatalan.peminjaman_id"></td>
                            <td class="px-4 py-2" x-text="pembatalan.alasan"></td>
                            <td class="px-4 py-2" x-text="pembatalan.cancelled_at"></td>
                            <td class="px-4 py-2">
                                <a :href="'/resepsionis/pembatalan/' + pembatalan.id" class="text-blue-500 hover:text-blue-700">Detail</a>
                                <a :href="'/resepsionis/pembatalan/' + pembatalan.id + '/edit'" class="text-yellow-500 hover:text-yellow-700 ml-2">Edit</a>
                                <form :action="'/resepsionis/pembatalan/' + pembatalan.id" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 ml-2">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
@endsection
