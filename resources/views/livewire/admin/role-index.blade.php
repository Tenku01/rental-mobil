<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Manajemen Role Akses</h2>

            @if (session()->has('message'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 rounded shadow-sm">
                    <p class="text-sm text-green-700">{{ session('message') }}</p>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded shadow-sm">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($roles as $role)
                <div class="border rounded-xl p-5 shadow-sm hover:shadow-md transition bg-gray-50 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-bold text-lg text-gray-800 capitalize">{{ $role->role_name }}</h3>
                            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">ID: {{ $role->id }}</span>
                        </div>
                        <p class="text-sm text-gray-500">
                            Digunakan oleh {{ $role->users()->count() }} pengguna.
                        </p>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        @if($role->id === 1)
                            <button disabled class="w-full bg-gray-200 text-gray-400 font-bold py-2 rounded-lg cursor-not-allowed text-sm">
                                System Protected
                            </button>
                        @else
                            <button wire:click="edit({{ $role->id }})" class="w-full bg-white border border-gray-300 text-gray-700 font-bold py-2 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition text-sm">
                                Edit Nama Role
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal Edit Nama Role -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Edit Nama Role</h3>
                </div>
                <form wire:submit.prevent="update">
                    <div class="bg-white px-6 py-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role Baru</label>
                        <input wire:model="role_name" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('role_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-2 italic">*Mengubah nama role tidak mengubah hak akses sistem, hanya label tampilan.</p>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-md">Simpan Perubahan</button>
                        <button type="button" wire:click="$set('showModal', false)" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>