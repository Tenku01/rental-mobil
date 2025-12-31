@extends('layouts.resepsionis')

@section('content')
<div class="container mx-auto px-4 py-6">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $title }}</h1>

    {{-- GRID STATISTIK --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Total Mobil --}}
        <a href="{{ route('resepsionis.mobil.index') }}"
           class="block bg-white shadow rounded-xl p-6 hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <div class="bg-teal-100 text-teal-600 p-3 rounded-full text-xl">🚗</div>
                <div>
                    <p class="text-gray-500 text-sm">Total Mobil</p>
                    <h2 class="text-3xl font-bold">{{ $totalMobil }}</h2>
                </div>
            </div>
        </a>

        {{-- Total Pelanggan --}}
        <a href="{{ route('resepsionis.pelanggan.index') }}"
           class="block bg-white shadow rounded-xl p-6 hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <div class="bg-purple-100 text-purple-600 p-3 rounded-full text-xl">🧑‍🤝‍🧑</div>
                <div>
                    <p class="text-gray-500 text-sm">Total Pelanggan</p>
                    <h2 class="text-3xl font-bold">{{ $totalPelanggan }}</h2>
                </div>
            </div>
        </a>

        {{-- Total Peminjaman --}}
        <a href="{{ route('resepsionis.peminjaman.index') }}"
           class="block bg-white shadow rounded-xl p-6 hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full text-xl">📦</div>
                <div>
                    <p class="text-gray-500 text-sm">Total Peminjaman</p>
                    <h2 class="text-3xl font-bold">{{ $totalPeminjaman }}</h2>
                </div>
            </div>
        </a>

        {{-- Peminjaman Berlangsung --}}
        <a href="{{ route('resepsionis.peminjaman.index', ['status' => 'berlangsung']) }}"
           class="block bg-white shadow rounded-xl p-6 hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <div class="bg-green-100 text-green-600 p-3 rounded-full text-xl">🔄</div>
                <div>
                    <p class="text-gray-500 text-sm">Berlangsung</p>
                    <h2 class="text-3xl font-bold">{{ $peminjamanBerlangsung }}</h2>
                </div>
            </div>
        </a>

        {{-- Peminjaman Selesai --}}
        <a href="{{ route('resepsionis.peminjaman.index', ['status' => 'selesai']) }}"
           class="block bg-white shadow rounded-xl p-6 hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-full text-xl">✔️</div>
                <div>
                    <p class="text-gray-500 text-sm">Selesai</p>
                    <h2 class="text-3xl font-bold">{{ $peminjamanSelesai }}</h2>
                </div>
            </div>
        </a>

        {{-- Total Pembatalan --}}
        <a href="{{ route('resepsionis.pembatalan.index') }}"
           class="block bg-white shadow rounded-xl p-6 hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <div class="bg-red-100 text-red-600 p-3 rounded-full text-xl">❌</div>
                <div>
                    <p class="text-gray-500 text-sm">Total Pembatalan</p>
                    <h2 class="text-3xl font-bold">{{ $totalPembatalan }}</h2>
                </div>
            </div>
        </a>

    </div>

</div>
@endsection
