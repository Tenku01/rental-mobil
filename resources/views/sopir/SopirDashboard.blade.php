@extends('layouts.sopir') 

@section('title', 'Dashboard Sopir')

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Sopir</h1>

    {{-- Notifikasi (Dibiarkan di luar pengecekan status agar selalu tampil) --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert">
            <p><strong>Terjadi Kesalahan:</strong></p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @isset($sopir)

        @if ($sopir->status === 'tersedia' || $sopir->status === 'bekerja')
            
            {{-- KONDISI 1: TAMPILAN AKTIF (TERSEDIA ATAU BEKERJA) --}}

            {{-- Notifikasi Khusus Status Bekerja --}}
            @if($sopir->status === 'bekerja')
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-8 rounded-md" role="alert">
                    <p class="font-bold">Anda Sedang Bertugas!</p>
                    <p class="text-sm">Status Anda saat ini 'Bekerja'. Lihat <a href="{{ route('sopir.activeTasks') }}" class="font-semibold underline hover:text-blue-800">Tugas Aktif</a> untuk menyelesaikannya.</p>
                </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Card Tugas Aktif (Ringkasan) --}}
                <div class="bg-white shadow-xl rounded-xl p-6 border-t-4 border-cyan-500 hover:shadow-2xl transition duration-300">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Total Penugasan Aktif</h2>
                    <p class="text-5xl font-extrabold text-cyan-600">{{ $tugasAktif->count() ?? 0 }}</p>
                    <a href="{{ route('sopir.activeTasks') }}" class="mt-4 inline-block text-sm font-medium text-cyan-600 hover:text-cyan-800 transition">
                        Lihat Detail Tugas &rarr;
                    </a>
                </div>

                {{-- Card Riwayat (Ringkasan) --}}
                <div class="bg-white shadow-xl rounded-xl p-6 border-t-4 border-gray-500 hover:shadow-2xl transition duration-300">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Total Riwayat Selesai</h2>
                    <p class="text-5xl font-extrabold text-gray-600">{{ $riwayat->where('status', 'selesai')->count() ?? 0 }}</p>
                    <a href="{{ route('sopir.history') }}" class="mt-4 inline-block text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                        Lihat Riwayat Lengkap &rarr;
                    </a>
                </div>

                {{-- Card Status (Ulangi Info Status) --}}
                <div class="bg-white shadow-xl rounded-xl p-6 border-t-4 {{ $sopir->status === 'tersedia' ? 'border-green-500' : 'border-blue-500' }} hover:shadow-2xl transition duration-300">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Status Ketersediaan</h2>
                    <p class="text-5xl font-extrabold 
                        {{ $sopir->status === 'tersedia' ? 'text-green-600' : 'text-blue-600' }}">
                        {{ ucfirst($sopir->status) }}
                    </p>
                    <p class="mt-4 text-sm text-gray-500">
                        Status dikelola melalui tombol *toggle* di sidebar.
                    </p>
                </div>
            </div>

        @elseif ($sopir->status === 'tidak tersedia')

            {{-- KONDISI 2: TAMPILAN TIDAK TERSEDIA --}}
            <div class="flex flex-col items-center justify-center p-12 bg-gray-50 rounded-xl shadow-xl border-t-4 border-red-500 min-h-[50vh]">
                <svg class="w-16 h-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Status Anda Saat Ini Tidak Tersedia</h2>
                <p class="text-center text-gray-600 mb-6 max-w-md">Untuk melihat penugasan aktif dan riwayat kerja, Anda harus mengubah status ketersediaan Anda menjadi **Tersedia** terlebih dahulu.</p>
                <p class="text-sm text-gray-500 italic">Silakan gunakan tombol *toggle* di sidebar kiri Anda untuk mengaktifkan status.</p>
            </div>

        @endif
        
    @else
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded-md" role="alert">
            <p class="font-bold">Memuat Data...</p>
            <p>Data dashboard belum dimuat. Pastikan pengguna yang login memiliki data sopir yang lengkap dan data telah dimuat ke dalam view.</p>
        </div>
    @endisset
</div>

{{-- Menggunakan Blade Component --}}
<x-complete-task-modal />

{{-- Script for Modal (tetap diperlukan di view ini) --}}
<script>
    /**
     * Mengontrol Modal Selesaikan Tugas
     * @param {number} taskId ID Peminjaman
     */
    function openModal(taskId) {
        const modal = document.getElementById('completeTaskModal');
        const form = document.getElementById('completeTaskForm');
        const taskIdDisplay = document.getElementById('taskIdDisplay');

        taskIdDisplay.textContent = taskId;
        // Mengarahkan aksi ke rute yang sudah didefinisikan: sopir.completeTask
        form.action = '{{ route("sopir.completeTask", "") }}/' + taskId; 

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('completeTaskModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('kondisi_mobil').value = ''; // Kosongkan input setelah modal ditutup
    }
</script>

@endsection