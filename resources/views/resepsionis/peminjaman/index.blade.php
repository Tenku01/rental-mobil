@extends('layouts.resepsionis')

@section('content')
    <div class="container mx-auto p-6 mt-8" 
         x-data="{ 
            searchQuery: '', 
            // FIX: Menggunakan getCollection() untuk mendapatkan data per halaman
            peminjamanData: @js($peminjaman->getCollection()), 
            // Ambil index awal dari paginator untuk penomoran yang benar
            startId: {{ $peminjaman->firstItem() ? $peminjaman->firstItem() - 1 : 0 }}
        }">

        <!-- Header -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 pb-4 border-b border-gray-200">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-4 md:mb-0">Daftar Peminjaman Mobil</h1>

            <div class="flex flex-col md:flex-row items-stretch md:items-center space-y-3 md:space-y-0 md:space-x-4 w-full md:w-auto">
                <!-- Search Bar -->
                <input type="text" x-model="searchQuery" placeholder="Cari nama pengguna..."
                    class="border border-gray-300 rounded-xl px-4 py-2 text-sm w-full md:w-80 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 transition duration-150">

                <!-- Add New Button -->
                <a href="{{ route('resepsionis.peminjaman.create') }}"
                    class="bg-cyan-600 text-white rounded-xl px-6 py-2 text-sm font-semibold hover:bg-cyan-700 transition duration-300 shadow-lg flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span>Tambah Peminjaman</span>
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded-xl shadow-2xl border border-gray-100">
            <table class="min-w-full table-auto text-sm text-gray-700">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">No</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Nama Pengguna</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Mobil (Plat)</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Tgl Sewa</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Tgl Kembali</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Total Harga</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Status</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template
                        x-for="(item, index) in peminjamanData.filter(p => 
                            p.user.name.toLowerCase().includes(searchQuery.toLowerCase())
                        )"
                        :key="item.id">

                        <tr class="hover:bg-cyan-50 transition duration-150">
                            <!-- Penomoran yang benar dengan pagination -->
                            <td class="px-4 py-3 font-semibold" x-text="searchQuery === '' ? index + startId + 1 : index + 1"></td>
                            
                            <!-- Nama Pengguna -->
                            <td class="px-4 py-3 font-medium text-gray-900" x-text="item.user.name"></td>
                            
                            <!-- Mobil -->
                            <td class="px-4 py-3">
                                <span x-text="item.mobil.merek"></span>
                                <span class="text-xs text-gray-500 ml-1" x-text="'(' + item.mobil.plat_nomor + ')'"></span>
                            </td>
                            
                            <!-- Tanggal Sewa -->
                            <td class="px-4 py-3" x-text="new Date(item.tanggal_sewa).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' })"></td>
                            
                            <!-- Tanggal Kembali -->
                            <td class="px-4 py-3" x-text="new Date(item.tanggal_kembali).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' })"></td>
                            
                            <!-- Total Harga -->
                            <td class="px-4 py-3 font-bold" x-text="'Rp ' + Number(item.total_harga).toLocaleString('id-ID')"></td>
                            
                            <!-- Status Badge -->
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full text-white text-xs font-semibold shadow-sm"
                                    :class="{
                                            'bg-yellow-500': item.status === 'menunggu pembayaran',
                                            'bg-blue-500': item.status === 'berlangsung',
                                            'bg-green-600': item.status === 'selesai',
                                            'bg-red-500': item.status === 'dibatalkan',
                                            'bg-purple-500': item.status === 'diproses' 
                                        }"
                                    x-text="item.status.toUpperCase()">
                                </span>
                            </td>
                            
                            <!-- Aksi -->
                            <td class="px-4 py-3 space-x-2 flex items-center">
                                <!-- Tombol Detail -->
                                <a :href="'/resepsionis/peminjaman/' + item.id"
                                    class="px-3 py-1 rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition text-xs font-semibold">
                                    Detail
                                </a>

                                <!-- Tombol Edit (Hanya tampil jika belum selesai/dibatalkan) -->
                                <template x-if="item.status !== 'selesai' && item.status !== 'dibatalkan'">
                                    <a :href="'/resepsionis/peminjaman/' + item.id + '/edit'"
                                        class="px-3 py-1 rounded-lg text-white bg-yellow-500 hover:bg-yellow-600 transition text-xs font-semibold">
                                        Edit
                                    </a>
                                </template>

                                <!-- Tombol Batalkan (Hanya tampil jika sedang berlangsung/menunggu) -->
                                <template x-if="item.status === 'berlangsung' || item.status === 'menunggu pembayaran'">
                                    <a :href="'/resepsionis/pembatalan/create?peminjaman_id=' + item.id"
                                        class="px-3 py-1 rounded-lg text-white bg-red-600 hover:bg-red-700 transition text-xs font-semibold">
                                        Batalkan
                                    </a>
                                </template>

                               
                            </td>

                        </tr>
                    </template>
                    
                    <template x-if="peminjamanData.filter(p => p.user.name.toLowerCase().includes(searchQuery.toLowerCase())).length === 0">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">Tidak ada data peminjaman yang ditemukan.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-6">
            {{ $peminjaman->links() }}
        </div>
        
    </div>
@endsection