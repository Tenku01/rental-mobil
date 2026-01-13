<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Inspeksi Kendaraan</h2>
            
            <div class="flex gap-4 mb-4">
                <input wire:model.live="search" type="text" placeholder="Cari Plat Mobil..." class="border-gray-300 rounded w-full md:w-1/3">
                <select wire:model.live="filterCondition" class="border-gray-300 rounded">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik Sempurna">Baik Sempurna</option>
                    <option value="Perlu Perbaikan Ringan">Perlu Perbaikan Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>
            </div>

            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mobil</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff Pemeriksa</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($inspections as $ins)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $ins->mobil_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $ins->staff->name ?? 'Staff' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs rounded-full font-bold {{ $ins->condition == 'Baik Sempurna' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $ins->condition }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm">{{ $ins->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="showDetail({{ $ins->id }})" class="text-blue-600 hover:underline text-sm">Detail</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $inspections->links() }}</div>
        </div>
    </div>

    @if($showDetailModal && $selectedInspection)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-lg">Detail Inspeksi</h3>
                <button wire:click="closeModal" class="text-gray-500">&times;</button>
            </div>
            <div class="p-6 space-y-3">
                <p><strong>Mobil:</strong> {{ $selectedInspection->mobil_id }}</p>
                <p><strong>Kondisi:</strong> {{ $selectedInspection->condition }}</p>
                <div>
                    <strong class="block mb-1">Keterangan:</strong>
                    <p class="bg-gray-100 p-2 rounded border">{{ $selectedInspection->keterangan }}</p>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 flex justify-end">
                <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 rounded">Tutup</button>
            </div>
        </div>
    </div>
    @endif
</div>