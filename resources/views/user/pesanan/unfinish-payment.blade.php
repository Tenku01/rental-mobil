<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pembayaran Belum Selesai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 shadow-lg sm:rounded-2xl p-8 text-center">
                <h1 class="text-2xl font-bold text-red-600 mb-4">⚠️ Pembayaran Belum Selesai</h1>
                <p class="mb-6">Silakan selesaikan pembayaran Anda untuk mengonfirmasi peminjaman mobil.</p>
                <a href="{{ route('mobils.index') }}" class="inline-block px-6 py-3 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition">
                    Kembali ke Daftar Mobil
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
