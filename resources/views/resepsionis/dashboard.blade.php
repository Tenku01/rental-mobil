@extends('layouts.resepsionis')

@section('content')
<div class="container mx-auto px-6 py-8">
    
    <!-- Welcome Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Overview</h1>
            <p class="text-gray-500 mt-1">Selamat datang kembali, {{ Auth::user()->name }}! Berikut ringkasan operasional hari ini.</p>
        </div>
        <div class="hidden md:block">
            <span class="bg-cyan-100 text-cyan-800 text-sm font-medium px-4 py-2 rounded-lg shadow-sm border border-cyan-200">
                📅 {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
            </span>
        </div>
    </div>

    <!-- STATISTIK UTAMA (GRID 4 KOLOM) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Card 1: Total Armada -->
        <a href="{{ route('resepsionis.mobil.index') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-cyan-200 transition duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 group-hover:text-cyan-600 transition">Total Armada</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalMobil }}</p>
                </div>
                <div class="bg-cyan-50 p-3 rounded-lg text-cyan-600 group-hover:bg-cyan-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-gray-400">
                <span class="text-green-500 font-bold mr-1">●</span> Siap Disewa
            </div>
        </a>

        <!-- Card 2: Peminjaman Berlangsung -->
        <a href="{{ route('resepsionis.peminjaman.index', ['status' => 'berlangsung']) }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-blue-200 transition duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 group-hover:text-blue-600 transition">Sedang Disewa</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $peminjamanBerlangsung }}</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-gray-400">
                <span class="text-blue-500 font-bold mr-1">●</span> Unit Keluar
            </div>
        </a>

        <!-- Card 3: Pesanan Baru -->
        <a href="{{ route('resepsionis.peminjaman.index', ['status' => 'menunggu pembayaran']) }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-yellow-200 transition duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 group-hover:text-yellow-600 transition">Pesanan Baru</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $peminjamanBaru }}</p>
                </div>
                <div class="bg-yellow-50 p-3 rounded-lg text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-gray-400">
                <span class="text-yellow-500 font-bold mr-1">●</span> Menunggu Pembayaran
            </div>
        </a>

        <!-- Card 4: Pelanggan -->
        <a href="{{ route('resepsionis.pelanggan.index') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-purple-200 transition duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 group-hover:text-purple-600 transition">Total Pelanggan</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalPelanggan }}</p>
                </div>
                <div class="bg-purple-50 p-3 rounded-lg text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-gray-400">
                <span class="text-purple-500 font-bold mr-1">●</span> Terdaftar
            </div>
        </a>

    </div>

    <!-- SECTION: STATUS SEKUNDER -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Tabel Transaksi Terbaru -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-700">Penyewaan Terbaru</h3>
                <a href="{{ route('resepsionis.peminjaman.index') }}" class="text-sm text-cyan-600 hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Pelanggan</th>
                            <th class="px-6 py-3">Mobil</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentPeminjaman as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-900">
                                {{ $item->user->name ?? 'Guest' }}
                            </td>
                            <td class="px-6 py-3 text-gray-600">
                                {{ $item->mobil->merek ?? '-' }} <span class="text-xs bg-gray-100 px-1 rounded border">{{ $item->mobil_id }}</span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                @php
                                    $statusClass = match($item->status) {
                                        'menunggu pembayaran' => 'bg-yellow-100 text-yellow-800',
                                        'pembayaran dp' => 'bg-indigo-100 text-indigo-800',
                                        'sudah dibayar lunas' => 'bg-blue-100 text-blue-800',
                                        'berlangsung' => 'bg-green-100 text-green-800',
                                        'selesai' => 'bg-gray-100 text-gray-600',
                                        'dibatalkan' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right text-gray-500">
                                {{ \Carbon\Carbon::parse($item->tanggal_sewa)->format('d M') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">Belum ada transaksi terbaru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section: Pembatalan & Summary -->
        <div class="space-y-6">
            
            <!-- Card Pembatalan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:border-red-200 transition">
                <div class="flex items-center gap-4">
                    <div class="bg-red-50 text-red-600 p-4 rounded-full">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-gray-500 font-medium">Pembatalan Pesanan</h3>
                        <p class="text-sm text-gray-400 mt-1">Total: <span class="font-bold text-gray-800">{{ $totalPembatalan }}</span></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="block text-3xl font-bold text-red-600">{{ $pendingPembatalan }}</span>
                    <span class="text-xs text-red-500 font-medium">Menunggu Approval</span>
                </div>
            </div>

            <!-- Card Selesai -->
             <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:border-green-200 transition">
                <div class="flex items-center gap-4">
                    <div class="bg-green-50 text-green-600 p-4 rounded-full">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-gray-500 font-medium">Transaksi Selesai</h3>
                        <p class="text-sm text-gray-400 mt-1">Total unit kembali</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="block text-3xl font-bold text-green-600">{{ $peminjamanSelesai }}</span>
                    <span class="text-xs text-green-500 font-medium">Sukses</span>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection