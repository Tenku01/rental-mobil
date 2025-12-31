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
            Staff Panel
        </span>

        <span x-show="!sidebarOpen"
              x-transition
              class="text-xl font-semibold tracking-wider">
            SP
        </span>
    </div>

    <div class="py-4 space-y-2">

        @php
            $currentRoute = Route::currentRouteName() ?? '';
        @endphp

        @foreach ([
            ['route' => 'staff.dashboard', 'label' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-2-2m-2 2h-4m-7 20h14a1 1 0 001-1V12a1 1 0 00-1-1H5a1 1 0 00-1 1v7a1 1 0 001 1z"></path>'],
            ['route' => 'staff.pengecekan.index', 'label' => 'Pengecekan', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>'],
            ['route' => 'staff.profile', 'label' => 'Profile', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>'],
        ] as $item)
            @php
                $isActive = Str::startsWith($currentRoute, $item['route']);
            @endphp

            <a href="{{ route($item['route']) }}"
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

{{-- MOBILE OVERLAY (harus di luar <nav>) --}}
<div 
    x-show="sidebarOpen"
    x-transition.opacity
    class="fixed inset-0 z-20 bg-cyan bg-opacity-50 lg:hidden"
    @click="sidebarOpen = false">
</div>
