<div x-bind:class="{ '-translate-x-full': !sidebarOpen }" 
     class="fixed inset-y-0 left-0 transform lg:static lg:translate-x-0 
            transition duration-300 ease-in-out bg-gradient-to-b from-cyan-800 to-cyan-900 text-white w-64 z-40 shadow-2xl">
    <div class="p-6">
        <h1 class="text-2xl font-extrabold text-cyan-100 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                </path>
            </svg>
            Sopir Panel
        </h1>
        <p class="text-xs text-cyan-200 mt-1">Driver Management System</p>
    </div>

    {{-- Toggle Status Ketersediaan --}}
    @isset($sopir)
        <div class="px-4 py-3 border-t border-b border-cyan-700/50 mx-4 rounded-lg bg-cyan-700/30 backdrop-blur-sm">
            <p class="text-sm font-semibold mb-2 flex justify-between items-center">
                <span class="text-cyan-100">Status Anda:</span>
                <span
                    class="font-bold px-3 py-1 rounded-full text-xs text-white
                    {{ match($sopir->status) {
                        'tersedia' => 'bg-green-500',
                        'tidak_tersedia' => 'bg-yellow-500',
                        'bekerja' => 'bg-blue-500',
                        default => 'bg-red-500'
                    } }}">
                    {{ ucfirst(str_replace('_', ' ', $sopir->status)) }}
                </span>
            </p>

            {{-- Form Toggle Status --}}
            <form id="statusToggleForm" action="{{ route('sopir.updateStatus') }}" method="POST" class="mt-2">
                @csrf
                @method('PUT')

                @php
                    $isTersedia = $sopir->status === 'tersedia';
                    $isBekerja = $sopir->status === 'bekerja';
                @endphp

                <input type="hidden" name="status" id="statusValue" value="{{ $sopir->status }}">

                <label for="statusToggle"
                    class="relative inline-flex items-center cursor-pointer {{ $isBekerja ? 'opacity-50 pointer-events-none' : '' }}">
                    <input type="checkbox" id="statusToggle" class="sr-only peer"
                        {{ $isTersedia || $isBekerja ? 'checked' : '' }} 
                        {{ $isBekerja ? 'disabled' : '' }}
                        onchange="submitStatusUpdate(this)">
                    
                    {{-- Toggle Switch --}}
                    <div class="relative w-11 h-6 bg-gray-600 
                         peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-cyan-300 
                         rounded-full peer
                         peer-checked:after:translate-x-full peer-checked:after:border-white
                         after:content-[''] after:absolute after:top-1/2 after:-translate-y-1/2
                         after:left-[2px] after:bg-white after:border-gray-300 after:border
                         after:rounded-full after:h-5 after:w-5 after:transition-all
                         peer-checked:bg-green-500">
                    </div>

                    <span class="ml-3 text-sm font-medium text-cyan-100">
                        @if ($isBekerja)
                            🔒 Terkunci: Sedang Bekerja
                        @else
                            {{ $isTersedia ? '✅ Sedang Tersedia' : '⏸️ Tidak Tersedia' }}
                        @endif
                    </span>
                </label>

                @if ($isBekerja)
                    <p class="text-xs text-cyan-200/70 mt-2">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Status terkunci saat sedang bertugas
                    </p>
                @endif
            </form>
        </div>
    @endisset

    <nav class="mt-4 space-y-1">
        {{-- Dashboard --}}
        <a href="{{ route('sopir.dashboard') }}"
            class="flex items-center px-6 py-3 transition duration-200
                    {{ Request::routeIs('sopir.dashboard') 
                        ? 'bg-cyan-600 border-r-4 border-cyan-200 font-semibold shadow-inner' 
                        : 'text-cyan-100 hover:bg-cyan-700/50 hover:border-r-4 border-cyan-400' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </a>

        {{-- Logbook --}}
        <a href="{{ route('sopir.logbook.index') }}"
    class="flex items-center px-6 py-3 transition duration-200
            {{ Request::routeIs('sopir.logbook.*') 
                ? 'bg-cyan-600 border-r-4 border-cyan-200 font-semibold shadow-inner' 
                : 'text-cyan-100 hover:bg-cyan-700/50 hover:border-r-4 border-cyan-400' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
    </svg>
    <span>Logbook</span>
</a>

        {{-- Tugas Aktif --}}
        <a href="{{ route('sopir.activeTasks') }}"
            class="flex items-center px-6 py-3 transition duration-200
                    {{ Request::routeIs('sopir.activeTasks') 
                        ? 'bg-cyan-600 border-r-4 border-cyan-200 font-semibold shadow-inner' 
                        : 'text-cyan-100 hover:bg-cyan-700/50 hover:border-r-4 border-cyan-400' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Tugas Aktif</span>
            @isset($activeTasksCount)
                <span class="ml-auto bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                    {{ $activeTasksCount }}
                </span>
            @endisset
        </a>

        {{-- Riwayat Kerja --}}
        <a href="{{ route('sopir.history') }}"
            class="flex items-center px-6 py-3 transition duration-200
                    {{ Request::routeIs('sopir.history') 
                        ? 'bg-cyan-600 border-r-4 border-cyan-200 font-semibold shadow-inner' 
                        : 'text-cyan-100 hover:bg-cyan-700/50 hover:border-r-4 border-cyan-400' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Riwayat Kerja</span>
        </a>
    </nav>

    {{-- Logout Button --}}
    <div class="absolute bottom-0 w-full p-4 border-t border-cyan-700/50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                class="flex items-center w-full px-4 py-2 text-sm text-cyan-100 hover:bg-red-600/20 hover:text-red-200 rounded-lg transition duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Keluar
            </button>
        </form>
    </div>

    {{-- Mobile Close Button --}}
    <button @click="sidebarOpen = false" 
            class="absolute top-4 right-4 text-cyan-100 hover:text-white lg:hidden focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>

<script>
    function submitStatusUpdate(checkbox) {
        const form = document.getElementById('statusToggleForm');
        const statusValueInput = document.getElementById('statusValue');
        
        // Toggle status
        statusValueInput.value = checkbox.checked ? 'tersedia' : 'tidak_tersedia';
        
        // Show loading state
        const button = checkbox.parentElement.querySelector('span');
        const originalText = button.textContent;
        button.textContent = 'Mengupdate...';
        button.classList.add('opacity-50');
        
        // Submit form
        form.submit();
    }
</script>