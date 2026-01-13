<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Data Pembatalan Pesanan</h2>
            
            <div class="flex gap-4 mb-4">
                <input wire:model.live="search" type="text" placeholder="Cari User..." class="border-gray-300 rounded w-full md:w-1/3">
                <select wire:model.live="filterStatus" class="border-gray-300 rounded">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibatalkan Oleh</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Approval</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Refund</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pembatalan as $data)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $data->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $data->cancelled_by }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs rounded font-bold 
                                    {{ $data->approval_status == 'approved' ? 'bg-green-100 text-green-800' : 
                                      ($data->approval_status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($data->approval_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm">{{ ucfirst(str_replace('_', ' ', $data->refund_status)) }}</td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="showDetail({{ $data->id }})" class="text-blue-600 hover:underline text-sm">Detail</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $pembatalan->links() }}</div>
        </div>
    </div>

    @if($showDetailModal && $selectedBatal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-lg">Detail Pembatalan</h3>
                <button wire:click="closeModal" class="text-gray-500">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <p><strong>Mobil:</strong> {{ $selectedBatal->peminjaman->mobil->merek ?? '-' }}</p>
                <div>
                    <strong>Alasan Pembatalan:</strong>
                    <p class="mt-1 bg-gray-100 p-3 rounded text-gray-700 italic">{{ $selectedBatal->alasan }}</p>
                </div>
                <div class="border-t pt-2">
                    <p><strong>Jumlah Refund:</strong> Rp {{ number_format($selectedBatal->jumlah_refund, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 flex justify-end">
                <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 rounded">Tutup</button>
            </div>
        </div>
    </div>
    @endif
</div>