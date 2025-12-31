<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- 🔹 Konten Utama -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Selamat datang di Aka Rent Car!") }}
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-2 text-gray-800">Pesanan Anda</h3>
                    <p class="text-sm text-gray-600">
                        Lihat daftar pesanan mobil yang telah Anda buat.
                    </p>
                    <a href="{{ route('pesanan.saya') }}"
                       class="inline-block mt-4 bg-cyan-600 text-white px-4 py-2 rounded hover:bg-cyan-700 transition">
                        Lihat Pesanan
                    </a>
                </div>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-2 text-gray-800">Armada Kami</h3>
                    <p class="text-sm text-gray-600">
                        Jelajahi berbagai pilihan mobil yang tersedia untuk disewa.
                    </p>
                    <a href="{{ route('mobils.index') }}"
                       class="inline-block mt-4 bg-cyan-600 text-white px-4 py-2 rounded hover:bg-cyan-700 transition">
                        Lihat Armada
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
