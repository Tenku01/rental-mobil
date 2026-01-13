<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            <!-- Header & Action -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Sopir</h2>
                <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Sopir
                </button>
            </div>

            <!-- Filter & Search -->
            <div class="flex gap-4 mb-6">
                <input wire:model.live="search" type="text" placeholder="Cari Nama / SIM..." class="border-gray-300 rounded-lg shadow-sm w-full md:w-1/3 focus:border-blue-500 focus:ring-blue-500">
                <select wire:model.live="filterStatus" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="bekerja">Sedang Bekerja</option>
                    <option value="tidak tersedia">Tidak Tersedia / Izin</option>
                </select>
            </div>

            <!-- Flash Message -->
            @if (session()->has('message'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded shadow-sm">
                    <p class="text-sm text-green-700">{{ session('message') }}</p>
                </div>
            @endif

            <!-- Grid Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($sopirs as $sopir)
                <div class="bg-white border rounded-xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between relative group">
                    
                    <!-- Detail -->
                    <div>
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                                {{ substr($sopir->nama, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">{{ $sopir->nama }}</h3>
                                <p class="text-xs text-gray-500">{{ $sopir->user->email ?? 'No Account' }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between border-b pb-2">
                                <span>No. SIM:</span>
                                <span class="font-mono font-semibold text-gray-800">{{ $sopir->no_sim ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between pt-1">
                                <span>Status:</span>
                                <span class="px-2 py-1 text-xs rounded-full font-bold 
                                    {{ $sopir->status == 'tersedia' ? 'bg-green-100 text-green-800' : 
                                      ($sopir->status == 'bekerja' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                    {{ strtoupper($sopir->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="mt-5 pt-4 border-t flex gap-2">
                        <button wire:click="edit({{ $sopir->id }})" class="flex-1 bg-white border border-gray-300 text-gray-700 font-semibold py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                            Edit Detail
                        </button>
                        <button wire:confirm="Yakin ingin menghapus sopir ini?" wire:click="delete({{ $sopir->id }})" class="px-3 bg-white border border-red-200 text-red-600 font-semibold py-2 rounded-lg hover:bg-red-50 transition text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-10">
                    <div class="inline-block p-4 rounded-full bg-gray-100 mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada data sopir.</p>
                </div>
                @endforelse
            </div>
            
            <div class="mt-6">{{ $sopirs->links() }}</div>
        </div>
    </div>

    <!-- Modal Form (Full CRUD) -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">{{ $isEditMode ? 'Edit Data Sopir' : 'Tambah Sopir Baru' }}</h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                    <div class="bg-white px-6 py-6 space-y-4">
                        
                        <!-- Nama -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input wire:model="nama" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email (Login)</label>
                            <input wire:model="email" type="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password {{ $isEditMode ? '(Isi jika ingin mengubah)' : '' }}</label>
                            <input wire:model="password" type="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- No SIM -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor SIM</label>
                                <input wire:model="no_sim" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono">
                                @error('no_sim') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Ketersediaan</label>
                                <select wire:model="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="tersedia">Tersedia</option>
                                    <option value="bekerja">Sedang Bekerja</option>
                                    <option value="tidak tersedia">Tidak Tersedia / Izin</option>
                                </select>
                                @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-md transition">
                            Simpan Data
                        </button>
                        <button type="button" wire:click="$set('showModal', false)" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>