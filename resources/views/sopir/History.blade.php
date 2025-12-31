@extends('layouts.sopir') 

@section('title', 'Riwayat Kerja')

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Riwayat Kerja Anda</h1>

    @isset($sopir)

        @if ($sopir->status === 'tersedia' || $sopir->status === 'bekerja')
            
            <div class="bg-white shadow-xl rounded-xl p-6">
                <h2 class="text-2xl font-semibold text-cyan-600 mb-6 border-b pb-3">Riwayat Selesai & Dibatalkan ({{ $riwayat->count() ?? 0 }})</h2>

                @if (($riwayat ?? collect())->isEmpty())
                    <p class="text-gray-500 italic">Belum ada riwayat tugas yang tercatat.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($riwayat as $item)
                            <div class="p-4 bg-gray-50 rounded-lg border-l-4 shadow-sm 
                                         {{ $item->status === 'selesai' ? 'border-green-500' : 'border-red-500' }}">
                                <p class="font-bold text-lg mb-1">ID #{{ $item->id }} - {{ ucfirst($item->status) }}</p>
                                <p class="text-gray-700 text-sm">Mobil: <span class="font-semibold">{{ $item->mobil->merek ?? 'N/A' }}</span></p>
                                <p class="text-gray-700 text-sm">Selesai/Batal: {{ \Carbon\Carbon::parse($item->updated_at)->format('d M Y H:i') }}</p>
                                
                                {{-- Detail Tambahan --}}
                                @if($item->kondisi_mobil)
                                    <p class="text-xs text-gray-500 mt-2 italic line-clamp-2" title="{{ $item->kondisi_mobil }}">
                                        Kondisi: {{ $item->kondisi_mobil }}
                                    </p>
                                @endif
                                @if($item->denda)
                                    <p class="text-xs font-semibold text-red-600 mt-1">Denda: Rp{{ number_format($item->denda, 0, ',', '.') }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        @elseif ($sopir->status === 'tidak tersedia')

            <div class="flex flex-col items-center justify-center p-12 bg-gray-50 rounded-xl shadow-xl border-t-4 border-red-500 min-h-[50vh]">
                <svg class="w-16 h-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Status Anda Tidak Aktif</h2>
                <p class="text-center text-gray-600 mb-6 max-w-md">Harap ubah status ketersediaan Anda menjadi **Tersedia** di sidebar untuk melihat riwayat kerja.</p>
            </div>

        @endif
        
    @endisset
</div>
@endsection