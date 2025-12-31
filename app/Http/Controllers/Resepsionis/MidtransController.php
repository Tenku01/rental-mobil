<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\PembatalanPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Transaction;

class MidtransController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans Sandbox
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false; // Sandbox mode
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Melakukan refund ke Midtrans berdasarkan pembatalan pesanan
     */
    public function refund($id)
    {
        $pembatalan = PembatalanPesanan::with('peminjaman')->findOrFail($id);
        $peminjaman = $pembatalan->peminjaman;

        // Pastikan hanya refund satu kali
        if ($pembatalan->refund_status !== 'pending_refund') {
            return back()->with('error', 'Refund tidak dapat diproses. Status refund saat ini: ' . $pembatalan->refund_status);
        }

        try {
            // Hitung jumlah refund: persentase * total_dibayarkan
            $refundAmount = ($pembatalan->persentase / 100) * $peminjaman->total_dibayarkan;

            // Kirim request refund ke Midtrans
            $response = Transaction::refund($peminjaman->id, [
                'refund_key' => 'refund_' . uniqid(),
                'amount' => $refundAmount,
                'reason' => $pembatalan->alasan ?? 'Pembatalan pesanan oleh admin',
            ]);

            // Update status refund di database
            $pembatalan->update([
                'refund_status' => 'refunded',
            ]);

            Log::info('Refund berhasil dikirim ke Midtrans', ['response' => $response]);

            return back()->with('success', 'Refund sebesar Rp ' . number_format($refundAmount, 0, ',', '.') . ' berhasil diproses.');
        } catch (\Exception $e) {
            Log::error('Refund gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses refund: ' . $e->getMessage());
        }
    }

    /**
     * Callback dari Midtrans (notifikasi transaksi/refund)
     */
    public function callback(Request $request)
    {
        $payload = $request->getContent();
        $notif = json_decode($payload);

        if (!$notif || !isset($notif->order_id)) {
            return response()->json(['message' => 'Invalid callback data'], 400);
        }

        $orderId = $notif->order_id;
        $transactionStatus = $notif->transaction_status ?? null;
        $fraudStatus = $notif->fraud_status ?? null;

        // Log callback untuk debugging
        Log::info('Midtrans Callback', [
            'order_id' => $orderId,
            'status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
        ]);

        $peminjaman = Peminjaman::where('id', $orderId)->first();
        if (!$peminjaman) {
            return response()->json(['message' => 'Peminjaman tidak ditemukan'], 404);
        }

        // Update status pembayaran berdasarkan callback
        if ($transactionStatus === 'refund') {
            $pembatalan = PembatalanPesanan::where('peminjaman_id', $peminjaman->id)->first();
            if ($pembatalan) {
                $pembatalan->update([
                    'refund_status' => 'refunded',
                    'approval_status' => 'approved',
                ]);
            }
        }

        return response()->json(['message' => 'Callback diterima'], 200);
    }
}
