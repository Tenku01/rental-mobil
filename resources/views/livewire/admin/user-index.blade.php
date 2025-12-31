<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Header dengan tombol tambah -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h2>
                    <button wire:click="create" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah User
                    </button>
                </div>

                <!-- Search & Filter -->
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <input wire:model.live.debounce.300ms="search" 
                           type="text" 
                           placeholder="Cari Nama / Email..." 
                           class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 w-full md:w-1/3">
                    
                    <select wire:model.live="filterRole" 
                            class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->role_name) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Flash Messages -->
                @if (session()->has('success'))
                    <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session()->has('message'))
                    <div class="bg-blue-100 text-blue-700 px-4 py-3 rounded mb-4">
                        {{ session('message') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-6 text-left">No</th>
                                <th class="py-3 px-6 text-left">Nama</th>
                                <th class="py-3 px-6 text-left">Email</th>
                                <th class="py-3 px-6 text-center">Role</th>
                                <th class="py-3 px-6 text-center">Status</th>
                                <th class="py-3 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm">
                            @forelse($users as $user)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-6">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td class="py-3 px-6">{{ $user->name }}</td>
                                <td class="py-3 px-6">{{ $user->email }}</td>
                                <td class="py-3 px-6 text-center">
                                    <span class="bg-gray-200 px-2 py-1 rounded text-xs">
                                        {{ $user->role->role_name ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <span class="px-2 py-1 rounded text-xs font-bold {{ $user->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex justify-center gap-2">
                                        <!-- Edit Button -->
                                        <button wire:click="edit({{ $user->id }})"
                                                class="px-3 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600">
                                            Edit
                                        </button>
                                        
                                        <!-- Toggle Status Button -->
                                        @if($user->id !== auth()->id())
                                            <button wire:click="toggleStatus({{ $user->id }})" 
                                                    class="text-xs font-bold px-3 py-1 rounded text-white {{ $user->status == 'aktif' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
                                                {{ $user->status == 'aktif' ? 'Non-Aktifkan' : 'Aktifkan' }}
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <button onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')"
                                                    class="px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
                                                Hapus
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400">Akun Sendiri</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Data tidak ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal CRUD -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $modalTitle }}</h3>
                </div>
                
                <!-- Modal Body -->
                <div class="px-6 py-4">
                    <form wire:submit.prevent="save">
                        <!-- Nama -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input type="text" wire:model="name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" wire:model="email"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Password -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Password {{ $editingUserId ? '(Kosongkan jika tidak ingin mengubah)' : '' }}
                            </label>
                            <input type="password" wire:model="password"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Konfirmasi Password -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                            <input type="password" wire:model="password_confirmation"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <!-- Role -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select wire:model="role_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Status -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" wire:model="status" value="aktif" class="text-blue-600">
                                    <span class="ml-2">Aktif</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" wire:model="status" value="nonaktif" class="text-blue-600">
                                    <span class="ml-2">Non-Aktif</span>
                                </label>
                            </div>
                            @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Modal Footer -->
                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <button type="button" wire:click="closeModal"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                {{ $editingUserId ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- JavaScript untuk konfirmasi delete -->
<script>
    function confirmDelete(userId, userName) {
        if (confirm(`Yakin ingin menghapus user "${userName}"?`)) {
            @this.delete(userId);
        }
    }
</script>