<header class="flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">

    {{-- BAGIAN KIRI --}}
    <div class="flex items-center">

        {{-- Tombol Hamburger --}}
        <button 
            @click="sidebarOpen = !sidebarOpen"
            class="text-gray-600 hover:text-gray-800 focus:outline-none mr-4 transition"
        >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Judul Halaman --}}
        <h1 class="text-xl font-semibold text-gray-800 hidden md:block">
            @switch(Route::currentRouteName())
                @case('staff.pengecekan.index')   Halaman Pengecekan @break
                @case('staff.dashboard')          Staff Dashboard @break
                @case('staff.profile')            Profile Saya @break
                @default                          Staff Panel
            @endswitch
        </h1>
    </div>

    {{-- BAGIAN KANAN: DROPDOWN USER --}}
    <div x-data="{ dropdownOpen: false }" class="relative">

        <button 
            @click="dropdownOpen = !dropdownOpen" 
            @keydown.escape="dropdownOpen = false"
            class="flex items-center focus:outline-none"
        >
            <span class="mr-2 text-sm font-medium text-gray-700 hidden sm:block">
                {{ Auth::user()->name ?? 'Staff Name' }}
            </span>

            <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm">
                {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}
            </div>
        </button>

        {{-- Dropdown --}}
        <div 
            x-show="dropdownOpen"
            @click.outside="dropdownOpen = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute right-0 mt-2 w-48 bg-white backdrop-blur-sm rounded-md overflow-hidden shadow-lg z-50"
        >
            {{-- Profile --}}
            @if (Route::has('staff.profile'))
                <a href="{{ route('staff.profile') }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-500 hover:text-white">
                    Profile
                </a>
            @endif

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button 
                    onclick="event.preventDefault(); this.closest('form').submit();"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-500 hover:text-white"
                >
                    Log Out
                </button>
            </form>
        </div>

    </div>

</header>
