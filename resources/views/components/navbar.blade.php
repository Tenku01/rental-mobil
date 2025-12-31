 <head>
     @php
    $isProduction = app()->environment('production');
    $manifestPath = $isProduction ? '../public_html/build/manifest.json' : public_path('build/manifest.json');
@endphp

@if ($isProduction && file_exists($manifestPath))
    @php
        $manifest = json_decode(file_get_contents($manifestPath), true);
    @endphp
    <link rel="stylesheet" href="{{ config('app.url') }}/build/{{ $manifest['resources/css/app.css']['file'] }}">
    <script type="module" src="{{ config('app.url') }}/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>
@else
    @viteReactRefresh
    @vite(['resources/js/app.js', 'resources/css/app.css'])
@endif
</head>

<nav x-data="{ open: false }" class="bg-white shadow-md fixed w-full top-0 left-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
        <!-- 🔹 Logo -->
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <img src="{{ asset('logoakarentcar.png') }}" alt="Aka Rent Car" class="h-12 w-auto object-contain" />
            <span class="sr-only">Aka Rent Car</span>
        </a>

        <!-- 🔹 Menu Desktop -->
        <ul class="hidden md:flex space-x-8 text-sm font-semibold text-gray-800">
            <li><a href="{{ route('dashboard') }}" class="hover:text-cyan-600">Dashboard</a></li>
            <li><a href="{{ route('mobils.index') }}" class="hover:text-cyan-600">Armada</a></li>
            <li><a href="{{ route('pesanan.saya') }}" class="hover:text-cyan-600">Pesanan Saya</a></li>

            <!-- 🔹 Dropdown Profil -->
            @auth
                <li class="relative" x-data="{ openDropdown: false }">
                    <button @click="openDropdown = !openDropdown" class="hover:text-cyan-600 flex items-center space-x-1">
                        <span>Profil</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <ul x-show="openDropdown" @click.away="openDropdown = false"
                        class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg z-50">
                        <li>
                            <a href="{{ route('profile') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Profil Saya
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('upload.identity') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Upload Identitas
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M17 16l4-4m0 0l-4-4m4 4H3" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth
        </ul>

        <!-- 🔹 Tombol Hamburger (Mobile) -->
        <button @click="open = !open" class="md:hidden text-cyan-600 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- 🔹 Menu Mobile -->
    <div x-show="open" @click.away="open = false" class="md:hidden bg-white border-t border-gray-200">
        <ul class="flex flex-col text-center py-4 space-y-2 font-medium">
            <li><a href="{{ route('dashboard') }}" class="block py-2 hover:text-cyan-600">Dashboard</a></li>
            <li><a href="{{ route('mobils.index') }}" class="block py-2 hover:text-cyan-600">Armada</a></li>
            <li><a href="{{ route('pesanan.saya') }}" class="block py-2 hover:text-cyan-600">Pesanan Saya</a></li>
            <li><a href="{{ route('profile') }}" class="block py-2 hover:text-cyan-600">Profil Saya</a></li>
            <li><a href="{{ route('upload.identity') }}" class="block py-2 hover:text-cyan-600">Upload Identitas</a></li>

            @auth
                <li class="flex justify-center items-center py-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center space-x-2 text-cyan-600 hover:bg-cyan-600 hover:text-white px-4 py-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 16l4-4m0 0l-4-4m4 4H3" />
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}" class="block py-2 hover:text-cyan-600">Login</a></li>
            @endauth
        </ul>
    </div>
</nav>

<!-- Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
