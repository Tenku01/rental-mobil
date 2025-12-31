<nav 
    x-cloak
    class="absolute inset-y-0 left-0 z-30 bg-cyan-800 text-cyan-50 overflow-y-auto 
           transition-all duration-300 transform
           lg:static lg:translate-x-0 lg:flex-shrink-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    :style="sidebarOpen ? 'width: 16rem;' : 'width: 5rem;'"
>

    {{-- Header Logo --}}
    <div class="flex items-center justify-center h-16 bg-cyan-900 shadow-md p-3 overflow-hidden">
        <span x-show="sidebarOpen"
              x-transition
              class="text-xl font-semibold tracking-wider whitespace-nowrap">
            Admin Panel
        </span>

        <span x-show="!sidebarOpen"
              x-transition
              class="text-xl font-semibold tracking-wider">
            AP
        </span>
    </div>

     <div class="py-4 space-y-2">

        @php
            $currentRoute = Route::currentRouteName() ?? '';
        @endphp

        {{-- DAFTAR MENU SIDEBAR --}}
        @foreach ([
            [
                'route' => 'admin.dashboard',
                'label' => 'Dashboard',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-2-2m-2 2h-4m-7 20h14a1 1 0 001-1V12a1 1 0 00-1-1H5a1 1 0 00-1 1v7a1 1 0 001 1z"></path>'
            ],
            [
                'route' => 'admin.mobil',
                'label' => 'Data Mobil',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>'
            ],
            [
                'route' => 'admin.peminjaman',
                'label' => 'Transaksi',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>'
            ],
            // --- GRUP MANAJEMEN USER ---
            [
                'route' => 'admin.users',
                'label' => 'Semua User',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>'
            ],
            [
                'route' => 'admin.staff', // <--- MENU BARU: Staff
                'label' => 'Data Staff',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>'
            ],
            [
                'route' => 'admin.resepsionis', // <--- MENU BARU: Resepsionis
                'label' => 'Resepsionis',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>'
            ],
            [
                'route' => 'admin.sopir',
                'label' => 'Data Sopir',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>'
            ],
            [
                'route' => 'admin.roles', // <--- MENU BARU: Roles
                'label' => 'Role Akses',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>'
            ],
            // ---------------------------
            [
                'route' => 'admin.verifikasi',
                'label' => 'Verifikasi KTP',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
            ],
        ] as $item)
            @php
                // Pastikan route ada untuk menghindari error jika belum didefinisikan
                $url = Route::has($item['route']) ? route($item['route']) : '#';
                $isActive = Str::startsWith($currentRoute, $item['route']);
            @endphp

            <a href="{{ $url }}"
               class="flex items-center px-4 py-2 mt-2 transition-colors duration-200 
                      {{ $isActive ? 'bg-cyan-700 border-l-4 border-cyan-500' : 'hover:bg-cyan-700' }}"
               :class="sidebarOpen ? 'justify-start' : 'justify-center'"
               title="{{ $item['label'] }}"
            >
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>

                <span x-show="sidebarOpen"
                      x-transition
                      class="ml-3 text-sm font-medium whitespace-nowrap">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach

    </div>
</nav>

{{-- MOBILE OVERLAY --}}
<div 
    x-show="sidebarOpen"
    x-transition.opacity
    class="fixed inset-0 z-20 bg-cyan-900 bg-opacity-50 lg:hidden"
    @click="sidebarOpen = false">
</div>