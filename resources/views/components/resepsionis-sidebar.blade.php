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

        @foreach ([
        // DASHBOARD
        [
            'route' => 'resepsionis.dashboard',
            'label' => 'Dashboard',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-2-2m-2 2h-4">
                        </path>',
        ],

        // VERIFIKASI IDENTITAS (MENU BARU)
        [
            'route' => 'resepsionis.verifikasi.index',
            'label' => 'Verifikasi Identitas',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>',
        ],

        // MANAJEMEN USER / PELANGGAN (Read/Write)
        [
            'route' => 'resepsionis.user.index',
            'label' => 'Data User',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>',
        ],

        // PELANGGAN
        [
            'route' => 'resepsionis.pelanggan.index',
            'label' => 'Pelanggan',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V5a4 4 0 10-8 0v6M5 20h14a2 2 0 002-2v-5a2
                        2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z">
                        </path>',
        ],

        // MOBIL
        [
            'route' => 'resepsionis.mobil.index',
            'label' => 'Mobil',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                       d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                       <circle cx="7" cy="17" r="2"></circle>
                       <circle cx="17" cy="17" r="2"></circle>
                       <path d="M5 17h14v-6l-2-5H7l-2 5v6z"></path>',
        ],

        // PEMINJAMAN
        [
            'route' => 'resepsionis.peminjaman.index',
            'label' => 'Peminjaman',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7h18M3 12h18m-9 5h9m-9 0a3 3 0 11-6 0m6 0a3 3 0 11-6 0">
                        </path>',
        ],

        // DATA PENGEMBALIAN
        [
            'route' => 'resepsionis.pengembalian.index',
            'label' => 'Data Pengembalian',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-2 4h.01M17 16h.01M9 16h.01M7 16h.01M9 12h.01M11 12h.01M13 12h.01M15 12h.01M17 12h.01"></path>',
        ],

        // MANAJEMEN DENDA (FINES)
        [
            'route' => 'resepsionis.fine.index',
            'label' => 'Manajemen Denda',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        ],

        // PEMBATALAN PESANAN
        [
            'route' => 'resepsionis.pembatalan.index',
            'label' => 'Pembatalan Pesanan',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>',
        ],
    ] as $item)
            @php
                $isActive = Str::startsWith($currentRoute, $item['route']);
            @endphp

            <a href="{{ route($item['route']) }}"
                class="flex items-center px-4 py-2 mt-2 transition-colors duration-200 
                    {{ $isActive ? 'bg-cyan-700 border-l-4 border-cyan-500' : 'hover:bg-cyan-700' }}"
                :class="sidebarOpen ? 'justify-start' : 'justify-center'" title="{{ $item['label'] }}">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>

                <span x-show="sidebarOpen" x-transition class="ml-3 text-sm font-medium whitespace-nowrap text-cyan-50">
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
