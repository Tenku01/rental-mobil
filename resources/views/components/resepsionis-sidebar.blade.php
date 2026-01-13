<nav x-cloak
    class="absolute inset-y-0 left-0 z-30 bg-cyan-800 text-cyan-50 overflow-y-auto 
           transition-all duration-300 transform 
           lg:static lg:translate-x-0 lg:flex-shrink-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    :style="sidebarOpen ? 'width: 16rem;' : 'width: 5rem;'">

    {{-- Header Logo --}}
    <div class="flex items-center justify-center h-16 bg-cyan-900 shadow-md p-3 overflow-hidden">
        <span x-show="sidebarOpen" x-transition class="text-xl font-semibold tracking-wider whitespace-nowrap">
            Resepsionis Panel
        </span>

        <span x-show="!sidebarOpen" x-transition class="text-xl font-semibold tracking-wider">
            RP
        </span>
    </div>

    <div class="py-4 space-y-2">

        @php
            $currentRoute = Route::currentRouteName() ?? '';
        @endphp

        {{-- KELOMPOK 1: UTAMA --}}
        <div class="px-4 mt-2 mb-1 text-xs font-semibold text-cyan-200 uppercase tracking-wider" x-show="sidebarOpen">
            Utama
        </div>

        @foreach ([
            [
                'route' => 'resepsionis.dashboard',
                'label' => 'Dashboard',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-2-2m-2 2h-4"></path>',
            ],
            [
                'route' => 'resepsionis.verifikasi.index',
                'label' => 'Verifikasi Identitas',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
            ],
        ] as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center px-4 py-2 transition-colors duration-200 
                      {{ Str::startsWith($currentRoute, $item['route']) ? 'bg-cyan-700 border-l-4 border-cyan-500' : 'hover:bg-cyan-700' }}"
               :class="sidebarOpen ? 'justify-start' : 'justify-center'"
               title="{{ $item['label'] }}">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>
                <span x-show="sidebarOpen" x-transition class="ml-3 text-sm font-medium whitespace-nowrap">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach

        {{-- KELOMPOK 2: MANAJEMEN DATA --}}
        <div class="px-4 mt-6 mb-1 text-xs font-semibold text-cyan-200 uppercase tracking-wider" x-show="sidebarOpen">
            Manajemen Data
        </div>

        @foreach ([
            [
                'route' => 'resepsionis.user.index', // Pastikan route ini ada atau sesuaikan
                'label' => 'Data User', // Mungkin maksudnya Data Akun User
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>',
            ],
            [
                'route' => 'resepsionis.pelanggan.index',
                'label' => 'Pelanggan',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V5a4 4 0 10-8 0v6M5 20h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z"></path>',
            ],
            [
                'route' => 'resepsionis.mobil.index',
                'label' => 'Mobil',
                'icon' => '
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13l2-5a2 2 0 012-1h10a2 2 0 012 1l2 5" /> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13h14v5a1 1 0 01-1 1h-1a2 2 0 01-4 0H11a2 2 0 01-4 0H6a1 1 0 01-1-1v-5z" /> <circle cx="7.5" cy="17.5" r="1.5" /> <circle cx="16.5" cy="17.5" r="1.5" />',
            ],
        ] as $item)
            @php $url = Route::has($item['route']) ? route($item['route']) : '#'; @endphp
            <a href="{{ $url }}"
               class="flex items-center px-4 py-2 transition-colors duration-200 
                      {{ Str::startsWith($currentRoute, $item['route']) ? 'bg-cyan-700 border-l-4 border-cyan-500' : 'hover:bg-cyan-700' }}"
               :class="sidebarOpen ? 'justify-start' : 'justify-center'"
               title="{{ $item['label'] }}">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>
                <span x-show="sidebarOpen" x-transition class="ml-3 text-sm font-medium whitespace-nowrap">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach

        {{-- KELOMPOK 3: TRANSAKSI & KEUANGAN --}}
        <div class="px-4 mt-6 mb-1 text-xs font-semibold text-cyan-200 uppercase tracking-wider" x-show="sidebarOpen">
            Transaksi & Keuangan
        </div>

        @foreach ([
            [
                'route' => 'resepsionis.peminjaman.index',
                'label' => 'Peminjaman',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18m-9 5h9m-9 0a3 3 0 11-6 0m6 0a3 3 0 11-6 0"></path>',
            ],
            [
                'route' => 'resepsionis.pengembalian.index', // Pastikan route ini ada
                'label' => 'Data Pengembalian',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-2 4h.01M17 16h.01M9 16h.01M7 16h.01M9 12h.01M11 12h.01M13 12h.01M15 12h.01M17 12h.01"></path>',
            ],
            [
                'route' => 'resepsionis.fine.index', // Pastikan route ini ada
                'label' => 'Manajemen Denda',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            ],
            [
                'route' => 'resepsionis.transactions.index',
                'label' => 'Pembayaran',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>',
            ],
            [
                'route' => 'resepsionis.pembatalan.index',
                'label' => 'Pembatalan Pesanan',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            ],
        ] as $item)
            @php $url = Route::has($item['route']) ? route($item['route']) : '#'; @endphp
            <a href="{{ $url }}"
               class="flex items-center px-4 py-2 transition-colors duration-200 
                      {{ Str::startsWith($currentRoute, $item['route']) ? 'bg-cyan-700 border-l-4 border-cyan-500' : 'hover:bg-cyan-700' }}"
               :class="sidebarOpen ? 'justify-start' : 'justify-center'"
               title="{{ $item['label'] }}">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>
                <span x-show="sidebarOpen" x-transition class="ml-3 text-sm font-medium whitespace-nowrap">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach

    </div>

</nav>

{{-- MOBILE OVERLAY --}}

<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-cyan-900 bg-opacity-50 lg:hidden"
    @click="sidebarOpen = false">
</div>