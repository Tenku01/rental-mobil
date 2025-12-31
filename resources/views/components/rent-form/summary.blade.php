<div
    x-cloak
    x-show="typeof durasiHari === 'function' && durasiHari() > 0"
    class="mt-10 pt-8 border-t-8 border-cyan-700 bg-gray-50 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden"
>
    <h2 class="text-2xl font-black text-cyan-900 mb-8 tracking-tighter uppercase border-b-2 border-cyan-100 pb-4">
        Invoice
    </h2>

    <div class="space-y-4 font-bold text-gray-700">
        <div class="flex justify-between items-center py-2 border-b border-gray-100">
            <span class="text-xs opacity-60 uppercase tracking-widest">Durasi Sewa</span>
            <span class="text-xl font-black text-cyan-700"
                  x-text="durasiHari() + ' HARI'">
            </span>
        </div>

        <div class="flex justify-between items-center py-2">
            <span class="text-xs opacity-60 uppercase tracking-widest">Subtotal Unit</span>
            <span class="text-lg font-black"
                  x-text="formatRupiah((durasiHari() || 0) * (hargaMobil || 0))">
            </span>
        </div>

        <div
            class="flex justify-between items-center py-2 text-cyan-600"
            x-show="addOnSopir === 1"
        >
            <span class="text-xs opacity-60 uppercase tracking-widest">Biaya Sopir</span>
            <span class="text-lg font-black"
                  x-text="'+ ' + formatRupiah((durasiHari() || 0) * 150000)">
            </span>
        </div>

        <div class="mt-6 pt-6 border-t-2 border-gray-200 flex justify-between items-end">
            <div class="flex flex-col">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em]">
                    Grand Total
                </span>
                <span class="text-4xl font-black text-red-600 tracking-tighter leading-none"
                      x-text="formatRupiah(totalHarga())">
                </span>
            </div>

            <div class="bg-green-600 text-white p-6 rounded-3xl shadow-xl flex flex-col items-end min-w-[200px]">
                <span class="text-[10px] font-black uppercase tracking-[0.3em] opacity-80 mb-1"
                      x-text="tipePembayaran === 'lunas' ? 'LUNAS SEKARANG' : 'DP SEKARANG'">
                </span>

                <span class="text-3xl font-black tracking-tighter leading-none"
                      x-text="formatRupiah(tipePembayaran === 'lunas'
                            ? totalHarga()
                            : (dpManual || 0))">
                </span>
            </div>
        </div>
    </div>
</div>
