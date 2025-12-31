<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Status Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto text-center">
        <h1 class="text-2xl font-bold mb-4">Status Pembayaran: {{ ucfirst($transaction->status) }}</h1>
        <p>Total yang dibayarkan: Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
        <a href="{{ route('mobils.index') }}" class="mt-4 px-6 py-3 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 inline-block">
            Kembali ke Daftar Mobil
        </a>
    </div>
</x-app-layout>
