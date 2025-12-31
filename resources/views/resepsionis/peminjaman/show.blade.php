@extends('layouts.resepsionis')

@section('content')
<div class="container mx-auto p-6 mt-20 bg-white shadow rounded-lg">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6">
        Detail Peminjaman Mobil
    </h1>

    <div class="grid grid-cols-2 gap-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Informasi Pengguna</h2>
            <p><span class="font-medium">Nama:</span> {{ $peminjaman->user->name ?? '-' }}</p>
            <p><span class="font-medium">Email:</span> {{ $peminjaman->user->email ?? '-' }}</p>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Informasi Mobil</h2>
            <p><span class="font-medium">Merek:</span> {{ $peminjaman->mobil->merek ?? '-' }}</p>
            <p><span class="font-medium">Nomor Polisi:</span> {{ $peminjaman->mobil->no_polisi ?? '-' }}</p>
        </div>
    </div>

    <div class="mt-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Detail Peminjaman</h2>
        <div class="grid grid-cols-2 gap-4">
            <p><span class="font-medium">Tanggal Sewa:</span> {{ $peminjaman->tanggal_sewa }}</p>
            <p><span class="font-medium">Tanggal Kembali:</span> {{ $peminjaman->tanggal_kembali }}</p>
            <p><span class="font-medium">Total Harga:</span> Rp {{ number_format($peminjaman->total_harga, 0, ',', '.') }}</p>
            <p><span class="font-medium">Total Dibayarkan:</span> Rp {{ number_format($peminjaman->total_dibayarkan, 0, ',', '.') }}</p>
            <p><span class="font-medium">Status:</span> 
                <span class="px-2 py-1 rounded text-white text-xs
                    @if($peminjaman->status == 'menunggu pembayaran') bg-yellow-500
                    @elseif($peminjaman->status == 'berlangsung') bg-blue-500
                    @elseif($peminjaman->status == 'selesai') bg-green-600
                    @elseif($peminjaman->status == 'dibatalkan') bg-gray-500
                    @endif">
                    {{ ucfirst($peminjaman->status) }}
                </span>
            </p>
            <p><span class="font-medium">Metode Pembayaran:</span> {{ $peminjaman->metode_pembayaran ?? '-' }}</p>
            <p><span class="font-medium">Tipe Pembayaran:</span> {{ strtoupper($peminjaman->tipe_pembayaran ?? '-') }}</p>
        </div>
    </div>

    <div class="mt-8 flex justify-between">
        <a href="{{ route('resepsionis.peminjaman.index') }}" 
           class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
           ← Kembali
        </a>

       <button 
    type="button"
    onclick="konfirmasiPembatalan({{ $peminjaman->id }})"
    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
    Batalkan Peminjaman
</button>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function konfirmasiPembatalan(peminjamanId) {
        Swal.fire({
            title: 'Konfirmasi Pembatalan',
            text: 'Apakah Anda yakin ingin membatalkan peminjaman ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke form pembatalan dengan ID otomatis terisi
                window.location.href = `/resepsionis/pembatalan/create?peminjaman_id=${peminjamanId}`;
            }
        });
    }
</script>
@endpush
@endsection
