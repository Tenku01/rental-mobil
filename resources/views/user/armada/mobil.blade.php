<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Mobil') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto mt-10 mb-16 px-6 py-10 bg-white rounded-2xl shadow-md border border-cyan-600">

        {{-- 🔹 Flash Messages --}}
        @if (session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif
        @if (session('warning'))
            <x-alert type="warning">{{ session('warning') }}</x-alert>
        @endif
        @if (session('error'))
            <x-alert type="error">{{ session('error') }}</x-alert>
        @endif

        {{-- 🔍 Search Bar --}}
        <div class="mb-6">
            <x-text-input 
                id="searchInput" 
                type="text" 
                placeholder="Cari nama/tipe mobil..." 
                class="w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm"
            />
        </div>

        {{-- 🔽 Filter Dropdown --}}
        <div class="flex flex-col sm:flex-row w-full gap-3 items-stretch">
            {{-- Wrapper kiri (dropdown) --}}
            <div class="flex flex-grow gap-3">
                {{-- Jumlah Kursi --}}
                <div x-data="{ open: false }" class="relative w-full">
                    <button 
                        @click="open = !open"
                        type="button"
                        class="flex items-center justify-between w-full px-4 py-2 
                               bg-white border border-cyan-500 
                               rounded-md font-medium text-gray-700 
                               hover:bg-cyan-50 transition"
                    >
                        Pilih Jumlah Kursi
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        class="absolute left-0 w-full mt-2 bg-white border border-cyan-500 rounded-md shadow-lg z-10"
                    >
                        <x-dropdown-link href="{{ route('mobils.index', ['jumlah_kursi' => '5']) }}">5 Kursi</x-dropdown-link>
                        <x-dropdown-link href="{{ route('mobils.index', ['jumlah_kursi' => '7']) }}">7 Kursi</x-dropdown-link>
                        <x-dropdown-link href="{{ route('mobils.index', ['jumlah_kursi' => '9']) }}">9 Kursi</x-dropdown-link>
                    </div>
                </div>

                {{-- Transmisi --}}
                <div x-data="{ open: false }" class="relative w-full">
                    <button 
                        @click="open = !open"
                        type="button"
                        class="flex items-center justify-between w-full px-4 py-2 
                               bg-white border border-cyan-500 
                               rounded-md font-medium text-gray-700 
                               hover:bg-cyan-50 transition"
                    >
                        Pilih Transmisi
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        class="absolute left-0 w-full mt-2 bg-white border border-cyan-500 rounded-md shadow-lg z-10"
                    >
                        <x-dropdown-link href="{{ route('mobils.index', ['transmisi' => 'manual']) }}">Manual</x-dropdown-link>
                        <x-dropdown-link href="{{ route('mobils.index', ['transmisi' => 'otomatis']) }}">Automatic</x-dropdown-link>
                    </div>
                </div>
            </div>

            {{-- Tombol Reset Filter --}}
            <div class="flex-none">
                <x-primary-button 
                    type="button" 
                    onclick="window.location.href='{{ route('mobils.index') }}'"
                    class="h-full px-6 py-2 flex items-center justify-center whitespace-nowrap"
                >
                    Reset Filter
                </x-primary-button>
            </div>
        </div>

        {{-- 🔹 Daftar Mobil --}}
        <section id="daftar-mobil" class="mt-10">
            @if ($mobils->isEmpty())
                <p class="text-center text-gray-500 text-lg py-10">🚗 Tidak ada mobil yang tersedia.</p>
            @else
                <p id="noResultMessage" class="hidden text-center text-gray-500 text-lg py-10">🚗 Mobil tidak ditemukan.</p>

                <div id="mobilList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                    @foreach ($mobils as $mobil)
                        <div class="mobil-card bg-white rounded-2xl shadow-md hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-300">
                            <div class="relative">
                                <img src="{{ asset('storage/' . $mobil->foto) }}" alt="{{ $mobil->tipe }}" class="w-full h-56 object-cover">
                                <div class="absolute bottom-0 left-0 bg-black bg-opacity-60 text-white text-sm font-semibold px-3 py-1 rounded-tr-lg">
                                    Rp {{ number_format($mobil->harga, 0, ',', '.') }} / hari
                                </div>
                            </div>

                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800">{{ $mobil->tipe }}</h3>
                                <p class="text-sm text-gray-500 mb-3">{{ $mobil->merek }}</p>

                                <div class="flex flex-col gap-1 text-sm text-gray-600">
                                    <span>Warna: {{ $mobil->warna }}</span>
                                    <span>Transmisi: {{ $mobil->transmisi }}</span>
                                    <span>Kursi: {{ $mobil->kursi }}</span>
                                </div>

                                <div class="mt-5">
                                    @auth
                                        @if ($hasIdentification)
                                            {{-- ✅ Sudah upload identitas --}}
                                            <a href="{{ route('peminjaman.create', $mobil->id) }}"
                                               class="block w-full text-center bg-cyan-500 text-white font-semibold py-2 rounded-md hover:bg-cyan-600 transition">
                                                Sewa Sekarang
                                            </a>
                                        @else
                                            {{-- ⚠️ Belum upload identitas --}}
                                            <a href="{{ route('upload.identity') }}"
                                               class="block w-full text-center bg-yellow-500 text-white font-semibold py-2 rounded-md hover:bg-yellow-600 transition">
                                                Lengkapi Identitas Dulu
                                            </a>
                                        @endif
                                    @else
                                        {{-- 🔒 Belum login --}}
                                        <a href="{{ route('login') }}"
                                           class="block w-full text-center bg-cyan-500 text-white font-semibold py-2 rounded-md hover:bg-cyan-600 transition">
                                            Login untuk Meminjam
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- 🔸 Pagination --}}
                <div class="mt-10">
                    {{ $mobils->links('components.pagination-info') }}
                </div>
            @endif
        </section>
    </div>

    {{-- 🔹 Script Pencarian --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('searchInput');
            const cards = document.querySelectorAll('.mobil-card');
            const message = document.getElementById('noResultMessage');

            input.addEventListener('keyup', () => {
                const value = input.value.toLowerCase();
                let hasResult = false;

                cards.forEach(card => {
                    const match = card.innerText.toLowerCase().includes(value);
                    card.style.display = match ? '' : 'none';
                    if (match) hasResult = true;
                });

                message.classList.toggle('hidden', hasResult);
            });
        });
    </script>
</x-app-layout>
