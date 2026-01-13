<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Laporan Kerusakan (Damage Report)</h2>
            
            <div class="mb-4">
                <input wire:model.live="search" type="text" placeholder="Cari Kode Laporan / Plat Mobil..." class="border-gray-300 rounded w-full md:w-1/3">
            </div>

            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mobil</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Biaya Kerusakan</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($reports as $report)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">{{ $report->kode_laporan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $report->mobil_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 font-bold">Rp {{ number_format($report->damage_cost, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="showDetail('{{ $report->kode_laporan }}')" class="text-blue-600 hover:underline text-sm">Detail</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $reports->links() }}</div>
        </div>
    </div>

    @if($showDetailModal && $selectedReport)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-lg">Detail Kerusakan</h3>
                <button wire:click="closeModal" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-xs text-gray-500 uppercase">Kode Laporan</label>
                    <p class="font-bold">{{ $selectedReport->kode_laporan }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Deskripsi Kerusakan</label>
                    <p class="text-gray-700 bg-gray-50 p-3 rounded border">{{ $selectedReport->damage_description }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Estimasi Biaya</label>
                    <p class="text-xl font-bold text-red-600">Rp {{ number_format($selectedReport->damage_cost, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 flex justify-end">
                <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>
    @endif
</div>