@extends('layouts.resepsionis')

@section('content')
    <div class="container mx-auto p-6 mt-10">
        <h1 class="text-3xl font-semibold text-gray-800 mb-6">Detail Pembatalan Pesanan</h1>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <p><strong>ID Pembatalan:</strong> {{ $pembatalanPesanan->id }}</p>
            <p><strong>ID Peminjaman:</strong> {{ $pembatalanPesanan->peminjaman_id }}</p>
            <p><strong>Alasan Pembatalan:</strong> {{ $pembatalanPesanan->alasan }}</p>
            <p><strong>Status Refund:</strong> {{ $pembatalanPesanan->refund_status }}</p>
            <p><strong>Tanggal Pembatalan:</strong> {{ $pembatalanPesanan->cancelled_at }}</p>
        </div>

        <div class="mt-6">
            <a href="{{ route('resepsionis.pembatalan.index') }}" class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600">Kembali ke Daftar Pembatalan</a>
        </div>
    </div>
@endsection
