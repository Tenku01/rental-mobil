<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Data Pengembalian Armada</h2>

            <!-- Filter & Search -->
            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <input wire:model.live="search" type="text" placeholder="Cari Kode / User / Plat Mobil..." class="border-gray-300 rounded-lg w-full md:w-1/3 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                
                <select wire:model.live="filterStatus" class="border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                    <option value="">Semua Status</option>
                    <option value="menunggu pengecekan">Menunggu Pengecekan</option>
                    <option value="selesai pengecekan">Selesai Pengecekan</option>
                    <option value="selesai">Selesai (Closed)</option>
                </select>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto rounded-lg shadow ring-1 ring-black ring-opacity-5">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode & Tgl</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penyewa & Mobil</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas Cek</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pengembalian as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-mono text-sm font-bold text-cyan-700">{{ $item->kode_pengembalian }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->peminjaman->user->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $item->peminjaman->mobil->merek ?? '-' }} 
                                    <span class="bg-gray-100 px-1 rounded border">{{ $item->peminjaman->mobil->id ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-600">
                                {{ $item->staff->nama ?? 'Belum ada' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @php
                                    $statusClass = match($item->status) {
                                        'selesai' => 'bg-green-100 text-green-800',
                                        'selesai pengecekan' => 'bg-blue-100 text-blue-800',
                                        'menunggu pengecekan' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full font-bold {{ $statusClass }}">
                                    {{ ucwords($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <button wire:click="showDetail('{{ $item->kode_pengembalian }}')" class="text-cyan-600 hover:text-cyan-900 font-semibold text-sm hover:underline">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada data pengembalian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $pengembalian->links() }}</div>
        </div>
    </div>

    <!-- Modal Detail -->
    @if($showDetailModal && $selectedPengembalian)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Detail Pengembalian #{{ $selectedPengembalian->kode_pengembalian }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="bg-white px-6 py-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">Tanggal Kembali</p>
                            <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($selectedPengembalian->tanggal_pengembalian)->format('d M Y, H:i') }} WIB</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">Status</p>
                            <span class="px-2 py-0.5 text-xs rounded bg-gray-200 font-bold">{{ ucwords($selectedPengembalian->status) }}</span>
                        </div>
                        
                        <div class="col-span-2 border-t pt-2">
                            <p class="text-gray-500 text-xs mb-1">Informasi Sewa</p>
                            <p class="font-bold text-gray-900">{{ $selectedPengembalian->peminjaman->user->name ?? '-' }}</p>
                            <p class="text-gray-600">{{ $selectedPengembalian->peminjaman->mobil->merek ?? '-' }} ({{ $selectedPengembalian->peminjaman->mobil->id ?? '-' }})</p>
                        </div>

                        <div class="col-span-2 border-t pt-2">
                            <p class="text-gray-500 text-xs mb-1">Petugas Pemeriksa (Staff)</p>
                            <p class="font-semibold">{{ $selectedPengembalian->staff->nama ?? 'Belum Ditugaskan' }}</p>
                        </div>

                        <div class="col-span-2 border-t pt-2 bg-gray-50 p-2 rounded">
                            <p class="text-gray-500 text-xs">Catatan Pengembalian</p>
                            <p class="text-gray-800 italic">{{ $selectedPengembalian->catatan ?? 'Tidak ada catatan khusus.' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse">
                    <button wire:click="closeModal" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-100">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>