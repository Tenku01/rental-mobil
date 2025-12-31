<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pesanan') }}
        </h2>
    </x-slot>
    <div class="max-w-3xl mx-auto mt-10 bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">{{ $mobil->merek }} - {{ $mobil->tipe }}</h2>
        <p><strong>Warna:</strong> {{ $mobil->warna }}</p>
        <p><strong>Transmisi:</strong> {{ ucfirst($mobil->transmisi) }}</p>
        <p><strong>Kursi:</strong> {{ $mobil->kursi }}</p>
        <p><strong>Harga:</strong> Rp{{ number_format($mobil->harga, 0, ',', '.') }} / hari</p>

        <div class="mt-6">
            <a href="{{ route('pesanan.saya') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Kembali
            </a>
           
        </div>
    </div>
</x-app-layout>
