@extends('layouts.staff')

@section('content')
<div class="max-w-xl mx-auto">

<h1 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
    <svg class="h-8 w-8 text-cyan-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
    Mulai Pengecekan Pengembalian
</h1>

@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 shadow-sm">
        ⚠️ {{ session('error') }}
    </div>
@endif

@if (session('warning'))
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-xl mb-4 shadow-sm">
        🔔 {{ session('warning') }}
    </div>
@endif

<div class="bg-white shadow-xl rounded-xl p-8 border-t-4 border-cyan-500">

    <p class="text-gray-600 mb-6">
        Masukkan Kode Pengembalian untuk memulai proses inspeksi mobil dan finalisasi denda.
    </p>

    {{-- FORM MENGGUNAKAN GET --}}
    <form id="formCek" method="GET">
        <div class="flex items-end space-x-3">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700">Kode Pengembalian</label>
                <input type="text" id="kode_pengembalian" required autofocus
                       class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-lg p-3 uppercase
                              focus:border-cyan-500 focus:ring-cyan-500 transition"
                       placeholder="Contoh: PBL00123">
            </div>

            <button type="submit"
                    class="px-6 py-3 bg-cyan-600 rounded-xl font-semibold text-white hover:bg-cyan-700
                           focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition">
                Cek
            </button>
        </div>
    </form>

</div>
</div>

<script>
document.getElementById('formCek').addEventListener('submit', function(e) {
    e.preventDefault();

    let kode = document.getElementById('kode_pengembalian').value.trim().toUpperCase();
    if (!kode) return;

    // Redirect ke detail pengecekan
    window.location.href = `/staff/pengecekan/${kode}/detail`;
});
</script>

@endsection
