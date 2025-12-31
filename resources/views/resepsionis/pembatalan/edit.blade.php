@extends('layouts.resepsionis')

@section('title', 'Edit Pembatalan Pesanan')

@section('content')

    <div class="container mx-auto p-4 md:p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Pembatalan Pesanan</h1>

        <!-- Tampilkan pesan peringatan jika ada -->

        @if (session('warning'))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded shadow" role="alert">
                <p class="font-bold">Perhatian!</p>
                <p>{{ session('warning') }}</p>
            </div>
        @endif

        <!-- Tampilkan pesan error jika ada -->

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow" role="alert">
                <p class="font-bold">Error!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Kolom Kiri: Detail Peminjaman (Read-only) -->

            <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-lg h-fit">
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-3 mb-4">Detail Peminjaman</h2>
                <div class="space-y-3 text-gray-600">
                    <p><strong>ID Peminjaman:</strong> <span
                            class="font-mono text-indigo-600">{{ $pembatalanPesanan->peminjaman->id }}</span></p>
                    <p><strong>Status Persetujuan:</strong>
                        @php
                            $statusClass =
                                [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ][$pembatalanPesanan->approval_status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $statusClass }}">
                            {{ ucfirst($pembatalanPesanan->approval_status) }}
                        </span>
                    </p>
                    <p><strong>Pelanggan:</strong> {{ $pembatalanPesanan->peminjaman->user->name ?? 'N/A' }}</p>
                    <p><strong>Mobil:</strong> {{ $pembatalanPesanan->peminjaman->mobil->merek ?? 'N/A' }}
                        ({{ $pembatalanPesanan->peminjaman->mobil->model ?? 'N/A' }})</p>
                    <p><strong>Tanggal Mulai:</strong>
                        {{ \Carbon\Carbon::parse($pembatalanPesanan->peminjaman->tanggal_sewa)->format('d F Y') }}</p>
                    <p><strong>Tanggal Selesai:</strong>
                        {{ \Carbon\Carbon::parse($pembatalanPesanan->peminjaman->tanggal_kembali)->format('d F Y') }}</p>
                    <p class="text-lg font-bold text-green-700 mt-4">
                        Total Dibayarkan: Rp{{ number_format($totalPaymentAmount, 0, ',', '.') }}
                    </p>
                    <p class="text-md font-semibold text-red-500">
                        Jumlah Refund Saat Ini: Rp{{ number_format($pembatalanPesanan->jumlah_refund, 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-500 italic">Dibatalkan oleh:
                        {{ ucfirst($pembatalanPesanan->cancelled_by) }}</p>
                </div>
            </div>

            <!-- Kolom Kanan: Form Edit Pembatalan & Aksi -->

            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-3 mb-4">Form Pembaharuan Persentase Refund</h2>

                <!-- Form for Updating Refund Percentage (Uses PUT method) -->
                <form id="refund-form" action="{{ route('resepsionis.pembatalan.update', $pembatalanPesanan->id) }}"
                    method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Field Alasan Pembatalan -->
                    <div class="mb-4">
                        <label for="alasan" class="block text-sm font-medium text-gray-700 mb-1">Alasan Pembatalan
                            (Revisi)</label>
                        <textarea name="alasan" id="alasan" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('alasan') border-red-500 @enderror"
                            required>{{ old('alasan', $pembatalanPesanan->alasan) }}</textarea>
                        @error('alasan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Field Persentase Refund -->
                    <div class="mb-6">
                        <label for="persentase_refund" class="block text-sm font-medium text-gray-700 mb-1">Persentase
                            Refund (0.00 hingga 1.00)</label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" max="1" name="persentase_refund"
                                id="persentase_refund"
                                value="{{ old('persentase_refund', $pembatalanPesanan->persentase_refund) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('persentase_refund') border-red-500 @enderror"
                                required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm" id="estimated-refund-display">
                                    (Max: Rp{{ number_format($totalPaymentAmount, 0, ',', '.') }} x
                                    {{ $pembatalanPesanan->persentase_refund * 100 }}%)
                                </span>
                            </div>
                        </div>
                        @error('persentase_refund')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol Simpan Perubahan Persentase (Hanya untuk revisi setelah approved) -->
                    @if ($pembatalanPesanan->approval_status !== 'pending')
                        <div class="flex justify-end space-x-3 mb-8 gap-4">
                            <a href="{{ route('resepsionis.pembatalan.index') }}"
                                class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-150">Batal</a>
                            <button type="submit"
                                class="px-4 py-2 text-black bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 transform hover:scale-[1.02]">
                                Simpan Perubahan Persentase
                            </button>
                        </div>
                    @endif
                </form>

                <!-- Section Aksi Persetujuan (Hanya tampil jika statusnya PENDING) -->
                @if ($pembatalanPesanan->approval_status === 'pending')
                    <h2 class="text-xl font-semibold text-gray-700 border-t pt-4 mt-4 mb-4">Aksi Persetujuan Pembatalan</h2>

                    <p class="text-sm text-gray-600 mb-4">Setelah menentukan Persentase Refund di atas, Anda dapat
                        menyetujui atau menolak permintaan pembatalan ini.</p>

                    <div class="flex justify-between space-x-4">

                        <!-- Tombol Tolak Refund (Reject) -->
                        <form action="{{ route('resepsionis.pembatalan.reject', $pembatalanPesanan->id) }}" method="POST"
                            onsubmit="return confirm('Anda yakin ingin MENOLAK permintaan pembatalan ini? Status akan diubah menjadi Rejected.');">
                            @csrf
                            <button type="submit"
                                class="w-full px-6 py-3 text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700 transition duration-150 transform hover:scale-[1.02] font-semibold">
                                <i class="fas fa-times mr-2"></i> Tolak Pembatalan
                            </button>
                        </form>

                        <!-- Tombol Setujui Refund (Approve) -->
                        <button type="button" onclick="submitApproval();"
                            class="w-full px-6 py-3 text-white bg-green-600 rounded-lg shadow-md hover:bg-green-700 transition duration-150 transform hover:scale-[1.02] font-semibold">
                            <i class="fas fa-check mr-2"></i> Setujui & Proses Refund
                        </button>
                    </div>

                    <!-- Hidden Form untuk Submit Aksi Approve (Menggunakan POST murni) -->
                    <form id="approve-form" action="{{ route('resepsionis.pembatalan.approve', $pembatalanPesanan->id) }}"
                        method="POST" style="display:none;">
                        @csrf
                        <!-- Hidden inputs untuk mentransfer data dari form utama -->
                        <input type="hidden" name="alasan" id="approve_alasan">
                        <input type="hidden" name="persentase_refund" id="approve_persentase_refund">
                    </form>

                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('resepsionis.pembatalan.index') }}"
                            class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-150">Kembali
                            ke Daftar</a>
                    </div>
                @else
                    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-3 rounded mt-6">
                        <p><strong>Status Sudah Diproses:</strong> Permintaan pembatalan ini sudah berstatus
                            <strong>{{ ucfirst($pembatalanPesanan->approval_status) }}</strong>.</p>
                        <p class="text-sm">Anda hanya dapat merevisi persentase refund jika status sudah 'approved'.</p>
                    </div>
                @endif


            </div>

        </div>

    </div>
    <script>
        // Fungsi yang dijalankan saat tombol "Setujui" diklik
        function submitApproval() {
            const alasan = document.getElementById('alasan').value;
            const persentase = document.getElementById('persentase_refund').value;
            const approvalForm = document.getElementById('approve-form');

            // 1. Validasi dasar sebelum transfer data
            if (!alasan || !persentase) {
                alert('Mohon isi Alasan Pembatalan dan Persentase Refund terlebih dahulu.');
                return;
            }

            // 2. Transfer data dari input visible ke hidden input POST form
            document.getElementById('approve_alasan').value = alasan;
            document.getElementById('approve_persentase_refund').value = persentase;

            // 3. Submit form POST
            approvalForm.submit();


        }

        // Logika untuk menampilkan estimasi jumlah refund
        document.addEventListener('DOMContentLoaded', function() {
            const persentaseInput = document.getElementById('persentase_refund');
            const totalAmount = parseFloat({{ $totalPaymentAmount ?? 0 }});
            const estimatedRefundDisplay = document.getElementById('estimated-refund-display');

            // Fungsi untuk mengupdate tampilan estimasi
            function updateEstimatedRefund() {
                let percentage = parseFloat(persentaseInput.value);

                if (isNaN(percentage) || percentage < 0 || percentage > 1) {
                    return;
                }

                let estimatedRefund = Math.round(totalAmount * percentage);

                // Memformat rupiah untuk ditampilkan
                const formattedTotal = (totalAmount).toLocaleString('id-ID', {
                    maximumFractionDigits: 0
                });
                const formattedPercentage = (percentage * 100).toFixed(0);

                if (estimatedRefundDisplay) {
                    estimatedRefundDisplay.innerHTML = `(Max: Rp${formattedTotal} x ${formattedPercentage}%)`;
                }
            }

            // Panggil saat load dan saat input berubah
            updateEstimatedRefund();
            persentaseInput.addEventListener('input', updateEstimatedRefund);


        });
    </script>
@endsection
