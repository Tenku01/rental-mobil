@props(['mobil'])

<input type="hidden" name="mobil_id" value="{{ $mobil->id }}">
<input type="hidden" name="add_on_sopir" x-model="addOnSopir">
<input type="hidden" name="tanggal_kembali" x-model="tanggalKembali">
<input type="hidden" name="tipe_pembayaran" x-model="tipePembayaran">
<input type="hidden" name="dp" x-model="dpManual">

<div class="bg-cyan-50 p-6 rounded-3xl shadow-inner border border-cyan-200">
    <div class="flex flex-col sm:flex-row items-center gap-8">
        <div class="w-full sm:w-1/3">
            <img src="{{ asset('storage/' . $mobil->foto) }}"
                 class="w-full h-44 object-cover rounded-2xl shadow-lg">
        </div>

        <div class="w-full sm:w-2/3">
            <span class="text-xs font-black text-cyan-600 uppercase tracking-widest">
                Unit Terpilih
            </span>

            <h2 class="text-3xl font-black text-cyan-800 mt-1 uppercase">
                {{ $mobil->merek }} {{ $mobil->tipe }}
            </h2>

            <div class="mt-4">
                <span class="text-xs font-bold text-gray-500 uppercase">
                    Harga / Hari
                </span>
                <p class="text-2xl font-black text-green-600">
                    Rp {{ number_format($mobil->harga, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
</div>
