@extends('layouts.resepsionis')

@section('header', 'Tambah Pelanggan Baru')

@section('content')

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <form action="{{ route('resepsionis.user.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-cyan-500 outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-cyan-500 outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-cyan-500 outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase">Nomor Telepon</label>
                        <input type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                            class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-cyan-500 outline-none">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Alamat</label>
                    <textarea name="alamat" rows="3"
                        class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-cyan-500 outline-none">{{ old('alamat') }}</textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('resepsionis.user.index') }}"
                        class="px-6 py-3 text-gray-400 font-bold text-xs uppercase hover:text-gray-600 transition-colors">Batal</a>
                    <button type="submit"
                        class="bg-cyan-700 text-white px-8 py-3 rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-cyan-800 transition-all shadow-lg">Simpan
                        Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection
