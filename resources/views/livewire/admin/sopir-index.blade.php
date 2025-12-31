<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Manajemen Sopir</h2>

            <div class="flex gap-4 mb-4">
                <input wire:model.live="search" type="text" placeholder="Cari Nama Sopir..." class="border-gray-300 rounded w-full md:w-1/3">
                <select wire:model.live="filterStatus" class="border-gray-300 rounded">
                    <option value="">Semua Status</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="bekerja">Sedang Bekerja</option>
                    <option value="tidak tersedia">Tidak Tersedia / Izin</option>
                </select>
            </div>

            @if (session()->has('message'))
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('message') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($sopirs as $sopir)
                <div class="border rounded-lg p-4 shadow-sm bg-gray-50 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                {{ substr($sopir->nama, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold">{{ $sopir->nama }}</h3>
                                <p class="text-xs text-gray-500">SIM: {{ $sopir->no_sim ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="px-2 py-1 text-xs rounded font-bold 
                                {{ $sopir->status == 'tersedia' ? 'bg-green-200 text-green-800' : 
                                  ($sopir->status == 'bekerja' ? 'bg-blue-200 text-blue-800' : 'bg-gray-300 text-gray-600') }}">
                                {{ strtoupper($sopir->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t">
                        <button wire:click="editStatus({{ $sopir->id }})" class="w-full bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-1 px-2 rounded text-sm">
                            Ubah Status
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center text-gray-500">Belum ada data sopir.</div>
                @endforelse
            </div>
            
            <div class="mt-4">{{ $sopirs->links() }}</div>
        </div>
    </div>

    <!-- Modal Ubah Status -->
    @if($showEditModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="bg-white p-5 rounded-lg shadow-xl w-96">
            <h3 class="text-lg font-bold mb-4">Update Status Sopir</h3>
            
            <select wire:model="statusSopir" class="w-full border p-2 rounded mb-4">
                <option value="tersedia">Tersedia (Ready)</option>
                <option value="bekerja">Sedang Bekerja</option>
                <option value="tidak tersedia">Tidak Tersedia (Sakit/Izin)</option>
            </select>
            @error('statusSopir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

            <div class="flex justify-end gap-2">
                <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                <button wire:click="updateStatus" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>