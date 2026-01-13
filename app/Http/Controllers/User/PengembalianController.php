<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PengembalianController extends Controller
{
    /**
     * 🔹 User klik tombol "Selesaikan Peminjaman"
     * Membuat record pengembalian dengan kode unik.
     */
    public function store(Request $request, $peminjaman_id)
    {
        $peminjaman = Peminjaman::with('mobil')->findOrFail($peminjaman_id);

        // Cegah duplikasi pengembalian untuk satu transaksi peminjaman yang sama
        $existingPengembalian = Pengembalian::where('peminjaman_id', $peminjaman->id)->first();
        if ($existingPengembalian) {
            return redirect()->back()->with('warning', 'Permintaan pengembalian sudah dibuat sebelumnya.');
        }

        /**
         * 🔹 Generate kode pengembalian unik
         * Format: RET-PLATNOMOR-IDPEMINJAMAN
         * Contoh: RET-AB1111Y-104
         * Penambahan ID peminjaman menjamin kode unik meskipun mobilnya sama.
         */
        $platNomor = str_replace(' ', '', $peminjaman->mobil_id);
        $kodePengembalian = 'RET-' . strtoupper($platNomor) . '-' . $peminjaman->id;

        // Simpan pengembalian
        Pengembalian::create([
            'kode_pengembalian'   => $kodePengembalian,
            'peminjaman_id'       => $peminjaman->id,
            'tanggal_pengembalian'=> Carbon::now(),
            'status'              => 'menunggu pengecekan',
        ]);

        // Update status peminjaman menjadi selesai agar tidak muncul di daftar aktif user
        $peminjaman->update(['status' => 'selesai']);

        // Update status mobil agar segera dibersihkan oleh tim operasional
        if ($peminjaman->mobil) {
            $peminjaman->mobil->update(['status' => 'dibersihkan']);
        }

        return redirect()->back()->with(
            'success',
            'Mobil berhasil dikembalikan. Mohon tunggu pengecekan fisik oleh staff kami.'
        );
    }

    /**
     * 🔹 Legacy - pengecekan dipindahkan ke modul Staff
     */
    public function pengecekan(Request $request, $id)
    {
        return redirect()->back()->with(
            'warning',
            'Pengecekan kendaraan dikelola oleh Staff dan tidak diproses di modul ini.'
        );
    }

    /**
     * 🔹 Legacy - pembayaran denda tidak ditangani di tabel pengembalian
     */
    public function bayarDenda(Request $request, $id)
    {
        return redirect()->back()->with(
            'warning',
            'Pembayaran denda dikelola melalui modul transaksi.'
        );
    }

    /**
     * 🔹 Generate Snap Token Midtrans (SIMULASI)
     */
    public function generateSnapToken($kode_pengembalian)
    {
        $pengembalian = Pengembalian::with('peminjaman.user')
            ->where('kode_pengembalian', $kode_pengembalian)
            ->firstOrFail();

        // Total denda diambil dari relasi denda (fines)
        $totalDenda = $pengembalian->total_outstanding_fine ?? 0;

        if ($totalDenda <= 0) {
            return response()->json(['error' => 'Tidak ada denda yang perlu dibayar.'], 400);
        }

        if (Auth::id() !== $pengembalian->peminjaman->user_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $orderId = 'DND-' . $pengembalian->kode_pengembalian . '-' . time();

        // Buat record transaksi pembayaran denda
        PaymentTransaction::create([
            'peminjaman_id' => $pengembalian->peminjaman_id,
            'midtrans_transaction_id' => $orderId,
            'status' => 'pending',
            'amount' => $totalDenda,
            'tipe_transaksi' => 'denda',
        ]);

        // Update status pengembalian sementara menunggu pembayaran
        $pengembalian->update([
            'status' => 'menunggu_pembayaran_midtrans',
        ]);

        // Token simulasi untuk pengujian frontend
        $snapToken = 'simulasi-' . $orderId . '-token';

        return response()->json(['snap_token' => $snapToken]);
    }

    /**
     * 🔹 User memilih metode pembayaran manual (Transfer/Tunai)
     */
    public function selectManualPaymentMethod(Request $request, $kode_pengembalian)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:transfer,tunai',
        ]);

        $pengembalian = Pengembalian::with('peminjaman')
            ->where('kode_pengembalian', $kode_pengembalian)
            ->firstOrFail();

        if (Auth::id() !== $pengembalian->peminjaman->user_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $totalDenda = $pengembalian->total_outstanding_fine ?? 0;

        if ($totalDenda <= 0) {
            return redirect()->back()->with('error', 'Tidak ada denda yang perlu dibayar.');
        }

        $newStatus = $request->metode_pembayaran === 'tunai'
            ? 'menunggu_pembayaran_tunai'
            : 'menunggu_verifikasi_transfer';

        // Update status pengembalian sesuai pilihan metode
        $pengembalian->update([
            'status' => $newStatus,
        ]);

        return redirect()->back()->with(
            'success',
            'Pilihan pembayaran berhasil dicatat. Mohon segera selesaikan pembayaran dan tunggu konfirmasi staff.'
        );
    }
}