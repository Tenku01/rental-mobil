@extends('layouts.resepsionis')

@section('content')
<div class="container mx-auto p-6 mt-10">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6">Edit Pelanggan</h1>

    <form action="{{ route('resepsionis.pelanggan.update', $pelanggan->id) }}" method="POST" class="bg-white shadow-lg rounded-lg p-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- User -->
            <div class="form-group">
                <label for="user_id" class="text-sm font-medium text-gray-700">User</label>
                <div 
                    x-data="{ 
                        open: false, 
                        selectedUser: {{ $pelanggan->user_id }},
                        selectedName: '{{ $pelanggan->user->name }}' 
                    }" 
                    class="relative mt-2"
                >
                    <!-- Hidden input for submission -->
                    <input type="hidden" name="user_id" x-model="selectedUser">

                    <!-- Button trigger -->
                    <button 
                        type="button"
                        @click="open = !open" 
                        class="text-left w-full p-3 border border-gray-300 rounded-md bg-white text-gray-700 shadow-md focus:ring-2 focus:ring-cyan-500 hover:ring-cyan-500 focus:outline-none flex justify-between items-center"
                    >
                        <span x-text="selectedName || '-- Pilih User --'"></span>
                        <svg class="w-4 h-4 ml-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown list -->
                    <div 
                        x-show="open" 
                        x-transition:enter="transition ease-out duration-150" 
                        x-transition:enter-start="opacity-0 scale-95" 
                        x-transition:enter-end="opacity-100 scale-100" 
                        x-transition:leave="transition ease-in duration-75" 
                        x-transition:leave-start="opacity-100 scale-100" 
                        x-transition:leave-end="opacity-0 scale-95"
                        @click.away="open = false"
                        class="absolute left-0 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg z-10 max-h-56 overflow-y-auto"
                    >
                        <ul class="py-2 text-sm">
                            @foreach($users as $user)
                                <li>
                                    <button 
                                        type="button"
                                        @click="
                                            selectedUser = '{{ $user->id }}'; 
                                            selectedName = '{{ $user->name }}'; 
                                            open = false;
                                        " 
                                        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-cyan-100 hover:text-cyan-600 transition-colors"
                                    >
                                        {{ $user->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Nama -->
            <div class="form-group">
                <label for="nama" class="text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $pelanggan->nama) }}" 
                       class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-1 focus:ring-cyan-500 shadow-lg hover:shadow-cyan-200 transition duration-150 ease-in-out" 
                       required>
            </div>

            <!-- No Telepon -->
            <div class="form-group">
                <label for="no_telepon" class="text-sm font-medium text-gray-700">No Telepon</label>
                <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon', $pelanggan->no_telepon) }}" 
                       class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-1 focus:ring-cyan-500 shadow-lg hover:shadow-cyan-200 transition duration-150 ease-in-out">
            </div>

            <!-- Alamat -->
            <div class="form-group">
                <label for="alamat" class="text-sm font-medium text-gray-700">Alamat</label>
                <textarea name="alamat" id="alamat" 
                          class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-1 focus:ring-cyan-500 shadow-lg hover:shadow-cyan-200 transition duration-150 ease-in-out" 
                          rows="4">{{ old('alamat', $pelanggan->alamat) }}</textarea>
            </div>

        </div>

        <!-- Buttons -->
        <div class="mt-6 flex justify-end gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md text-sm hover:bg-blue-700 hover:shadow-md transition duration-150 ease-in-out">Simpan</button>
            <a href="{{ route('resepsionis.pelanggan.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded-md text-sm hover:bg-gray-500 hover:shadow-md transition duration-150 ease-in-out">Batal</a>
        </div>
    </form>
</div>
@endsection
