<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Log Aktivitas Sopir</h2>
            
            <div class="mb-4">
                <input wire:model.live="search" type="text" placeholder="Cari Nama Sopir..." class="border-gray-300 rounded w-full md:w-1/3">
            </div>

            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sopir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Log</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $log->peminjaman->sopir->nama ?? 'Unknown' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->waktu_log }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 font-bold uppercase">
                                    {{ str_replace('_', ' ', $log->status_log) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="showDetail({{ $log->id }})" class="text-blue-600 hover:underline text-sm">Lihat</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $logs->links() }}</div>
        </div>
    </div>

    @if($showDetailModal && $selectedLog)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-lg">Detail Aktivitas</h3>
                <button wire:click="closeModal" class="text-gray-500">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <p><strong>Aktivitas:</strong> {{ $selectedLog->deskripsi_aktivitas }}</p>
                @if($selectedLog->foto_bukti)
                    <div>
                        <strong>Foto Bukti:</strong>
                        <img src="{{ asset('storage/'.$selectedLog->foto_bukti) }}" class="mt-2 rounded-lg shadow w-full h-48 object-cover">
                    </div>
                @endif
            </div>
            <div class="px-6 py-3 bg-gray-50 flex justify-end">
                <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 rounded">Tutup</button>
            </div>
        </div>
    </div>
    @endif
</div>