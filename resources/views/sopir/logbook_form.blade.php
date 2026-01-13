@extends('layouts.sopir')

@section('title', 'Logbook Aktivitas - ' . ($peminjaman->mobil->merek ?? 'Tugas'))

@section('content')

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
{{-- Header --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
<div>
<a href="{{ route('sopir.logbook.index') }}"
class="inline-flex items-center text-sm font-semibold text-cyan-600 hover:text-cyan-800 mb-3 transition duration-200">
<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
</svg>
Kembali ke Daftar Tugas
</a>
<h1 class="text-3xl font-bold text-gray-800">Logbook Perjalanan</h1>
<p class="text-gray-600 mt-1">Catat aktivitas perjalanan Anda secara rill</p>
</div>

    {{-- Info Box --}}
    <div class="bg-gradient-to-r from-cyan-50 to-blue-50 px-6 py-4 rounded-xl shadow-md border border-cyan-200">
        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Mobil Tugas</p>
        <p class="text-xl font-bold text-cyan-700 mt-1">
            {{ $peminjaman->mobil->merek ?? 'N/A' }} - {{ $peminjaman->mobil->plat_nomor ?? 'N/A' }}
        </p>
        <div class="flex items-center mt-2 text-sm text-gray-600">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ \Carbon\Carbon::parse($peminjaman->tanggal_mulai)->format('d M Y') }} - 
            {{ \Carbon\Carbon::parse($peminjaman->tanggal_selesai)->format('d M Y') }}
        </div>
    </div>
</div>

{{-- Notifikasi --}}
@if (session('success'))
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    </div>
@endif

{{-- Alert Logbook Hari Ini --}}
@if(isset($logbook_hari_ini) && $logbook_hari_ini)
    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-lg shadow-sm">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="font-medium">Update terakhir hari ini pada jam 
                <span class="font-bold">{{ $logbook_hari_ini->waktu_log->format('H:i') }}</span> 
                ({{ $logbook_hari_ini->waktu_log->diffForHumans() }})
            </p>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Form Input --}}
    <div class="lg:col-span-1">
        <div class="bg-white shadow-2xl rounded-2xl p-6 sticky top-8 border border-gray-200">
            <h2 class="text-xl font-bold text-cyan-700 mb-6 pb-3 border-b border-gray-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Update Aktivitas
            </h2>
            
            <form id="logbookForm" action="{{ route('sopir.logbook.store', $peminjaman->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                
                {{-- Status Log --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Status Aktivitas
                    </label>
                    <select name="status_log" id="status_log" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 text-sm transition duration-200">
                        <option value="" disabled selected>Pilih status...</option>
                        <option value="mulai_kerja">🚀 Mulai Kerja Hari Ini</option>
                        <option value="dalam_perjalanan">📍 Dalam Perjalanan</option>
                        <option value="selesai_hari_ini">🏠 Selesai Kerja Hari Ini</option>
                        <option value="selesai_peminjaman">🏁 Tugas Selesai (Kembali ke Pool)</option>
                    </select>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        Deskripsi Aktivitas
                    </label>
                    <textarea name="deskripsi_aktivitas" rows="4" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 text-sm transition duration-200"
                            placeholder="Tuliskan aktivitas Anda..."
                            minlength="10" maxlength="500"></textarea>
                </div>

                {{-- Upload Foto --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Foto Bukti (Opsional)
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-cyan-400 hover:bg-cyan-50 transition duration-200">
                        <div class="space-y-2 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label class="relative cursor-pointer bg-white rounded-md font-medium text-cyan-600 hover:text-cyan-700 focus-within:outline-none">
                                    <span class="px-4 py-2 bg-cyan-100 rounded-lg hover:bg-cyan-200 transition">Pilih file</span>
                                    <input type="file" name="foto_bukti" class="sr-only" accept="image/*">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 
                               text-white font-bold py-3 px-4 rounded-xl shadow-lg transition duration-200 
                               transform hover:-translate-y-0.5 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Aktivitas
                </button>
            </form>
        </div>
    </div>

    {{-- Riwayat Logbook --}}
    <div class="lg:col-span-2">
        <div class="bg-white shadow-2xl rounded-2xl p-6 border border-gray-200">
            <div class="flex justify-between items-center mb-6 pb-3 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-cyan-700 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Riwayat Aktivitas
                </h2>
                <span class="bg-cyan-100 text-cyan-800 text-xs font-semibold px-3 py-1 rounded-full">
                    {{ $logbooks->total() }} Total Entri
                </span>
            </div>

            @if($logbooks->isEmpty())
                <div class="text-center py-16">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum ada catatan</h3>
                </div>
            @else
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($logbooks as $log)
                        <li>
                            <div class="relative pb-8">
                                @if (!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex items-start space-x-4">
                                    <div class="relative">
                                        <span class="h-10 w-10 rounded-full flex items-center justify-center ring-8 ring-white 
                                            {{ match($log->status_log) {
                                                'selesai_peminjaman' => 'bg-green-500',
                                                'mulai_kerja' => 'bg-blue-500',
                                                'selesai_hari_ini' => 'bg-purple-500',
                                                default => 'bg-cyan-500'
                                            } }}">
                                            @switch($log->status_log)
                                                @case('selesai_peminjaman') 🏁 @break
                                                @case('mulai_kerja') 🚀 @break
                                                @case('selesai_hari_ini') 🏠 @break
                                                @default 📍 @break
                                            @endswitch
                                        </span>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0 bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition duration-200">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-2">
                                                    <h3 class="text-sm font-bold text-gray-900">
                                                        {{ ucfirst(str_replace('_', ' ', $log->status_log)) }}
                                                    </h3>
                                                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
                                                        {{ $log->waktu_log->diffForHumans() }}
                                                    </span>
                                                </div>
                                                
                                                <p class="text-sm text-gray-700 mb-3">{{ $log->deskripsi_aktivitas }}</p>
                                                
                                                @if($log->foto_bukti)
                                                    <div class="mt-2">
                                                        <a href="{{ Storage::url($log->foto_bukti) }}" target="_blank" class="text-xs font-bold text-cyan-600 flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            Lihat Foto
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="text-right ml-4">
                                                <div class="text-sm font-bold text-gray-900">
                                                    {{ $log->waktu_log->format('H:i') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $log->waktu_log->format('d/m/y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                
                @if($logbooks->hasPages())
                    <div class="mt-6">
                        {{ $logbooks->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>


</div>
@endsection

@push('scripts')

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
const form = document.getElementById('logbookForm');

    form.addEventListener(&#39;submit&#39;, function(e) {
        const statusSelect = document.getElementById(&#39;status_log&#39;);
        const status = statusSelect.value;
        
        if (status === &#39;selesai_peminjaman&#39;) {
            e.preventDefault();
            
            Swal.fire({
                title: &#39;Selesaikan Tugas?&#39;,
                text: &quot;Apakah Anda yakin ingin mengakhiri peminjaman ini secara permanen? Status Anda akan kembali menjadi &#39;tersedia&#39;.&quot;,
                icon: &#39;warning&#39;,
                showCancelButton: true,
                confirmButtonColor: &#39;#0891b2&#39;, // cyan-600
                cancelButtonColor: &#39;#ef4444&#39;, // red-500
                confirmButtonText: &#39;Ya, Selesaikan!&#39;,
                cancelButtonText: &#39;Batal&#39;,
                reverseButtons: true
            }).then((result) =&gt; {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    });
});


</script>

@endpush