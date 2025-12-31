@extends('layouts.staff')

@section('content')
    {{-- 
        Logic AlpineJS: 
        1. totalFine di-init dengan $totalFines (jika mode history) atau $lateFine (jika mode pengecekan baru).
        2. newDamageCost digunakan untuk menambah denda kerusakan secara real-time di UI.
    --}}
    <div x-data="{ 
        totalFine: {{ isset($totalFines) ? $totalFines : ($lateFine ?? 0) }}, 
        newDamageCost: 0 
    }" class="space-y-6">

        <h1 class="text-3xl font-bold text-gray-900">Detail Pengecekan: {{ $pengembalian->kode_pengembalian }}</h1>

        {{-- Form mengarah ke finalisasi pengecekan --}}
        <form action="{{ route('staff.pengecekan.finalisasi', $pengembalian->kode_pengembalian) }}" method="POST">
            @csrf

            {{-- 1. INFORMASI DASAR --}}
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-indigo-700 mb-4">Informasi Dasar</h2>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Penyewa</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $pengembalian->peminjaman->user->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Mobil</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $pengembalian->peminjaman->mobil->nama_mobil ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Jadwal Kembali</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_kembali . ' ' . $pengembalian->peminjaman->jam_sewa)->format('d M Y H:i') }}
                        </dd>
                    </div>
                    @if(isset($hargaPerHari))
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Harga Sewa / Hari (Basis Denda)</dt>
                        <dd class="mt-1 text-sm font-bold text-gray-700">
                            Rp {{ number_format($hargaPerHari, 0, ',', '.') }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- 2. DENDA KETERLAMBATAN (Updated Logic: Jam) --}}
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-red-700 mb-4">1. Denda Keterlambatan</h2>
                
                {{-- Menampilkan info keterlambatan --}}
                @if ($jamTerlambat > 0)
                    <div class="bg-red-50 border-l-4 border-red-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    Terlambat: <span class="font-bold text-lg">{{ $jamTerlambat }} Jam</span>
                                </p>
                                <p class="text-sm text-red-600 mt-1">
                                    Rumus: (Harga Harian x 10%) x Jam Terlambat
                                </p>
                                @if(isset($lateFine))
                                <p class="text-lg font-bold text-red-800 mt-2">
                                    Denda: Rp {{ number_format($lateFine, 0, ',', '.') }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-green-50 border-l-4 border-green-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">
                                    Pengembalian Tepat Waktu (Tidak ada denda keterlambatan).
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- 3. LAPORAN KERUSAKAN --}}
            <div x-data="{ isDamaged: false }" class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-yellow-700 mb-4">2. Laporan Kerusakan & Biaya</h2>

                {{-- Tampilkan riwayat kerusakan jika ada (mode detail history) --}}
                @if ($pengembalian->damageReports->count())
                    <div class="mb-4 p-4 bg-yellow-50 rounded border border-yellow-200">
                        <h3 class="font-bold text-yellow-800">Laporan Kerusakan Tercatat:</h3>
                        <ul class="list-disc pl-5 mt-2">
                            @foreach($pengembalian->damageReports as $report)
                                <li class="text-sm text-gray-700">
                                    {{ $report->damage_description }} - 
                                    <span class="font-bold">Rp {{ number_format($report->damage_cost, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Input kerusakan baru (hanya muncul jika status belum selesai) --}}
                @if($pengembalian->status != 'selesai pengecekan' && $pengembalian->status != 'Selesai')
                <label class="inline-flex items-center mb-4 cursor-pointer">
                    <input type="checkbox" x-model="isDamaged" class="form-checkbox h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300">
                    <span class="ml-2 text-gray-700 font-medium">Temukan Kerusakan Baru?</span>
                </label>

                <div x-show="isDamaged" x-transition class="space-y-4 border p-4 rounded-md bg-gray-50">
                    <div>
                        <label for="damage_description" class="block text-sm font-medium text-gray-700">Deskripsi Kerusakan</label>
                        <textarea name="damage_description" id="damage_description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Contoh: Bumper depan penyok, spion kiri patah..."></textarea>
                    </div>
                    <div>
                        <label for="damage_cost" class="block text-sm font-medium text-gray-700">Biaya Perbaikan (Rp)</label>
                        <input type="number" name="damage_cost" id="damage_cost" min="0" value="0"
                            x-model.number="newDamageCost"
                            @input="totalFine = {{ $lateFine ?? 0 }} + newDamageCost"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p class="text-xs text-gray-500 mt-1">*Masukkan nominal tanpa titik/koma</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- 4. HASIL INSPEKSI --}}
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-green-700 mb-4">3. Hasil Inspeksi Kendaraan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="inspection_condition" class="block text-sm font-medium text-gray-700">Kondisi Umum Mobil</label>
                        <select name="inspection_condition" id="inspection_condition" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Pilih Kondisi</option>
                            <option value="Baik Sempurna" {{ ($pengembalian->inspections->first()->condition ?? '') == 'Baik Sempurna' ? 'selected' : '' }}>Baik Sempurna</option>
                            <option value="Perlu Perbaikan Ringan" {{ ($pengembalian->inspections->first()->condition ?? '') == 'Perlu Perbaikan Ringan' ? 'selected' : '' }}>Perlu Perbaikan Ringan</option>
                            <option value="Rusak Berat" {{ ($pengembalian->inspections->first()->condition ?? '') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="inspection_notes" class="block text-sm font-medium text-gray-700">Catatan Inspeksi Staff</label>
                        <textarea name="inspection_notes" id="inspection_notes" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $pengembalian->inspections->first()->keterangan ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            {{-- 5. TOTAL BIAYA --}}
            <div class="bg-white shadow rounded-lg p-6 mb-6 sticky bottom-0 border-t-4 border-indigo-600">
                <h2 class="text-xl font-semibold text-indigo-700 mb-4">4. Total Biaya dan Pembayaran</h2>

                <div class="flex justify-between items-center bg-indigo-50 p-4 rounded-lg">
                    <div>
                        <span class="text-lg font-bold text-gray-700 block">TOTAL AKHIR DENDA</span>
                        <span class="text-xs text-gray-500">(Keterlambatan + Kerusakan)</span>
                    </div>
                    <span class="text-3xl font-extrabold text-indigo-800">
                        Rp <span x-text="Number(totalFine).toLocaleString('id-ID')"></span>
                    </span>
                </div>
            </div>

            @if($pengembalian->status != 'selesai pengecekan' && $pengembalian->status != 'Selesai')
            <div class="mt-6">
                <button type="submit" onclick="return confirm('Apakah data inspeksi dan denda sudah benar? Data tidak bisa diubah setelah disimpan.')"
                    class="w-full px-6 py-4 bg-green-600 text-white font-bold text-lg rounded-md hover:bg-green-700 transition duration-150 shadow-lg transform hover:-translate-y-0.5">
                    FINALISASI PENGEMBALIAN & SIMPAN
                </button>
            </div>
            @else
            <div class="mt-6 p-4 bg-gray-100 text-center rounded text-gray-600">
                Pengecekan telah diselesaikan.
            </div>
            @endif
        </form>
    </div>
@endsection