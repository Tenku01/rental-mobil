<div class="pt-8 border-t border-gray-100 relative min-h-[140px]">

    {{-- LOADING CEK SOPIR --}}
    <div x-show="isCheckingDriver"
         x-transition.opacity
         class="flex items-center space-x-4 text-cyan-700 bg-white p-6 rounded-3xl border-2 border-dashed border-cyan-200 mb-6 animate-pulse">
        <div class="w-10 h-10 border-4 border-cyan-600 border-t-transparent rounded-full animate-spin"></div>
        <div class="flex flex-col">
            <span class="font-black text-sm uppercase tracking-widest">Validasi Armada Driver</span>
            <span class="text-xs text-gray-400 italic">Mohon tunggu...</span>
        </div>
    </div>

    {{-- OPSI SOPIR TERSEDIA --}}
    <div x-show="showDriverOption && !isCheckingDriver" x-transition>
        <div class="relative w-full" x-data="{ open: false }">

            <div class="flex items-center justify-between mb-3 px-1">
                <h4 class="font-black text-xs text-gray-400 uppercase tracking-widest">
                    Layanan Sopir
                </h4>
                <div class="flex items-center space-x-2 px-3 py-1 bg-green-50 rounded-full border border-green-200">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-600"></span>
                    </span>
                    <span class="text-[10px] font-black text-green-700 uppercase"
                          x-text="`${sisaSopir} Tersedia`"></span>
                </div>
            </div>

            <button type="button"
                    @click="open = !open"
                    class="group p-5 w-full border-2 border-cyan-100 bg-white rounded-3xl flex items-center justify-between hover:border-cyan-400 transition-all duration-300 shadow-sm">

                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-cyan-600 text-white rounded-xl shadow-lg group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>

                    <div class="flex flex-col items-start">
                        <span class="font-black text-cyan-900 tracking-tighter text-lg uppercase"
                              x-text="addOnSopir ? 'DENGAN SOPIR' : 'LEPAS KUNCI'"></span>
                        <span class="text-[10px] text-gray-400 font-bold mt-1 tracking-widest uppercase italic"
                              x-text="addOnSopir ? 'Rp 150.000 / Hari' : 'Anda mengemudi sendiri'"></span>
                    </div>
                </div>

                <svg class="w-6 h-6 text-cyan-300 transition-transform duration-500"
                     :class="open ? 'rotate-180 text-cyan-600' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- DROPDOWN --}}
            <div x-show="open"
                 x-cloak
                 x-transition
                 @click.away="open = false"
                 class="absolute z-40 mt-3 w-full bg-white border border-gray-100 rounded-3xl shadow-2xl p-2 space-y-1">

                <button type="button"
                        @click="addOnSopir = 0; open = false"
                        class="w-full text-left p-4 rounded-2xl transition-all"
                        :class="addOnSopir === 0
                            ? 'bg-cyan-600 text-white shadow-xl'
                            : 'hover:bg-cyan-50 text-gray-700'">
                    Lepas Kunci
                </button>

                <button type="button"
                        @click="addOnSopir = 1; open = false"
                        class="w-full text-left p-4 rounded-2xl transition-all"
                        :class="addOnSopir === 1
                            ? 'bg-cyan-600 text-white shadow-xl'
                            : 'hover:bg-cyan-50 text-gray-700'">
                    Gunakan Sopir (+150rb/Hari)
                </button>
            </div>
        </div>
    </div>

    {{-- SOPIR PENUH --}}
    <div x-show="driverFullStatus && !isCheckingDriver"
         x-transition
         class="p-6 bg-gradient-to-br from-orange-50 to-white border-l-8 border-orange-500 rounded-3xl text-orange-900 shadow-md flex items-start gap-5 border-2 border-orange-100">

        <div class="p-3 bg-orange-500 rounded-2xl shadow-lg flex-shrink-0 animate-bounce">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <div>
            <h4 class="font-black uppercase text-xs tracking-widest italic mb-1">
                Layanan Sopir Penuh
            </h4>
            <p class="text-sm font-semibold leading-relaxed">
                Mohon maaf, kru sopir telah terjadwal penuh.
                Silakan pesan unit secara
                <span class="underline font-black">LEPAS KUNCI</span>.
            </p>
        </div>
    </div>
</div>
