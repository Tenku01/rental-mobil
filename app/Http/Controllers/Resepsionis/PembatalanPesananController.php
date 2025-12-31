<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\PembatalanPesanan;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Mendefinisikan class PembatalanPesananController yang meng-extend Controller
class PembatalanPesananController extends Controller
{
    // Method untuk menampilkan daftar pembatalan pesanan
    public function index()
    {
        // Mengambil semua data pembatalan pesanan beserta relasi 'peminjaman'
        $pembatalanPesanan = PembatalanPesanan::with('peminjaman')->get();
        return view('resepsionis.pembatalan.index', compact('pembatalanPesanan'));
    }

    // Method untuk menampilkan form pembuatan pembatalan pesanan
    public function create(Request $request)
    {
        // Ambil semua peminjaman yang statusnya belum 'dibatalkan' untuk dropdown
        $peminjaman = Peminjaman::where('status', '!=', 'dibatalkan')->get();

        // Ambil ID yang dikirim via query string
        $selectedId = $request->get('peminjaman_id');

        // Ambil data detail peminjaman (beserta relasinya) jika ID dipilih
        $selectedPeminjaman = null;
        if ($selectedId) {
            $selectedPeminjaman = Peminjaman::with(['user', 'mobil'])->find($selectedId);
        }

        return view('resepsionis.pembatalan.create', compact('peminjaman', 'selectedPeminjaman', 'selectedId'));
    }

