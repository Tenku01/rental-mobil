<header x-data="{ userMenuOpen: false }" class="bg-white shadow-lg z-30">
    <div class="flex items-center justify-between h-16 px-6">
        
        {{-- Tombol Toggle Sidebar (Hanya untuk Mobile) --}}
        {{-- Tombol hanya muncul jika sidebar tertutup --}}
        <button @click="sidebarOpen = true" x-show="!sidebarOpen" class="text-gray-500 lg:hidden focus:outline-none hover:text-cyan-600 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        
        {{-- Judul di Desktop (Menggunakan @yield('title') dari Dashboard) --}}
        <div class="text-lg font-bold text-gray-700 hidden lg:block">
            @yield('title')
        </div>

        {{-- Spacer agar elemen profil tetap di kanan --}}
        <div class="flex-1 lg:hidden"></div>

        {{-- User Profile & Logout (Menggunakan Alpine x-data) --}}
        <div class="relative ml-auto" @click.outside="userMenuOpen = false">
            <button @click="userMenuOpen = !userMenuOpen" 
                    class="flex items-center space-x-2 focus:outline-none p-1 rounded-full hover:bg-gray-100 transition">
                
                {{-- Nama Pengguna --}}
                <span class="text-sm font-medium text-gray-600 hidden sm:inline-block">
                    {{ Auth::user()->name ?? 'Pengemudi' }}
                </span>
                
                {{-- Inisial Profil --}}
                <div class="w-10 h-10 rounded-full bg-cyan-600 flex items-center justify-center text-white text-base font-bold shadow-md">
                    {{ mb_substr(Auth::user()->name ?? 'P', 0, 1) }}
                </div>
            </button>

            {{-- Dropdown Menu --}}
            <div x-show="userMenuOpen" 
                 x-transition:enter="transition ease-out duration-100" 
                 x-transition:enter-start="transform opacity-0 scale-95" 
                 x-transition:enter-end="transform opacity-100 scale-100" 
                 x-transition:leave="transition ease-in duration-75" 
                 x-transition:leave-start="transform opacity-100 scale-100" 
                 x-transition:leave-end="transform opacity-0 scale-95" 
                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl py-1 z-50 border border-gray-200 origin-top-right">
                
                <a href="#" 
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-cyan-50 hover:text-cyan-700 transition">Lihat Profil</a>
                
                <div class="border-t border-gray-100 my-1"></div>
                
                {{-- Logout Form --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>