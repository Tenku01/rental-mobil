@extends('layouts.resepsionis')

@section('header', 'Detail Pelanggan')

@section('content')

<div class="max-w-4xl mx-auto">
<div class="mb-4">
<a href="{{ route('resepsionis.user.index') }}" class="text-cyan-600 hover:text-cyan-800 text-sm font-bold flex items-center">
<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
Kembali ke Daftar
</a>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
    <div class="bg-cyan-800 p-8 text-white flex items-center">
        <div class="h-20 w-20 rounded-full bg-cyan-700 flex items-center justify-center text-3xl font-bold border-4 border-cyan-600">
            {{ substr($user->name, 0, 1) }}
        </div>
        <div class="ml-6">
            <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
            <p class="text-cyan-200">ID Member: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
        <div class="space-y-4">
            <div>
                <h3 class="font-bold text-gray-400 uppercase tracking-widest text-[10px] mb-1">Email Terdaftar</h3>
                <p class="text-gray-800 font-medium">{{ $user->email }}</p>
            </div>
            <div>
                <h3 class="font-bold text-gray-400 uppercase tracking-widest text-[10px] mb-1">Nomor Telepon</h3>
                <p class="text-gray-800 font-medium">{{ $user->pelanggan->no_telepon ?? '-' }}</p>
            </div>
        </div>
        <div class="space-y-4">
            <div>
                <h3 class="font-bold text-gray-400 uppercase tracking-widest text-[10px] mb-1">Status Akun</h3>
                <span class="px-3 py-1 text-[10px] font-black uppercase rounded {{ $user->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $user->status }}
                </span>
            </div>
            <div>
                <h3 class="font-bold text-gray-400 uppercase tracking-widest text-[10px] mb-1">Terdaftar Sejak</h3>
                <p class="text-gray-800 font-medium">{{ $user->created_at->format('d F Y') }}</p>
            </div>
        </div>
        <div class="md:col-span-2">
            <h3 class="font-bold text-gray-400 uppercase tracking-widest text-[10px] mb-1">Alamat Lengkap</h3>
            <p class="text-gray-800 font-medium">{{ $user->pelanggan->alamat ?? 'Alamat belum diatur.' }}</p>
        </div>
    </div>

    <div class="bg-gray-50 p-6 border-t border-gray-100 flex justify-end">
        <a href="{{ route('resepsionis.user.edit', $user->id) }}" class="bg-cyan-700 text-white px-6 py-2 rounded-lg font-bold text-xs uppercase hover:bg-cyan-800 transition-all">
            Edit Profil
        </a>
    </div>
</div>


</div>
@endsection