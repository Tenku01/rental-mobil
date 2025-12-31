<div class="mt-8 pt-8 border-t border-gray-100">
    <div class="flex flex-col sm:flex-row gap-6">
        <div class="w-full sm:w-1/2">
            <x-input-label
                value="Metode Pembayaran"
                class="text-cyan-700 font-black uppercase text-[11px] mb-4 tracking-widest px-1" />

            <div class="flex flex-col gap-3">
                <label
                    class="flex items-center justify-between p-4 border-2 rounded-2xl cursor-pointer transition-all"
                    :class="tipePembayaran === 'dp'
                        ? 'bg-cyan-600 border-cyan-600 text-white shadow-lg'
                        : 'bg-white border-gray-100 text-gray-700'">
                    <div class="flex items-center space-x-3">
                        <input type="radio" value="dp" x-model="tipePembayaran" name="radio-tipe-bayar"
                            class="w-5 h-5">
                        <span class="font-black uppercase">Bayar DP</span>
                    </div>
                </label>

                <label
                    class="flex items-center justify-between p-4 border-2 rounded-2xl cursor-pointer transition-all"
                    :class="tipePembayaran === 'lunas'
                        ? 'bg-cyan-600 border-cyan-600 text-white shadow-lg'
                        : 'bg-white border-gray-100 text-gray-700'">
                    <div class="flex items-center space-x-3">
                        <input type="radio" value="lunas" x-model="tipePembayaran" name="radio-tipe-bayar"
                            class="w-5 h-5">
                        <span class="font-black uppercase">Bayar Lunas</span>
                    </div>
                </label>
            </div>
        </div>

        {{-- INPUT DP --}}
        <div class="w-full sm:w-1/2"
             x-show="tipePembayaran === 'dp'"
             x-transition
             x-cloak>
            <x-input-label
                value="Nominal DP (Minimal Rp 1.000)"
                class="text-yellow-700 font-black uppercase text-[11px] mb-4 tracking-widest px-1" />

            <div class="p-6 border-2 border-yellow-200 bg-yellow-50/50 rounded-3xl">
                <div class="relative">
                    <span
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-yellow-600 font-black">
                        Rp
                    </span>

                    <input
                        type="number"
                        min="1000"
                        step="1000"
                        x-model.number="dpManual"
                        @input="validateDP"
                        class="pl-14 block w-full border-2 border-yellow-100 rounded-xl py-3 font-black text-yellow-900 bg-white">
                </div>

                <template x-if="dpError">
                    <p class="text-red-600 text-[11px] mt-2 font-bold italic"
                       x-text="dpError"></p>
                </template>
            </div>
        </div>
    </div>
</div>
