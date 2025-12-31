<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Form Peminjaman Mobil') }}
        </h2>
    </x-slot>

    <!-- Dependencies -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 sm:p-10 shadow-2xl rounded-3xl overflow-hidden transition duration-500 hover:shadow-cyan-400/50 text-gray-900"
                x-data="{
                    tanggalSewa: '',
                    jamSewa: '{{ now()->format('H:i') }}',
                    tanggalKembali: '',
                
                    // STATE UNTUK SOPIR (SIMPLE)
                    sopirAvailable: true, // boolean: true/false saja
                    sisaSopir: 2, // number: jumlah sopir tersedia
                    isCheckingDriver: false, // boolean: loading state
                    addOnSopir: 0, // 0 = tidak, 1 = ya
                    hargaSopirPerHari: 1500, // harga yang benar
                
                    // HARGA
                    hargaMobil: {{ $mobil->harga }},
                    tipePembayaran: 'dp',
                    dpManual: 1000,
                    dpError: '',
                
                    // HELPER FUNCTIONS
                    toIsoDate(dateStr) {
                        if (!dateStr) return null;
                        const parts = dateStr.split('-');
                        if (parts.length !== 3) return null;
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    },
                
                    durasiHari() {
                        if (!this.tanggalSewa || !this.tanggalKembali) return 0;
                        const start = new Date(this.toIsoDate(this.tanggalSewa) + 'T' + this.jamSewa);
                        const end = new Date(this.toIsoDate(this.tanggalKembali) + 'T' + this.jamSewa);
                        if (isNaN(start.getTime()) || isNaN(end.getTime())) return 0;
                        const diffHours = (end - start) / (1000 * 60 * 60);
                        return Math.ceil(diffHours / 24);
                    },
                
                    totalHarga() {
                        const durasi = this.durasiHari();
                        if (durasi <= 0) return 0;
                        const hargaDasar = durasi * this.hargaMobil;
                        const biayaSopir = this.addOnSopir ? durasi * this.hargaSopirPerHari : 0;
                        return hargaDasar + biayaSopir;
                    },
                
                    // ✅ FUNGSI UTAMA CEK SOPIR
                    checkDriver() {
                
                        console.log('=== checkDriver dipanggil ===');
                        console.log('tanggalSewa:', this.tanggalSewa);
                        console.log('tanggalKembali:', this.tanggalKembali);
                        // Reset state sopir
                        this.sopirAvailable = true;
                        this.sisaSopir = 2;
                        this.addOnSopir = 0;
                        if (!this.tanggalSewa || !this.tanggalKembali) {
                            console.log('Tanggal sewa atau kembali belum lengkap. Membatalkan pengecekan sopir.');
                            return;
                        }
                
                        this.isCheckingDriver = true;
                        const postData = {
                            _token: '{{ csrf_token() }}',
                            tanggal_sewa: this.tanggalSewa || '',
                            tanggal_kembali: this.tanggalKembali || ''
                        };
                        console.log('Data dikirim:', postData);
                
                        axios.post('{{ route('check.driver') }}', postData)
                            .then(response => {
                                console.log('✅ RESPONSE DARI SERVER:', response.data);
                
                                // ⚠️ PASTIKAN INI DIEKSEKUSI
                                this.sopirAvailable = response.data.available;
                                this.sisaSopir = response.data.sisa || response.data.total_sopir || 0;
                
                                console.log('Set state:', {
                                    sopirAvailable: this.sopirAvailable,
                                    sisaSopir: this.sisaSopir
                                });
                
                                if (!response.data.available) {
                                    this.addOnSopir = 0;
                                }
                            })
                            .catch(error => {
                                console.error('❌ ERROR:', error.response?.data || error.message);
                                this.sopirAvailable = false;
                                this.sisaSopir = 0;
                                this.addOnSopir = 0;
                            })
                            .finally(() => {
                                console.log('checkDriver selesai');
                                this.isCheckingDriver = false;
                            });
                    },
                
                    validateDP() {
                        if (this.tipePembayaran === 'lunas') {
                            this.dpError = '';
                            return;
                        }
                
                        this.dpManual = Number(this.dpManual);
                        const minDp = 1000;
                        const maxDp = 2000;
                        const total = this.totalHarga();
                
                        if (this.dpManual < minDp) {
                            this.dpError = 'Minimal DP adalah ' + this.formatRupiah(minDp);
                        } else if (this.dpManual > maxDp) {
                            this.dpError = 'Maksimal DP adalah ' + this.formatRupiah(maxDp);
                        } else if (this.dpManual > total && total > 0) {
                            this.dpError = 'DP tidak boleh melebihi Total Harga';
                        } else {
                            this.dpError = '';
                        }
                    },
                
                    sisaBayar() {
                        if (this.tipePembayaran === 'lunas') return 0;
                        const sisa = this.totalHarga() - this.dpManual;
                        return sisa < 0 ? 0 : sisa;
                    },
                
                    updateDP() {
                        if (this.tipePembayaran === 'lunas') {
                            this.dpManual = 0;
                            this.dpError = '';
                        } else {
                            if (this.dpManual < 1000) {
                                this.dpManual = 1000;
                            }
                            this.validateDP();
                        }
                    },
                
                    formatRupiah(number) {
                        return 'Rp ' + (Number(number) || 0).toLocaleString('id-ID');
                    }
                }" x-effect="updateDP()">
                <h1 class="text-3xl font-extrabold text-center text-cyan-700 mb-8 border-b-2 border-cyan-100 pb-4">
                    FORM PENYEWAAN MOBIL
                </h1>

                <form id="peminjaman-form" method="POST" action="{{ route('peminjaman.store') }}"
                    enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    <input type="hidden" name="mobil_id" value="{{ $mobil->id }}">
                    <input type="hidden" name="add_on_sopir" x-model="addOnSopir">
                    <input type="hidden" name="add_on_sopir" x-bind:value="addOnSopir">
                    <input type="hidden" name="tanggal_kembali_field" x-bind:value="tanggalKembali">
                    <input type="hidden" name="tipe_pembayaran" x-bind:value="tipePembayaran">

                    <!-- Detail Mobil & Gambar -->
                    <div class="bg-cyan-50 p-6 rounded-xl shadow-inner border border-cyan-200">
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <div class="w-full sm:w-1/3 flex-shrink-0">
                                <img src="{{ asset('storage/' . $mobil->foto) }}"
                                    onerror="this.onerror=null;this.src='https://placehold.co/256x160/22C55E/FFFFFF?text=CAR';"
                                    alt="Mobil"
                                    class="w-full h-40 object-cover rounded-xl shadow-md transition duration-300 hover:scale-[1.02]">
                            </div>
                            <div class="w-full sm:w-2/3 text-left">
                                <x-input-label value="Mobil yang Dipilih" class="text-gray-500" />
                                <p class="text-2xl font-bold text-cyan-800 mt-1">{{ $mobil->merek }}
                                    {{ $mobil->tipe }}</p>
                                <x-input-label value="Harga Sewa / Hari" class="mt-3 text-gray-500" />
                                <p class="text-xl font-extrabold text-green-600">Rp
                                    {{ number_format($mobil->harga, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Tanggal & Jam (GRID) -->
                    <div class="space-y-6 pt-4 border-t border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-700">Periode Sewa</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="tanggal_sewa" :value="__('Tanggal Sewa')" class="text-cyan-700 font-medium" />
                                <x-text-input id="tanggal_sewa" name="tanggal_sewa" type="text"
                                    placeholder="Pilih tanggal sewa"
                                    class="mt-1 block w-full border-cyan-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-lg"
                                    autocomplete="off" x-model="tanggalSewa" @change="checkDriver()" readonly
                                    required />
                            </div>
                            <div>
                                <x-input-label for="jam_sewa" :value="__('Jam Sewa (WIB)')" class="text-cyan-700 font-medium" />
                                <x-text-input id="jam_sewa" name="jam_sewa" type="time"
                                    class="mt-1 block w-full border-cyan-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-lg"
                                    x-model="jamSewa" required />
                            </div>
                            <div class="sm:col-span-1">
                                <x-input-label for="tanggal_kembali" :value="__('Tanggal Kembali')"
                                    class="text-cyan-700 font-medium" />
                                <x-text-input id="tanggal_kembali" name="tanggal_kembali" type="text"
                                    placeholder="Pilih tanggal kembali"
                                    class="mt-1 block w-full border-cyan-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-lg"
                                    autocomplete="off" x-model="tanggalKembali" @change="checkDriver()" readonly
                                    required />

                            </div>
                        </div>
                        <!-- Info Deadline -->
                        <p id="deadline-info"
                            class="mt-2 text-sm text-cyan-600 italic p-3 bg-cyan-50 border-l-4 border-cyan-400 rounded">
                        </p>
                    </div>

                    <!-- --- LOGIKA BARU: ADD-ON SOPIR DINAMIS --- -->
                    <div class="pt-4 border-t border-gray-100"
                        x-show="tanggalSewa && tanggalKembali && durasiHari() > 0">
                        <!-- Loading State -->
                        <div x-show="isCheckingDriver" x-cloak class="flex items-center gap-2 text-blue-600 mb-3">
                            <span class="text-sm">Memeriksa ketersediaan sopir...</span>
                        </div>

                        <!-- ✅ OPSI JIKA SOPIR TERSEDIA -->
                        <div x-show="!isCheckingDriver && sopirAvailable === true" x-transition.opacity.duration.300ms>
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                Butuh Sopir?
                                <span class="text-green-600 font-semibold">
                                    (Tersedia: <span x-text="sisaSopir"></span> sopir)
                                </span>
                            </label>

                            <div class="flex space-x-4">
                                <label class="inline-flex items-center px-4 py-2 border rounded-lg cursor-pointer"
                                    :class="{ 'bg-blue-50 border-blue-500': addOnSopir === 0 }">
                                    <input type="radio" name="add_on_sopir" value="0" x-model="addOnSopir"
                                        class="text-blue-600 mr-2">
                                    <span>Tidak, Lepas Kunci</span>
                                </label>
                                <label class="inline-flex items-center px-4 py-2 border rounded-lg cursor-pointer"
                                    :class="{ 'bg-blue-50 border-blue-500': addOnSopir === 1 }">
                                    <input type="radio" name="add_on_sopir" value="1" x-model="addOnSopir"
                                        class="text-blue-600 mr-2">
                                    <span>Ya, Butuh Sopir (+Rp 1.500/hari)</span>
                                </label>
                            </div>
                        </div>

                        <!-- ❌ PESAN JIKA TIDAK ADA SOPIR TERSEDIA -->
                        <div x-show="!isCheckingDriver && sopirAvailable === false" x-transition.opacity.duration.500ms
                            class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                ⚠️ Maaf, sopir sedang penuh
                            </p>
                        </div>
                    </div>

                    <!-- Jenis Pembayaran -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <x-input-label value="Jenis Pembayaran" class="text-cyan-700 font-medium mb-3" />
                        <div class="flex gap-6">
                            <label
                                class="inline-flex items-center space-x-2 p-3 border rounded-lg transition duration-150"
                                :class="{ 'bg-cyan-100 border-cyan-500 shadow-md': tipePembayaran === 'dp', 'bg-gray-50 border-gray-300 hover:border-cyan-400': tipePembayaran !== 'dp' }">
                                <input type="radio" value="dp" x-model="tipePembayaran"
                                    name="radio-tipe-bayar" class="text-cyan-600 focus:ring-cyan-500">
                                <span>Bayar DP</span>
                            </label>
                            <label
                                class="inline-flex items-center space-x-2 p-3 border rounded-lg transition duration-150"
                                :class="{ 'bg-cyan-100 border-cyan-500 shadow-md': tipePembayaran === 'lunas', 'bg-gray-50 border-gray-300 hover:border-cyan-400': tipePembayaran !== 'lunas' }">
                                <input type="radio" value="lunas" x-model="tipePembayaran"
                                    name="radio-tipe-bayar" class="text-cyan-600 focus:ring-cyan-500">
                                <span>Bayar Lunas</span>
                            </label>
                        </div>
                    </div>

                    <!-- Input DP (Conditional) -->
                    <div x-show="tipePembayaran === 'dp'" x-cloak x-transition.opacity
                        class="mt-4 p-4 border border-yellow-200 bg-yellow-50 rounded-lg">
                        <label for="dpManual" class="block text-sm font-medium text-gray-700">Nominal DP <span
                                class="text-xs text-gray-500">(Minimal Rp1.000, Maksimal Rp2.000)</span></label>
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                            <input type="number" id="dpManual" name="dp" x-model.number="dpManual"
                                @input="validateDP" :required="tipePembayaran === 'dp'"
                                :disabled="tipePembayaran === 'lunas'"
                                class="pl-10 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500 disabled:opacity-50"
                                min="1000" max="2000" step="1000">
                        </div>
                        <template x-if="dpError">
                            <p class="text-red-600 text-sm mt-1 flex items-center" x-text="dpError"></p>
                        </template>
                    </div>

                    <!-- Ringkasan Pembayaran -->
                    <div class="mt-8 pt-4 border-t-2 border-cyan-400 bg-cyan-50 p-6 rounded-xl shadow-lg"
                        x-show="durasiHari() > 0">
                        <h2 class="text-xl font-bold text-cyan-800 mb-4">Ringkasan Pembayaran</h2>
                        <input type="hidden" name="add_on_sopir" x-model="addOnSopir">
                        <div class="space-y-2 text-gray-700">
                            <div class="flex justify-between"><span>Durasi Sewa:</span><span
                                    class="font-bold text-cyan-700" x-text="durasiHari() + ' hari'"></span></div>
                            <div class="flex justify-between"><span>Biaya Sewa Mobil:</span><span
                                    class="font-semibold text-cyan-700"
                                    x-text="formatRupiah(durasiHari() * hargaMobil)"></span></div>
                            <div class="flex justify-between" x-show="addOnSopir === 1"><span>Biaya Sopir:</span><span
                                    class="font-semibold text-cyan-700"
                                    x-text="formatRupiah(durasiHari() * hargaSopirPerHari)"></span></div>
                            <div class="border-t border-cyan-200 pt-3 flex justify-between text-lg font-bold">
                                <span>TOTAL HARGA:</span><span class="text-red-600"
                                    x-text="formatRupiah(totalHarga())"></span>
                            </div>
                            <div class="border-t border-cyan-200 pt-3 mt-3">
                                <div class="flex justify-between"><span class="font-bold"
                                        x-text="tipePembayaran === 'lunas' ? 'BAYAR LUNAS (DP)' : 'Nominal DP yang dibayar:'"></span><span
                                        class="font-bold text-green-700" x-text="formatRupiah(dpManual)"></span></div>
                                <template x-if="tipePembayaran === 'dp'">
                                    <div class="flex justify-between mt-1 text-sm text-gray-600"><span>Sisa
                                            Pembayaran:</span><span class="font-semibold"
                                            x-text="formatRupiah(sisaBayar())"></span></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div x-data="{ open: false, selected: 'Transfer Bank', value: 'transfer' }" class="relative w-full pt-4 border-t border-gray-100">
                        <x-input-label value="Metode Pembayaran" class="text-cyan-700 font-medium" />
                        <button type="button" @click="open=!open"
                            class="p-3 w-full border border-cyan-400 bg-white rounded-lg flex items-center justify-between hover:bg-cyan-50 transition duration-150 mt-1 shadow-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                            <span x-text="selected" class="font-medium text-gray-800"></span>
                            <svg class="w-4 h-4 text-cyan-600 transition-transform" :class="{ 'rotate-180': open }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak @click.away="open=false"
                            class="absolute z-20 mt-1 w-full bg-white border border-cyan-200 rounded-lg shadow-xl origin-top-right">
                            <ul class="divide-y divide-gray-100">
                                <li @click="selected='Transfer Bank'; value='transfer'; open=false"
                                    class="p-3 hover:bg-cyan-100 cursor-pointer rounded-t-lg">Transfer Bank (Via
                                    Midtrans)</li>
                                <li @click="selected='Bayar di Tempat'; value='cash'; open=false"
                                    class="p-3 hover:bg-cyan-100 cursor-pointer rounded-b-lg">Bayar di Tempat (Hanya
                                    untuk sisa pembayaran)</li>
                            </ul>
                        </div>
                        <input type="hidden" name="metode_pembayaran" x-model="value">
                    </div>

                    <!-- Tombol Submit -->
                    <div class="flex items-center justify-end mt-8 pt-4 border-t border-gray-200">
                        <x-primary-button type="button" id="pay-button"
                            class="ml-3 w-full sm:w-auto px-6 py-3 text-lg font-semibold bg-cyan-600 hover:bg-cyan-700 transition duration-200 shadow-xl hover:shadow-cyan-400/50">
                            {{ __('Ajukan Peminjaman & Lanjutkan Pembayaran') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Datepicker & Toast -->
    <script>
        const bookedDates = @json($bookedDates ?? []);
        const disabledDates = bookedDates.map(d => d);

        function showToast(message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'bg-red-600 text-white p-3 rounded-lg shadow-xl mb-2 animate-fadein border border-red-800';
            toast.textContent = message;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('animate-fadeout');
                toast.addEventListener('animationend', () => toast.remove());
            }, 3000);
        }

        $('#tanggal_sewa').datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: 0,
            beforeShowDay: function(date) {
                var string = $.datepicker.formatDate('dd-mm-yy', date);
                var isAvailable = disabledDates.indexOf(string) == -1;
                return [isAvailable, isAvailable ? "" : "disabled-date"];
            },
            onSelect: function(selectedDate) {
                var parts = selectedDate.split("-");
                var minReturn = new Date(parts[2], parts[1] - 1, parts[0]);
                minReturn.setDate(minReturn.getDate() + 1);
                $('#tanggal_kembali').datepicker("option", "minDate", minReturn).val("");

                // Trigger event input agar Alpine model terupdate
                this.dispatchEvent(new Event('input'));
                updateJamSewa();

                // 🔥 TRIGGER Cek Sopir Alpine
                const alpineEl = document.querySelector('[x-data]');
                if (alpineEl && alpineEl.__x) {
                    alpineEl.__x.$data.tanggalSewa = selectedDate;
                    alpineEl.__x.$data.tanggalKembali = ''; // Reset kembali karena baru pilih sewa
                    alpineEl.__x.$data.checkDriver();
                }
            }
        });

        // 🔹 MODIFIED: Datepicker Tanggal Kembali dengan Pengecekan Rentang & Sopir
        $('#tanggal_kembali').datepicker({
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(date) {
                var string = $.datepicker.formatDate('dd-mm-yy', date);
                var isAvailable = disabledDates.indexOf(string) == -1;
                return [isAvailable, isAvailable ? "" : "disabled-date"];
            },
            onSelect: function(selectedDate) {
                var sewa = $('#tanggal_sewa').val();
                if (sewa) {
                    var partsSewa = sewa.split("-");
                    var sewaDate = new Date(partsSewa[2], partsSewa[1] - 1, partsSewa[0]);
                    var partsKembali = selectedDate.split("-");
                    var kembaliDate = new Date(partsKembali[2], partsKembali[1] - 1, partsKembali[0]);

                    // Validasi Dasar (Tanggal Kembali harus > Tanggal Sewa)
                    if (kembaliDate <= sewaDate) {
                        showToast("⚠ Tanggal kembali harus lebih besar dari tanggal sewa!");
                        $(this).val('');
                        $('#deadline-info').text("");
                        this.dispatchEvent(new Event('input'));
                        return;
                    }

                    // 🔍 VALIDASI TAMBAHAN: Cek apakah rentang tanggal melompati booking
                    var isOverlap = false;
                    for (var d = new Date(sewaDate); d <= kembaliDate; d.setDate(d.getDate() + 1)) {
                        var checkStr = $.datepicker.formatDate('dd-mm-yy', d);
                        if (disabledDates.indexOf(checkStr) !== -1) {
                            isOverlap = true;
                            break;
                        }
                    }

                    if (isOverlap) {
                        showToast("⚠ Rentang tanggal mencakup tanggal yang sudah dibooking oleh orang lain!");
                        $(this).val(''); // Reset input
                        $('#deadline-info').text("");
                    } else {
                        // Jika valid, update info deadline
                        updateDeadlineInfo();

                        // 🔥 TRIGGER Cek Sopir Alpine saat tanggal kembali valid
                        const alpineEl = document.querySelector('[x-data]');
                        if (alpineEl && alpineEl.__x) {
                            alpineEl.__x.$data.tanggalKembali = selectedDate;
                            alpineEl.__x.$data.checkDriver();
                        }
                    }
                }

                this.dispatchEvent(new Event('input')); // Update Alpine
            }
        });

        function updateJamSewa() {
            var tanggalSewa = $('#tanggal_sewa').val();
            var jamInput = $('#jam_sewa');
            if (!tanggalSewa) return;
            var today = new Date();
            var parts = tanggalSewa.split("-");
            var selected = new Date(parts[2], parts[1] - 1, parts[0]);
            jamInput.removeAttr("min");
            if (selected.toDateString() === today.toDateString()) {
                var nextHour = new Date(today.getTime() + (60 * 60 * 1000));
                var minJam = String(nextHour.getHours()).padStart(2, '0') + ':' + String(nextHour.getMinutes()).padStart(2,
                    '0');
                jamInput.attr("min", minJam);
                if (jamInput.val() < minJam) jamInput.val(minJam);
            }
            jamInput[0].dispatchEvent(new Event('input'));
            updateDeadlineInfo(); // Panggil juga saat update jam
        }

        // 🔹 Tampilkan deadline pengembalian
        function updateDeadlineInfo() {
            var tanggalKembali = $('#tanggal_kembali').val();
            var jamSewa = $('#jam_sewa').val();

            if (tanggalKembali && jamSewa) {
                // Cari scope Alpine terdekat
                const alpineEl = document.querySelector('[x-data]');
                if (alpineEl && alpineEl.__x) {
                    const durasi = alpineEl.__x.$data.durasiHari();

                    $('#deadline-info').html(
                        `✅ Durasi Sewa: <span class="font-bold text-cyan-800">${durasi} Hari</span>. Maksimal pengembalian pada pukul <span class="font-semibold text-cyan-800">${jamSewa} WIB</span> tanggal <span class="font-semibold text-cyan-800">${tanggalKembali}</span>.`
                    );
                } else {
                    $('#deadline-info').html(
                        `⏰ Maksimal pengembalian pada pukul <span class="font-semibold text-cyan-800">${jamSewa} WIB</span> tanggal <span class="font-semibold text-cyan-800">${tanggalKembali}</span>.`
                    );
                }
            } else {
                $('#deadline-info').html("");
            }
        }

        // 🔸 Update deadline saat user ubah jam 
        $('#jam_sewa').on('change', function() {
            updateDeadlineInfo();
            // Pemicu Alpine untuk update durasi/total harga
            const alpineEl = document.querySelector('[x-data]');
            if (alpineEl && alpineEl.__x) {
                alpineEl.__x.$data.jamSewa = this.value; // Manual set if needed
            }
        });

        $(document).ready(function() {
            updateJamSewa();
        });

        // Axios Submit Logic
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');
            const form = document.getElementById('peminjaman-form');

            payButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (!form.checkValidity()) {
                    showToast('⚠️ Mohon lengkapi semua field yang diperlukan!');
                    form.reportValidity();
                    return;
                }
                const formData = new FormData(form);
                payButton.disabled = true;
                payButton.textContent = 'Memproses...';

                axios.post(form.action, formData)
                    .then(function(response) {
                        payButton.disabled = false;
                        payButton.textContent = 'Ajukan Peminjaman & Lanjutkan Pembayaran';

                        if (response.data.snap_token) {
                            const peminjamanId = response.data.peminjaman_id;

                            snap.pay(response.data.snap_token, {
                                onSuccess: function() {
                                    window.location.href =
                                        "{{ route('payment.success') }}";
                                },
                                onError: function() {
                                    window.location.href = "{{ route('payment.failed') }}";
                                },
                                onClose: function() {
                                    console.log(
                                        '❌ Snap ditutup. Menghapus data booking...');
                                    showToast(
                                        '🚫 Pembayaran dibatalkan. Data peminjaman sedang dihapus...'
                                    );

                                    fetch(`/peminjaman/${peminjamanId}/cancel`, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector(
                                                        'meta[name="csrf-token"]')
                                                    .content
                                            },
                                            body: JSON.stringify({
                                                _method: 'DELETE'
                                            })
                                        })
                                        .then(res => {
                                            if (res.ok) {
                                                console.log(
                                                    '✅ Data berhasil dihapus dari database.'
                                                );
                                                showToast(
                                                    '🗑️ Data peminjaman telah dihapus. Silakan pesan ulang jika ingin melanjutkan.'
                                                );
                                            } else {
                                                console.error(
                                                    'Gagal menghapus data di server.'
                                                );
                                            }
                                        })
                                        .catch(err => console.error(
                                            'Error saat request hapus:', err));
                                }
                            });
                        } else if (response.data.error) {
                            showToast('❌ ' + response.data.error);
                        }
                    })
                    .catch(function(error) {
                        payButton.disabled = false;
                        payButton.textContent = 'Ajukan Peminjaman & Lanjutkan Pembayaran';
                        let msg = error.response?.data?.message || error.response?.data?.error ||
                            'Terjadi kesalahan.';
                        showToast('❌ ' + msg);
                    });
            });
        });
    </script>

    <div id="toast-container" class="fixed top-4 right-4 z-50 pointer-events-none"></div>
    <style>
        .ui-datepicker-unselectable.ui-state-disabled,
        .disabled-date a {
            background: #ff0000 !important;
            color: white !important;
            opacity: 0.6;
            cursor: not-allowed !important;
        }

        @keyframes fadein {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeout {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        .animate-fadein {
            animation: fadein 0.3s forwards;
        }

        .animate-fadeout {
            animation: fadeout 0.5s forwards;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>