    // Method untuk menyimpan pembatalan pesanan baru dan memproses refund
    public function store(Request $request)
    {
        $validated = $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'alasan' => 'required|string',
            'persentase_refund' => 'required|numeric|min:0|max:1',
        ]);

        $peminjaman = Peminjaman::findOrFail($validated['peminjaman_id']);

        // Ambil semua transaksi Midtrans dengan status 'settlement' untuk peminjaman ini
        $payments = PaymentTransaction::where('peminjaman_id', $peminjaman->id)
            ->where('status', 'settlement')
            ->get();

        if ($payments->isEmpty()) {
            // Jika tidak ada transaksi settlement, hanya batalkan pesanan tanpa refund
            $this->cancelOrderWithoutRefund($peminjaman, $validated);
            return redirect()->route('resepsionis.pembatalan.index')
                ->with('warning', 'Pesanan berhasil dibatalkan. Tidak ada transaksi yang dapat direfund.');
        }

        $totalRefund = 0;
        $refundTransactions = [];

        // Konfigurasi Midtrans
        $serverKey = config('services.midtrans.server_key');
        $base64Auth = base64_encode($serverKey . ':');

        foreach ($payments as $payment) {
            // Hitung jumlah refund untuk transaksi ini
            $refundAmount = (int) ($validated['persentase_refund'] * $payment->amount);

            if ($refundAmount <= 0) continue;

            // Kirim request refund ke Midtrans
            $refundUrl = "https://api.sandbox.midtrans.com/v2/{$payment->midtrans_transaction_id}/refund";
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $base64Auth,
                'Content-Type' => 'application/json',
            ])->post($refundUrl, [
                'refund_key' => 'refund_' . uniqid(),
                'amount' => $refundAmount,
                'reason' => $validated['alasan'],
            ]);

            $midtransResponse = $response->json();

            // Catat transaksi refund baru
            $refundTransaction = PaymentTransaction::create([
                'peminjaman_id' => $peminjaman->id,
                'midtrans_transaction_id' => $payment->midtrans_transaction_id,
                'status' => ($midtransResponse['status_code'] ?? null) == '200' ? 'refunded' : 'failed',
                'amount' => $refundAmount,
                'tipe_transaksi' => 'refund',
                'midtrans_response' => json_encode($midtransResponse),
                'id_transaksi_awal' => $payment->id,
            ]);

            $refundTransactions[] = $refundTransaction;
            $totalRefund += $refundAmount;
        }

        // Simpan data pembatalan pesanan
        PembatalanPesanan::create([
            'peminjaman_id' => $peminjaman->id,
            'cancelled_by' => 'admin',
            'approval_status' => 'approved',
            'alasan' => $validated['alasan'],
            'refund_status' => !empty($refundTransactions) && $totalRefund > 0 ? 'refunded' : 'pending_refund',
            'cancelled_at' => now(),
            'persentase_refund' => $validated['persentase_refund'],
            'jumlah_refund' => $totalRefund,
            'id_transaksi_refund' => $refundTransactions[0]->id ?? null, // Simpan ID refund pertama sebagai referensi
        ]);

        // Update status peminjaman menjadi 'dibatalkan'
        $peminjaman->update(['status' => 'dibatalkan']);

        return redirect()->route('resepsionis.pembatalan.index')
            ->with('success', 'Pesanan berhasil dibatalkan dan refund sedang diproses. Total refund: ' . number_format($totalRefund, 0, ',', '.'));
    }

    // Helper untuk membatalkan pesanan tanpa proses refund Midtrans
    private function cancelOrderWithoutRefund($peminjaman, $validated)
    {
        // Simpan pembatalan pesanan tanpa refund
        PembatalanPesanan::create([
            'peminjaman_id' => $peminjaman->id,
            'cancelled_by' => 'admin',
            'approval_status' => 'approved',
            'alasan' => $validated['alasan'],
            'refund_status' => 'no_refund_needed',
            'cancelled_at' => now(),
            'persentase_refund' => 0,
            'jumlah_refund' => 0,
            'id_transaksi_refund' => null,
        ]);

        // Update status peminjaman
        $peminjaman->update(['status' => 'dibatalkan']);
    }

    /**
     * Tampilkan form untuk mengedit pembatalan pesanan.
     * Tidak disarankan untuk mengedit pembatalan yang sudah diproses refund.
     * Hanya izinkan edit jika status refund masih 'pending' atau untuk mengubah alasan.
     */
    public function edit($id)
    {
        // Ambil data pembatalan pesanan berdasarkan ID
        $pembatalanPesanan = PembatalanPesanan::with('peminjaman.user', 'peminjaman.mobil')->findOrFail($id);

        // Jika refund sudah diproses, berikan peringatan atau larang edit lebih lanjut
        if ($pembatalanPesanan->refund_status === 'refunded') {
            return redirect()->route('resepsionis.pembatalan.show', $id)
                             ->with('warning', 'Pembatalan pesanan ini sudah diproses refund dan tidak dapat diedit.');
        }

        // Ambil transaksi Midtrans 'settlement' terkait jika diperlukan untuk pengecekan
        $payment = PaymentTransaction::where('peminjaman_id', $pembatalanPesanan->peminjaman_id)
            ->where('tipe_transaksi', 'payment')
            ->where('status', 'settlement')
            ->first();
        
        // Hitung nilai total transaksi awal (jika ada)
        $totalPaymentAmount = $payment ? $payment->amount : 0;

        return view('resepsionis.pembatalan.edit', compact('pembatalanPesanan', 'totalPaymentAmount'));
    }

    /**
     * Perbarui data pembatalan pesanan.
     * Perubahan persentase refund akan memicu permintaan refund baru/tambahan (jika belum direfund).
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'alasan' => 'required|string',
            'persentase_refund' => 'required|numeric|min:0|max:1',
        ]);

        // Ambil data pembatalan pesanan yang akan diperbarui
        $pembatalanPesanan = PembatalanPesanan::findOrFail($id);
        $peminjaman = $pembatalanPesanan->peminjaman;
        $oldRefundPercentage = $pembatalanPesanan->persentase_refund;

        // Ambil transaksi Midtrans 'settlement' terkait
        $payments = PaymentTransaction::where('peminjaman_id', $peminjaman->id)
            ->where('tipe_transaksi', 'payment')
            ->where('status', 'settlement')
            ->get();

        $totalRefund = $pembatalanPesanan->jumlah_refund;
        $refundStatus = $pembatalanPesanan->refund_status;

        // Cek apakah ada perubahan persentase refund DAN belum ada refund yang sukses
        if ($validated['persentase_refund'] != $oldRefundPercentage && $pembatalanPesanan->refund_status !== 'refunded') {
            
            // Hitung total jumlah yang sudah direfund sebelumnya (dari semua transaksi refund terkait)
            $existingRefundAmount = PaymentTransaction::where('peminjaman_id', $peminjaman->id)
                                                    ->where('tipe_transaksi', 'refund')
                                                    ->where('status', 'refunded')
                                                    ->sum('amount');

            $newTotalRefund = 0;
            $totalAmountToRefund = 0;

            // Konfigurasi Midtrans
            $serverKey = config('services.midtrans.server_key');
            $base64Auth = base64_encode($serverKey . ':');

            // Proses refund hanya jika ada transaksi settlement
            if ($payments->isNotEmpty()) {
                
                foreach ($payments as $payment) {
                    // Hitung jumlah refund yang seharusnya berdasarkan persentase baru
                    $targetRefundAmount = (int) ($validated['persentase_refund'] * $payment->amount);
                    
                    // Hitung total refund yang sudah dilakukan untuk transaksi payment ini
                    $currentRefundedForThisPayment = PaymentTransaction::where('peminjaman_id', $peminjaman->id)
                                                                        ->where('id_transaksi_awal', $payment->id)
                                                                        ->where('tipe_transaksi', 'refund')
                                                                        ->where('status', 'refunded')
                                                                        ->sum('amount');
                                                                        
                    // Hitung jumlah tambahan yang perlu direfund (selisih antara target dan yang sudah direfund)
                    $additionalRefundAmount = $targetRefundAmount - $currentRefundedForThisPayment;
                    
                    if ($additionalRefundAmount > 0) {
                        // Kirim request refund ke Midtrans
                        $refundUrl = "https://api.sandbox.midtrans.com/v2/{$payment->midtrans_transaction_id}/refund";
                        $response = Http::withHeaders([
                            'Authorization' => 'Basic ' . $base64Auth,
                            'Content-Type' => 'application/json',
                        ])->post($refundUrl, [
                            'refund_key' => 'refund_' . uniqid(),
                            'amount' => $additionalRefundAmount,
                            'reason' => $validated['alasan'] . ' (Perubahan persentase refund)',
                        ]);

                        $midtransResponse = $response->json();

                        // Catat transaksi refund baru
                        PaymentTransaction::create([
                            'peminjaman_id' => $peminjaman->id,
                            'midtrans_transaction_id' => $payment->midtrans_transaction_id,
                            'status' => ($midtransResponse['status_code'] ?? null) == '200' ? 'refunded' : 'failed',
                            'amount' => $additionalRefundAmount,
                            'tipe_transaksi' => 'refund',
                            'midtrans_response' => json_encode($midtransResponse),
                            'id_transaksi_awal' => $payment->id,
                        ]);
                        
                        $newTotalRefund += $additionalRefundAmount;
                    }

                    // Total refund baru (yang sudah ada + yang baru diproses)
                    $totalAmountToRefund += $targetRefundAmount; 
                }
                
                $totalRefund = $totalAmountToRefund; // Total keseluruhan refund yang terjadi
                $refundStatus = ($newTotalRefund > 0 || $existingRefundAmount > 0) ? 'refunded' : 'pending_refund';

            } else {
                 $refundStatus = 'no_refund_needed';
            }
        }

        // Perbarui data pembatalan pesanan
        $pembatalanPesanan->update([
            'alasan' => $validated['alasan'],
            'persentase_refund' => $validated['persentase_refund'],
            'jumlah_refund' => $totalRefund,
            'refund_status' => $refundStatus,
        ]);


        return redirect()->route('resepsionis.pembatalan.index')
            ->with('success', 'Data pembatalan pesanan berhasil diperbarui. Refund tambahan telah diproses (jika ada).');
    }

    // Method untuk menampilkan detail pembatalan pesanan
    public function show($id)
    {
        $pembatalanPesanan = PembatalanPesanan::with('peminjaman')->findOrFail($id);
        return view('resepsionis.pembatalan.show', compact('pembatalanPesanan'));
    }
}