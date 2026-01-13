<div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
    @if ($item->mobil && $item->mobil->foto)
        <img src="{{ asset('storage/' . $item->mobil->foto) }}" alt="{{ $item->mobil->merek }}" class="w-full h-48 object-cover">
    @else
        <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">Tidak ada gambar</div>
    @endif

    <div class="p-5">
        <h3 class="text-lg font-semibold mb-2">{{ $item->mobil->merek ?? '-' }} - {{ $item->mobil->tipe ?? '-' }}</h3>
        <p class="text-sm text-gray-600 mb-1">Tanggal Sewa: {{ $item->tanggal_sewa }}</p>
        <p class="text-sm text-gray-600 mb-1">Tanggal Kembali: {{ $item->tanggal_kembali }}</p>

        <p class="text-sm text-gray-600 mb-1">
            Status:
            <span class="font-medium
                @if($item->status === 'selesai') text-green-600
                @elseif($item->status === 'pembayaran dp') text-yellow-600
                @elseif($item->status === 'menunggu pembayaran') text-blue-600
                @elseif($item->status === 'dibatalkan') text-red-600
                @else text-gray-600 @endif">
                {{ ucfirst($item->status) }}
            </span>
        </p>

        <p class="text-sm text-gray-600 mb-1">
            Total Harga: Rp {{ number_format($item->total_harga, 0, ',', '.') }} <br>
            DP Dibayarkan: Rp {{ number_format($item->dp_dibayarkan, 0, ',', '.') }} <br>
            @if($activeTab === 'sudah dibayar lunas')
                Pelunasan: Rp {{ number_format($item->sisa_bayar, 0, ',', '.') }}
            @else
                Sisa Bayar: Rp {{ number_format($item->sisa_bayar, 0, ',', '.') }}
            @endif
        </p>

        {{-- Kondisi Mobil Saat Ini, hanya tampil di tab "Belum Diambil" dan "Berlangsung" --}}
        @if($activeTab === 'sudah dibayar lunas' || $activeTab === 'berlangsung')
            <p class="text-sm text-gray-600 mb-1">
                Kondisi Mobil:
                <span class="font-medium {{ $item->kondisi_mobil ? 'text-gray-800' : 'text-gray-400' }}">
                    {{ $item->kondisi_mobil ?? 'Belum ada kondisi yang dicatat' }}
                </span>
            </p>
        @endif

        {{-- 🔹 Status Pengajuan Pembatalan --}}
        @php
            $reqBatal = $item->pembatalan ?? null;
            $isPendingCancel = $reqBatal && $reqBatal->approval_status === 'pending';
            $isRejectedCancel = $reqBatal && $reqBatal->approval_status === 'rejected';
        @endphp

        @if($isPendingCancel)
            <div class="w-full bg-yellow-50 text-yellow-800 border border-yellow-200 rounded-lg p-3 text-sm text-center mb-3">
                Pengajuan pembatalan <strong>menunggu persetujuan admin</strong>.
            </div>
        @endif

        {{-- 🔹 Tombol Aksi --}}
        <div class="flex flex-col items-center gap-2 mt-3">
            <a href="{{ route('mobils.show', $item->mobil_id) }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition w-full text-center">
                Lihat Detail Mobil
            </a>

            {{-- 🔸 Menunggu Pembayaran (NEW) --}}
            @if($activeTab === 'menunggu pembayaran')
                @unless($isPendingCancel)
                    <button onclick="bukaModalBatal({{ $item->id }})"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition w-full text-center">
                        Batalkan Pesanan
                    </button>
                @endunless
            @endif

            {{-- 🔸 Belum Lunas (DP) --}}
            @if($activeTab === 'pembayaran dp')
                <button onclick="bayarSisa({{ $item->id }})"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition w-full text-center">
                    Bayar Sisa
                </button>
            @endif

            {{-- 🔸 Belum Diambil (Lunas) --}}
            @if($activeTab === 'sudah dibayar lunas')
                <button onclick="bukaModal({{ $item->id }})"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition w-full text-center">
                    Cek Kondisi Mobil
                </button>

                @unless($isPendingCancel)
                    <button onclick="bukaModalBatal({{ $item->id }})"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition w-full text-center">
                        Batalkan Pesanan
                    </button>
                @endunless
            @endif

            {{-- 🔸 Berlangsung --}}
            @if($activeTab === 'berlangsung')
                <div x-data="{ openModal: false }"
                    class="bg-gray-100 border border-gray-300 rounded-lg p-3 text-sm text-gray-700 w-full">
                    <p><strong>Rundown Pengembalian:</strong></p>
                    <p>Tanggal Kembali: {{ $item->tanggal_kembali }}</p>
                    <p>Jam Estimasi: {{ \Carbon\Carbon::parse($item->jam_sewa)->format('H:i') }} WIB</p>
                    <p class="mt-2 text-gray-600">
                        Pastikan mobil dikembalikan sebelum
                        <span class="font-semibold">
                            {{ \Carbon\Carbon::parse($item->tanggal_kembali . ' ' . $item->jam_sewa)->format('d M Y, H:i') }} WIB
                        </span>.
                    </p>

                    <button @click="openModal = true"
                        class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition w-full text-center">
                        Selesaikan Peminjaman
                    </button>

                    @unless($isPendingCancel)
                        <button onclick="bukaModalBatal({{ $item->id }})"
                            class="mt-2 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition w-full text-center">
                            Batalkan Pesanan
                        </button>
                    @endunless

                    {{-- Modal Konfirmasi Pengembalian --}}
                    <div x-show="openModal"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-transition>
                        <div class="bg-white rounded-xl shadow-xl p-6 w-80 text-center">
                            <h2 class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi Pengembalian</h2>
                            <p class="text-gray-600 mb-4">Apakah Anda yakin ingin mengembalikan mobil sekarang?</p>

                            <div class="flex gap-3">
                                <button @click="openModal = false"
                                    class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 transition">
                                    Batal
                                </button>

                                <form action="{{ route('pengembalian.store', ['peminjaman_id' => $item->id]) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                                        Ya, Kembalikan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>