<x-app-layout>
    <div class="flex flex-col items-center justify-center min-h-screen text-center">
        <h2 class="text-2xl font-semibold text-green-600 mb-2">✅ Pembayaran Berhasil!</h2>
        <p class="text-gray-700 mb-4">Terima kasih, transaksi kamu telah selesai.</p>

        <a href="{{ route('mobils.index') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Kembali ke Mobil
        </a>
    </div>
</x-app-layout>
