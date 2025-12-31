@extends('layouts.sopir') 

@section('title', 'Tugas Aktif')

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Penugasan Aktif Anda</h1>

    {{-- Notifikasi --}}
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
            
            @if($sopir->status === 'bekerja')
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-8 rounded-md" role="alert">
                    <p class="font-bold">Status Anda: BEKERJA</p>
                    <p class="text-sm">Anda sedang dalam penugasan aktif. Mohon selesaikan tugas yang sedang berlangsung.</p>
                </div>
            @endif
            
            <div class="bg-white shadow-xl rounded-xl p-6">
                <h2 class="text-2xl font-semibold text-cyan-600 mb-6 border-b pb-3">Daftar Tugas ({{ $tugasAktif->count() ?? 0 }})</h2>

                @if (($tugasAktif ?? collect())->isEmpty())
                    <p class="text-gray-500 italic">Tidak ada penugasan yang sedang aktif atau menunggu dimulai.</p>
                @else
                    <div class="space-y-6">
                        @foreach ($tugasAktif as $tugas)
                            <div class="border border-gray-200 p-4 rounded-lg shadow-md transition duration-200 {{ $tugas->status === 'berlangsung' ? 'border-l-4 border-blue-500' : '' }}">
                                <p class="text-lg font-bold text-cyan-700">Tugas ID: #{{ $tugas->id }}</p>
                                <p class="text-gray-600">Pelanggan: <span class="font-semibold">{{ $tugas->user->name ?? 'N/A' }}</span></p>
                                <p class="text-gray-600">Mobil: <span class="font-semibold">{{ $tugas->mobil->merek ?? 'N/A' }} - {{ $tugas->mobil->plat_nomor ?? 'N/A' }}</span></p> 
                                <p class="text-gray-600">Tanggal Sewa: {{ \Carbon\Carbon::parse($tugas->tanggal_sewa)->format('d M Y') }}</p>
                                <p class="text-gray-600">Jadwal Kembali: <span class="font-bold text-red-500">{{ \Carbon\Carbon::parse($tugas->tanggal_kembali)->format('d M Y H:i') }}</span></p>
                                <p class="mt-2">Status Pembayaran: <span class="px-2 py-0.5 text-xs font-medium rounded bg-indigo-100 text-indigo-800">{{ ucfirst($tugas->status) }}</span></p>

                                @if($tugas->status === 'berlangsung')
                                    <button onclick="openModal({{ $tugas->id }})" 
                                            class="mt-3 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out">
                                        Selesaikan Tugas
                                    </button>
                                @else
                                    <p class="mt-3 text-sm text-gray-500 italic">Tugas dalam status persiapan (Menunggu Mobil Diambil).</p>
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
                <p class="text-center text-gray-600 mb-6 max-w-md">Harap ubah status ketersediaan Anda menjadi **Tersedia** di sidebar untuk melihat daftar tugas.</p>
            </div>

        @endif
        
    @endisset
</div>

{{-- Modal Selesaikan Tugas (Sama seperti di dashboard, diulang di sini karena ini adalah file view terpisah) --}}
<x-complete-task-modal />


<script>
    // Fungsionalitas modal yang diperlukan di view ini
    function openModal(taskId) {
        const modal = document.getElementById('completeTaskModal');
        const form = document.getElementById('completeTaskForm');
        const taskIdDisplay = document.getElementById('taskIdDisplay');

        taskIdDisplay.textContent = taskId;
        form.action = '{{ route("sopir.completeTask", "") }}/' + taskId; 

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('completeTaskModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('kondisi_mobil').value = '';
    }
</script>

@endsection