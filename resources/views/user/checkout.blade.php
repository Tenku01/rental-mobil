<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pembayaran Peminjaman') }}
        </h2>
    </x-slot>

    <div class="py-12 text-center">
        <h1 class="text-2xl font-bold mb-6">Sedang mengarahkan ke Midtrans...</h1>
        <p>Jika tidak diarahkan otomatis, klik tombol di bawah:</p>
        <a href="{{ $paymentUrl }}" class="mt-4 px-6 py-3 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 inline-block">
            Bayar Sekarang
        </a>
    </div>

    <script>
        window.location.href = "{{ $paymentUrl }}";
    </script>
</x-app-layout>
