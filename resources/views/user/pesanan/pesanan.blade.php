<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pesanan Saya') }}
        </h2>
    </x-slot>

    {{-- Script Midtrans --}}
    @push('scripts')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
        {{-- Pastikan komponen scripts-pesanan memuat fungsi callMidtransSnap & callManualPayment --}}
        @include('components.scripts-pesanan')
    @endpush

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 🔹 Tabs Filter Pesanan --}}
            <div class="mb-6 flex flex-wrap gap-2 justify-center">
                @php
                    $tabs = [
                        'semua' => 'Semua Pesanan',
                        'menunggu pembayaran' => 'Menunggu Pembayaran',
                        'pembayaran dp' => 'Belum Lunas',
                        'sudah dibayar lunas' => 'Belum Diambil',
                        'berlangsung' => 'Berlangsung',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ];
                    $activeTab = request('status') ?? 'semua';
                @endphp

                @foreach ($tabs as $key => $label)
                    <a href="?status={{ $key }}"
                        class="px-4 py-2 rounded-full border 
                        {{ $activeTab === $key ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100' }}
                        transition">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- 🔹 Filter Berdasarkan Tab --}}
            @php
                $filtered = $activeTab === 'semua'
                    ? $peminjaman
                    : $peminjaman->filter(fn($item) => $item->status === $activeTab);
            @endphp

            @if ($filtered->isEmpty())
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <p class="text-gray-600">Tidak ada pesanan pada kategori ini.</p>
                    <a href="{{ route('mobils.index') }}"
                        class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Pesan Mobil Sekarang
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($filtered as $item)

                        {{-- ========================================================= --}}
                        {{-- 🔹 TAB SELESAI --}}
                        {{-- ========================================================= --}}
                        @if ($activeTab === 'selesai')
                            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                                @if ($item->mobil && $item->mobil->foto)
                                    <img src="{{ asset('storage/' . $item->mobil->foto) }}"
                                            alt="{{ $item->mobil->merek }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">
                                        Tidak ada gambar
                                    </div>
                                @endif

                                <div class="p-5">
                                    <h3 class="text-lg font-semibold mb-2">
                                        {{ $item->mobil->merek ?? '-' }} - {{ $item->mobil->tipe ?? '-' }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-1">Tanggal Sewa: {{ $item->tanggal_sewa }}</p>
                                    <p class="text-sm text-gray-600 mb-1">Tanggal Kembali: {{ $item->tanggal_kembali }}</p>
                                    <p class="text-sm text-gray-600 mb-1">
                                        Status: <span class="font-medium text-green-600">{{ ucfirst($item->status) }}</span>
                                    </p>

                                    @if ($item->pengembalian)
                                        <div class="mt-3 bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm text-gray-700">
                                            
                                            @php
                                                // Menggunakan accessor dari model Pengembalian
                                                $totalDenda = $item->pengembalian->total_outstanding_fine ?? 0; 
                                                
                                                // Mengambil status langsung dari DB
                                                // Kemungkinan nilai: 'menunggu pengecekan', 'selesai pengecekan', 'menunggu_pembayaran_midtrans', 'dibayar'
                                                $statusPengembalian = $item->pengembalian->status; 
                                                
                                                $isAdaDenda = $totalDenda > 0;
                                                $isSudahDibayar = $item->pengembalian->status_pembayaran_denda === 'dibayar';
                                            @endphp

                                            <p class="mb-1"><strong>Total Denda:</strong> Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>

                                            <p class="mb-1"><strong>Status Pengembalian:</strong>
                                                {{-- Sesuaikan string pengecekan dengan Controller --}}
                                                @if ($statusPengembalian === 'menunggu pengecekan')
                                                    <span class="text-yellow-600 font-medium">Dalam Pengecekan Staff</span>
                                                
                                                @elseif($statusPengembalian === 'selesai pengecekan' && $isAdaDenda)
                                                    <span class="text-red-600 font-bold">Pengecekan Selesai (Ada Denda)</span>
                                                
                                                @elseif($statusPengembalian === 'selesai pengecekan' && !$isAdaDenda)
                                                    <span class="text-green-600 font-bold">Pengecekan Selesai (Tanpa Denda)</span>
                                                
                                                @elseif($statusPengembalian === 'menunggu_pembayaran_midtrans')
                                                    <span class="text-blue-600 font-medium">Menunggu Pembayaran Midtrans</span>
                                                
                                                @elseif($statusPengembalian === 'menunggu_verifikasi_transfer')
                                                    <span class="text-yellow-600 font-medium">Menunggu Verifikasi Transfer Manual</span>
                                                
                                                @elseif($statusPengembalian === 'menunggu_pembayaran_tunai')
                                                    <span class="text-yellow-600 font-medium">Menunggu Pembayaran Tunai</span>
                                                
                                                @elseif($statusPengembalian === 'completed' || $statusPengembalian === 'Selesai')
                                                    <span class="text-green-600 font-medium">Selesai & Lunas</span>
                                                
                                                @else
                                                    {{-- Fallback untuk status lain --}}
                                                    <span class="text-gray-600 font-medium">{{ ucfirst(str_replace('_', ' ', $statusPengembalian)) }}</span>
                                                @endif
                                            </p>

                                            {{-- 🔹 Tombol Bayar Denda --}}
                                            {{-- Muncul jika: Selesai Dicek Staff AND Ada Tagihan AND Belum Lunas --}}
                                            @if ($statusPengembalian === 'selesai pengecekan' && $isAdaDenda && !$isSudahDibayar)
                                                <div x-data="{ openModalBayar: false, metodePilihan: 'transfer' }" class="mt-3">
                                                    <button @click="openModalBayar = true"
                                                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition w-full text-center font-bold animate-pulse">
                                                        Bayar Denda (Rp {{ number_format($totalDenda, 0, ',', '.') }})
                                                    </button>

                                                    {{-- Modal Pembayaran Denda --}}
                                                    <div x-show="openModalBayar"
                                                        style="display: none;" 
                                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                                        x-transition.opacity>
                                                        <div @click.outside="openModalBayar = false" class="bg-white rounded-xl shadow-2xl p-6 w-96 max-w-sm text-center transform transition-all duration-300">
                                                            <h2 class="text-lg font-bold text-gray-800 mb-2">Pilih Metode Pembayaran</h2>
                                                            <p class="text-sm text-gray-600 mb-4">Total Denda: <span class="font-bold text-red-600">Rp {{ number_format($totalDenda, 0, ',', '.') }}</span></p>

                                                            <div class="flex flex-col gap-3 mb-5 text-left">
                                                                {{-- Pilihan 1: Midtrans --}}
                                                                <label class="border p-3 rounded-xl cursor-pointer transition" 
                                                                    :class="metodePilihan === 'transfer' ? 'border-blue-600 bg-blue-50 shadow' : 'border-gray-300 hover:bg-gray-100'">
                                                                    <input type="radio" x-model="metodePilihan" value="transfer" class="hidden">
                                                                    <span class="ml-2 font-medium text-gray-800">Transfer / E-Wallet</span>
                                                                    <p class="text-xs text-gray-500 ml-6">Otomatis lunas (VA, QRIS, dll)</p>
                                                                </label>
                                                                
                                                                {{-- Pilihan 2: Tunai/Manual --}}
                                                                <label class="border p-3 rounded-xl cursor-pointer transition" 
                                                                    :class="metodePilihan === 'tunai' ? 'border-blue-600 bg-blue-50 shadow' : 'border-gray-300 hover:bg-gray-100'">
                                                                    <input type="radio" x-model="metodePilihan" value="tunai" class="hidden">
                                                                    <span class="ml-2 font-medium text-gray-800">Bayar Tunai</span>
                                                                    <p class="text-xs text-gray-500 ml-6">Bayar di kantor/resepsionis</p>
                                                                </label>
                                                            </div>

                                                            <div class="flex gap-3">
                                                                <button type="button" @click="openModalBayar = false"
                                                                    class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 transition">
                                                                    Batal
                                                                </button>
                                                                
                                                                <button type="button"
                                                                    @click="
                                                                        if (metodePilihan === 'transfer') {
                                                                            callMidtransSnap('{{ $item->pengembalian->kode_pengembalian }}');
                                                                            openModalBayar = false;
                                                                        } else if (metodePilihan === 'tunai') {
                                                                            callManualPayment('{{ $item->pengembalian->kode_pengembalian }}', 'tunai');
                                                                            openModalBayar = false; 
                                                                        }
                                                                    "
                                                                    class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                                                                    Konfirmasi
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($statusPengembalian === 'selesai pengecekan' && !$isAdaDenda)
                                                <p class="mt-2 text-green-600 font-medium">Tidak ada denda yang perlu dibayar.</p>
                                            @elseif($isSudahDibayar)
                                                <p class="mt-2 text-green-600 font-medium">✅ Denda sudah dibayar ({{ ucfirst($item->pengembalian->metode_pembayaran) }})</p>
                                            @endif
                                            
                                        </div>
                                    @endif

                                    <a href="{{ route('mobils.show', $item->mobil_id) }}"
                                        class="mt-3 block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                                        Lihat Detail Mobil
                                    </a>
                                </div>
                            </div>

                        {{-- ========================================================= --}}
                        {{-- 🔹 TAB DIBATALKAN --}}
                        {{-- ========================================================= --}}
                        @elseif ($activeTab === 'dibatalkan')
                            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                                @if ($item->mobil && $item->mobil->foto)
                                    <img src="{{ asset('storage/' . $item->mobil->foto) }}" 
                                            alt="{{ $item->mobil->merek }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">
                                        Tidak ada gambar
                                    </div>
                                @endif

                                <div class="p-5">
                                    <h3 class="text-lg font-semibold mb-2">
                                        {{ $item->mobil->merek ?? '-' }} - {{ $item->mobil->tipe ?? '-' }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-1">Tanggal Sewa: {{ $item->tanggal_sewa }}</p>
                                    <p class="text-sm text-gray-600 mb-3">
                                        Status: <span class="font-medium text-red-600">{{ ucfirst($item->status) }}</span>
                                    </p>

                                    @php $batal = $item->pembatalan ?? null; @endphp
                                    <div class="bg-rose-50 border border-rose-200 rounded-lg p-3 text-sm text-rose-900">
                                        <p class="mb-1"><strong>Dibatalkan pada:</strong> {{ optional($batal)->cancelled_at?->format('d M Y H:i') ?? '-' }}</p>
                                        <p class="mb-1"><strong>Alasan:</strong> {{ $batal->alasan ?? '-' }}</p>
                                        <p class="mb-1">
                                            <strong>Status Refund:</strong>
                                            @if(optional($batal)->refund_status === 'pending_refund')
                                                <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-800">Pending</span>
                                            @elseif(optional($batal)->refund_status === 'refunded')
                                                <span class="px-2 py-0.5 rounded bg-green-100 text-green-800">Refunded</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-800">No Refund</span>
                                            @endif
                                        </p>
                                        <p class="mb-1"><strong>Nominal Refund:</strong> Rp {{ number_format(optional($batal)->jumlah_refund ?? 0, 0, ',', '.') }}</p>
                                    </div>

                                    <a href="{{ route('mobils.show', $item->mobil_id) }}"
                                        class="mt-3 block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                                        Lihat Detail Mobil
                                    </a>
                                </div>
                            </div>

                        {{-- ========================================================= --}}
                        {{-- 🔹 TAB LAIN (Default Card) --}}
                        {{-- ========================================================= --}}
                        @else
                            @include('components.card-pesanan-lain', ['item' => $item, 'activeTab' => $activeTab])
                        @endif
                        @include('components.scripts-pesanan')
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>