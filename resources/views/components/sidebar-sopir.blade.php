<div x-bind:class="{ '-translate-x-full': !sidebarOpen }" 
     {{-- Fixed untuk mobile (agar menutupi konten), lg:static agar di desktop menggeser konten --}}
     class="fixed inset-y-0 left-0 transform lg:static lg:translate-x-0 
            transition duration-300 ease-in-out bg-cyan-800 text-white w-64 z-40 shadow-2xl">
    <div class="p-6">
        <h1 class="text-2xl font-extrabold text-cyan-200">
            <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.726A20.813 20.813 0 0113.805 10M17.657 16.726L6.343 5.274M17.657 16.726l-7.791 2.518m-6.85-2.518l7.791-2.518M6.343 5.274l7.791 2.518M6.343 5.274l7.791-2.518">
                </path>
            </svg>
            Sopir Panel
        </h1>
    </div>

    {{-- Toggle Status Ketersediaan --}}
    @isset($sopir)
        <div class="px-4 py-3 border-t border-b border-cyan-700 mx-4 rounded-lg bg-cyan-700/50">
            <p class="text-sm font-semibold mb-2 flex justify-between items-center">
                <span>Status Anda:</span>
                <span
                    class="font-bold px-2 py-0.5 rounded-full text-xs text-white
                {{ $sopir->status === 'tersedia'
                    ? 'bg-green-500'
                    : ($sopir->status === 'tidak tersedia'
                        ? 'bg-yellow-500'
                        : ($sopir->status === 'bekerja'
                            ? 'bg-blue-500'
                            : 'bg-red-500')) }}">
                    {{ ucfirst($sopir->status) }}
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

                {{-- Hidden input to hold the status value that will be submitted --}}
                <input type="hidden" name="status" id="statusValue" value="{{ $sopir->status }}">

                <label for="statusToggle"
                    class="relative inline-flex items-center cursor-pointer {{ $isBekerja ? 'opacity-50 pointer-events-none' : '' }}">
                    <input type="checkbox" id="statusToggle" class="sr-only peer"
                        {{ $isTersedia || $isBekerja ? 'checked' : '' }} {{ $isBekerja ? 'disabled' : '' }}
                        onchange="submitStatusUpdate(this)">
                    {{-- Toggle Switch Styling --}}
                    <div
                        class="relative w-11 h-6 bg-gray-400 
            peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-cyan-300 
            rounded-full peer
            peer-checked:after:translate-x-full peer-checked:after:border-white
            after:content-[''] after:absolute after:top-1/2 after:-translate-y-1/2
            after:left-[2px] after:bg-white after:border-gray-300 after:border
            after:rounded-full after:h-5 after:w-5 after:transition-all
            peer-checked:bg-green-500
            {{ $isTersedia ? 'bg-green-500' : 'bg-gray-400' }}">
                    </div>


                    <span class="ml-3 text-sm font-medium text-white/90">
                        @if ($isBekerja)
                            Terkunci: Bekerja
                        @else
                            {{ $isTersedia ? 'Sedang Tersedia' : 'Sedang Tidak Tersedia' }}
                        @endif
                    </span>
                </label>

                {{-- Optional message when locked --}}
                @if ($isBekerja)
                    <p class="text-xs text-white/70 mt-2">Status terkunci saat bertugas.</p>
                @endif
            </form>
        </div>
    @endisset

    <nav class="mt-4 space-y-2">

        {{-- Link Dashboard --}}
        <a href="{{ route('sopir.dashboard') }}"
            class="flex items-center px-6 py-3 transition duration-200 text-cyan-100
                    {{ Request::routeIs('sopir.dashboard') ? 'bg-cyan-600 border-l-4 border-white font-semibold' : 'hover:bg-cyan-700' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M3 14h18m-9-4v8m-9-6v8m18-8v8"></path>
            </svg>
            <span>Dashboard</span>
        </a>

        {{-- Link Tugas Aktif (Menggunakan rute terpisah) --}}
        <a href="{{ route('sopir.activeTasks') }}"
            class="flex items-center px-6 py-3 transition duration-200 text-cyan-100
                    {{ Request::routeIs('sopir.activeTasks') ? 'bg-cyan-600 border-l-4 border-white font-semibold' : 'hover:bg-cyan-700' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                </path>
            </svg>
            <span>Tugas Aktif</span>
        </a>

        {{-- Link Riwayat (Menggunakan rute terpisah) --}}
        <a href="{{ route('sopir.history') }}"
            class="flex items-center px-6 py-3 transition duration-200 text-cyan-100
                    {{ Request::routeIs('sopir.history') ? 'bg-cyan-600 border-l-4 border-white font-semibold' : 'hover:bg-cyan-700' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Riwayat Kerja</span>
        </a>
    </nav>

    {{-- Tombol Close Sidebar (Hanya untuk Mobile) --}}
    <button @click="sidebarOpen = false" class="absolute top-4 right-4 text-cyan-100 lg:hidden focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>

{{-- Inline JavaScript untuk menangani submission toggle --}}
<script>
    function submitStatusUpdate(checkbox) {
        const form = document.getElementById('statusToggleForm');
        const statusValueInput = document.getElementById('statusValue');
        
        // Tentukan status baru: jika dicentang -> 'tersedia', jika tidak dicentang -> 'tidak tersedia'
        statusValueInput.value = checkbox.checked ? 'tersedia' : 'tidak tersedia';
        
        // Submit form
        form.submit();
    }
</script>


  

    