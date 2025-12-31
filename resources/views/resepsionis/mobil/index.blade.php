@extends('layouts.resepsionis')

@section('header', 'Daftar Kendaraan')

@section('content')

    <div class="space-y-6" x-data>
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Mobil</h1>
            
            <!-- Tombol Tambah Mobil Baru -->
            <a href="{{ route('resepsionis.mobil.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-cyan-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                <i class="fas fa-plus mr-2"></i> Tambah Mobil
            </a>
        </div>

        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Tampilkan Toast jika ada sesi sukses
                    Alpine.store('toast').show('success', '{{ session('success') }}');
                });
            </script>
        @endif
        
        @if ($mobils->isEmpty())
            <div class="text-center p-10 bg-white rounded-xl shadow-lg">
                <i class="fas fa-car-crash text-6xl text-gray-300 mb-4"></i>
                <p class="text-xl font-medium text-gray-500">Belum ada data mobil yang terdaftar.</p>
                <a href="{{ route('resepsionis.mobil.create') }}" class="mt-4 inline-block text-cyan-600 hover:text-cyan-800">Tambahkan mobil sekarang</a>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Foto
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipe / Merek
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Spesifikasi
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Harga Harian
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            
                            @foreach ($mobils as $mobil)
                                <!-- Tambahkan kelas hover di sini -->
                                <tr class="hover:bg-cyan-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if ($mobil->foto)
                                                <img class="h-10 w-10 rounded-lg object-cover" 
                                                     src="{{ asset('storage/' . $mobil->foto) }}" 
                                                     alt="{{ $mobil->tipe }}">
                                            @else
                                                <!-- Placeholder jika tidak ada foto -->
                                                <img class="h-10 w-10 rounded-lg object-cover bg-gray-200 p-1" 
                                                     src="https://placehold.co/100x100/A0E7E5/083344?text=No+Img" 
                                                     alt="{{ $mobil->tipe }}">
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $mobil->tipe }}</div>
                                        <div class="text-xs text-gray-500">{{ $mobil->merek }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $mobil->kursi }} Kursi, {{ ucfirst($mobil->transmisi) }}, {{ $mobil->warna }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        Rp. {{ number_format($mobil->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($mobil->status == 'tersedia')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Tersedia
                                            </span>
                                        @elseif ($mobil->status == 'disewa')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Disewa
                                            </span>
                                        @elseif ($mobil->status == 'pemeliharaan')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pemeliharaan
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Dibersihkan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        
                                        <!-- Tombol Detail (BARU DITAMBAHKAN) -->
                                        <a href="{{ route('resepsionis.mobil.show', $mobil) }}" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-info-circle"></i> Detail
                                        </a>

                                        <!-- Tombol Edit (TETAP ADA) -->
                                        <a href="{{ route('resepsionis.mobil.edit', $mobil) }}" class="text-cyan-600 hover:text-cyan-800 ml-2">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <!-- Tombol Hapus dan Form Tersembunyi DIHAPUS -->
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Bagian Paginasi -->
                <div class="p-6">
                    {{ $mobils->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection