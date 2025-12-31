@extends('layouts.resepsionis')

@section('content')
    <div class="container mx-auto p-6 mt-20" x-data="{ searchQuery: '', filteredPelanggans: @js($pelanggans) }">
        <!-- Card Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-semibold text-gray-800">Pelanggan</h1>

            <div class="flex items-center space-x-4">
                <!-- Search Bar -->
                <input type="text" x-model="searchQuery" placeholder="Cari pelanggan..." class="border border-gray-300 rounded-md px-4 py-2 text-sm w-80">

                
                <!-- Add New Button -->
                <a href="{{ route('resepsionis.pelanggan.create') }}" class="bg-green-600 text-white rounded-md px-4 py-2 text-sm">Tambah Pelanggan</a>
            </div>
        </div>

        <!-- Table of Pelanggan -->
        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full table-auto text-sm text-gray-700">
                <thead class="w-auto bg-blue-100">
                    <tr>
                        <th class="px-4 py-2 text-left">No</th>
                        <th class="px-4 py-2 text-left">Nama</th>
                        <th class="px-4 py-2 text-left">No Telepon</th>
                        <th class="px-4 py-2 text-left">Alamat</th>
                        <th class="px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loop through filteredPelanggans -->
                    <template x-for="(pelanggan, index) in filteredPelanggans.filter(p => p.nama.toLowerCase().includes(searchQuery.toLowerCase()))" :key="pelanggan.id">
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2" x-text="index + 1"></td>
                            <td class="px-4 py-2" x-text="pelanggan.nama"></td>
                            <td class="px-4 py-2" x-text="pelanggan.no_telepon"></td>
                            <td class="px-4 py-2" x-text="pelanggan.alamat"></td>
                            <td class="px-4 py-2">
                                <a :href="'/resepsionis/pelanggan/' + pelanggan.id" class="text-blue-500 hover:text-blue-700">Detail</a>
                                <a :href="'/resepsionis/pelanggan/' + pelanggan.id + '/edit'" class="text-yellow-500 hover:text-yellow-700 ml-2">Edit</a>
                                <form :action="'/resepsionis/pelanggan/' + pelanggan.id" method="POST" class="inline">
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
