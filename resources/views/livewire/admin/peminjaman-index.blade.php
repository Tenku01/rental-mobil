<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold text-gray-800">Transaksi Sewa (Read Only)</h2>
                
                <div class="flex gap-2 w-full md:w-auto">
                    <!-- Filter Status -->
                    <select wire:model.live="filterStatus" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">Semua Status</option>
                        <option value="menunggu pembayaran">Menunggu Pembayaran</option>
                        <option value="sudah dibayar lunas">Lunas / Siap Ambil</option>
                        <option value="berlangsung">Sedang Berlangsung</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>

                    <!-- Search -->
                    <input wire:model.live="search" type="text" placeholder="Cari User / Mobil..." class="border-gray-300 rounded-md shadow-sm text-sm">
                </div>
            </div>

            <!-- Tabel Transaksi -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID / User</th>
                            <th class="py-3 px-6 text-left">Mobil</th>
                            <th class="py-3 px-6 text-center">Jadwal</th>
                            <th class="py-3 px-6 text-center">Keuangan</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @forelse($peminjaman as $trx)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                <div class="font-bold">#{{ $trx->id }}</div>
                                <div class="text-xs text-gray-500">{{ $trx->user->name ?? 'User Hapus' }}</div>
                            </td>
                            <td class="py-3 px-6 text-left">
                                <div class="font-medium">{{ $trx->mobil->merek ?? '-' }}</div>
                                <div class="text-xs">{{ $trx->mobil->tipe ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="text-xs">
                                    {{ \Carbon\Carbon::parse($trx->tanggal_sewa)->format('d M') }} - 
                                    {{ \Carbon\Carbon::parse($trx->tanggal_kembali)->format('d M Y') }}
                                </div>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="font-bold">Total: Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</div>
                                @if($trx->sisa_bayar > 0)
                                    <span class="text-red-500 text-xs font-semibold">Kurang: Rp {{ number_format($trx->sisa_bayar, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-green-500 text-xs font-semibold">Lunas</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                @php
                                    $badge = match($trx->status) {
                                        'menunggu pembayaran' => 'bg-yellow-100 text-yellow-800',
                                        'sudah dibayar lunas' => 'bg-blue-100 text-blue-800',
                                        'berlangsung' => 'bg-green-100 text-green-800',
                                        'selesai' => 'bg-gray-200 text-gray-800',
                                        'dibatalkan' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100'
                                    };
                                @endphp
                                <span class="{{ $badge }} py-1 px-3 rounded-full text-xs font-bold uppercase">
                                    {{ $trx->status }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <!-- HANYA TOMBOL DETAIL -->
                                <button wire:click="showDetail({{ $trx->id }})" class="bg-blue-100 text-blue-600 hover:bg-blue-200 px-3 py-1 rounded-md text-xs font-semibold transition duration-200">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">Belum ada transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="mt-4">
                {{ $peminjaman->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Detail (Sederhana) -->
    @if($showDetailModal && $selectedPeminjaman)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
            
            <!-- Background Backdrop -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between mb-4 border-b pb-2">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Detail Transaksi #{{ $selectedPeminjaman->id }}
                        </h3>
                        <button wire:click="closeModal" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <p class="font-bold text-gray-800">Pelanggan:</p>
                            <p>{{ $selectedPeminjaman->user->name ?? '-' }}</p>
                            <p class="text-xs">{{ $selectedPeminjaman->user->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">Mobil:</p>
                            <p>{{ $selectedPeminjaman->mobil->merek ?? '-' }} - {{ $selectedPeminjaman->mobil->tipe ?? '-' }}</p>
                            <p class="text-xs">Plat: {{ $selectedPeminjaman->mobil->id ?? '-' }}</p>
                        </div>
                        
                        <div class="col-span-2">
                            <p class="font-bold text-gray-800">Sopir:</p>
                            @if($selectedPeminjaman->sopir)
                                <p>{{ $selectedPeminjaman->sopir->nama }} ({{ $selectedPeminjaman->sopir->status }})</p>
                            @else
                                <p class="italic text-gray-400">Tanpa Sopir</p>
                            @endif
                        </div>

                        <div class="col-span-2 border-t pt-2 mt-2">
                            <p class="font-bold text-gray-800 mb-1">Bukti Transfer (Jika Ada):</p>
                            @if($selectedPeminjaman->bukti_transaksi)
                                <a href="{{ asset('storage/'.$selectedPeminjaman->bukti_transaksi) }}" target="_blank">
                                    <img src="{{ asset('storage/'.$selectedPeminjaman->bukti_transaksi) }}" class="h-32 object-cover rounded border hover:opacity-75 transition">
                                </a>
                            @else
                                 <p class="text-gray-400 italic text-xs">Tidak ada bukti upload manual.</p>
                            @endif
                        </div>

                        <div class="col-span-2 bg-gray-50 p-3 rounded border mt-2">
                            <p class="font-bold text-gray-800 mb-1">Riwayat Pembayaran (Midtrans):</p>
                            @if($selectedPeminjaman->paymentTransactions->count() > 0)
                                <ul class="list-disc pl-5 space-y-1 text-xs">
                                    @foreach($selectedPeminjaman->paymentTransactions as $pay)
                                        <li>
                                            <span class="font-semibold">Rp {{ number_format($pay->amount) }}</span> 
                                            - <span class="{{ $pay->status == 'settlement' || $pay->status == 'capture' ? 'text-green-600' : 'text-yellow-600' }}">{{ $pay->status }}</span>
                                            ({{ ucfirst($pay->tipe_transaksi) }})
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-400 italic text-xs">Belum ada data transaksi.</p>
                            @endif
                        </div>

                        @if($selectedPeminjaman->fines->count() > 0)
                        <div class="col-span-2 bg-red-50 p-3 rounded border border-red-200 mt-2">
                            <p class="font-bold text-red-800 mb-1">Denda:</p>
                            <ul class="list-disc pl-5 space-y-1 text-xs text-red-700">
                                @foreach($selectedPeminjaman->fines as $fine)
                                    <li>Rp {{ number_format($fine->jumlah_denda) }} ({{ $fine->status }}) - {{ $fine->keterangan }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="closeModal" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>