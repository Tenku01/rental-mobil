<div class="space-y-6 pt-6 border-t border-gray-100">

    <div class="flex items-center space-x-2">
        <div class="w-2 h-6 bg-cyan-500 rounded-full"></div>
        <h2 class="text-xl font-black text-gray-700 uppercase tracking-tight">
            Waktu Penggunaan
        </h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

        {{-- TANGGAL SEWA --}}
        <div>
            <x-input-label
                for="tanggal_sewa"
                value="Tanggal Sewa"
                class="text-cyan-700 font-black uppercase text-[11px] mb-1"
            />
            <x-text-input
                id="tanggal_sewa"
                name="tanggal_sewa"
                type="text"
                placeholder="Pilih Tanggal"
                readonly
                required
                x-model="tanggalSewa"
                @change="checkDriver()"
                class="block w-full border-2 border-cyan-100 focus:border-cyan-500 focus:ring-cyan-500
                       rounded-2xl bg-gray-50/30 font-bold py-3 pl-4"
            />
        </div>

        {{-- JAM SEWA --}}
        <div>
            <x-input-label
                for="jam_sewa"
                value="Jam Sewa (WIB)"
                class="text-cyan-700 font-black uppercase text-[11px] mb-1"
            />
            <x-text-input
                id="jam_sewa"
                name="jam_sewa"
                type="time"
                required
                x-model="jamSewa"
                class="block w-full border-2 border-cyan-100 focus:border-cyan-500 focus:ring-cyan-500
                       rounded-2xl bg-gray-50/30 font-bold py-3"
            />
        </div>

        {{-- TANGGAL KEMBALI --}}
        <div>
            <x-input-label
                for="tanggal_kembali"
                value="Tanggal Kembali"
                class="text-cyan-700 font-black uppercase text-[11px] mb-1"
            />
            <x-text-input
                id="tanggal_kembali"
                name="tanggal_kembali"
                type="text"
                placeholder="Pilih Tanggal"
                readonly
                required
                x-model="tanggalKembali"
                @change="checkDriver()"
                class="block w-full border-2 border-cyan-100 focus:border-cyan-500 focus:ring-cyan-500
                       rounded-2xl bg-gray-50/30 font-bold py-3 pl-4"
            />
        </div>

    </div>

    {{-- INFO DEADLINE --}}
    <div
        x-show="tanggalKembali"
        x-cloak
        x-transition
        class="p-4 bg-cyan-600 rounded-2xl shadow-lg shadow-cyan-200">

        <p class="text-sm text-white font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>

            <span>
                Pengembalian maksimal pukul
                <strong x-text="jamSewa"></strong>
                WIB pada
                <strong x-text="tanggalKembali"></strong>
            </span>
        </p>
    </div>

</div>
