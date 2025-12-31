<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Fine;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PengembalianController extends Controller
{
    /**
     * 🔹 User klik tombol "Selesaikan Peminjaman"
     * Updated: Hanya membuat record pengembalian, perhitungan denda dilakukan oleh Staff.
     */
     public function store(Request $request, $peminjaman_id)
    {
        // Ambil data peminjaman beserta relasi mobilnya
        $peminjaman = Peminjaman::with('mobil')->findOrFail($peminjaman_id);

        // Cek apakah sudah ada pengembalian
        $existingPengembalian = Pengembalian::where('peminjaman_id', $peminjaman->id)->first();
        if ($existingPengembalian) {
            return redirect()->back()->with('warning', 'Permintaan pengembalian sudah dibuat sebelumnya.');
        }

        /**
         * GENERATE KODE: RET-(plat mobil)
         * Kita ambil plat nomor dari kolom mobil_id di tabel peminjaman (sesuai data Anda sebelumnya)
         * Kita gunakan Str::upper dan str_replace untuk merapikan formatnya (menghapus spasi)
         */
            $platNomor = str_replace(' ', '', $peminjaman->mobil_id); // 'AB 1111 Y' -> 'AB1111Y'
            $kodePengembalian = 'RET-' . strtoupper($platNomor);

        // 1. Buat record Pengembalian
        Pengembalian::create([
            'kode_pengembalian' => $kodePengembalian,
            'peminjaman_id' => $peminjaman->id,
            'tanggal_pengembalian' => Carbon::now(),
            'status' => 'menunggu pengecekan',
        ]);

        // 2. Update status peminjaman
        $peminjaman->update(['status' => 'selesai']);
        
        if ($peminjaman->mobil) {
            $peminjaman->mobil->update(['status' => 'dibersihkan']);
        }

        return redirect()->back()->with('success', 'Mobil berhasil dikembalikan. Mohon tunggu pengecekan oleh staff untuk kalkulasi denda (jika ada).');
    }

    /**
     * 🔹 Staff melakukan pengecekan kendaraan
     * NOTE: Logic pengecekan utama sekarang ada di StaffDashboardController.
     * Fungsi ini mungkin legacy/tidak digunakan jika user tidak punya akses staff.
     */
    public function pengecekan(Request $request, $id)
    {
        $request->validate([
            'status_pemeriksaan' => 'required|in:baik,rusak',
            'denda_kerusakan' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $pengembalian = Pengembalian::findOrFail($id);

        $dendaKerusakan = $request->status_pemeriksaan === 'rusak'
            ? ($request->denda_kerusakan ?? 0)
            : 0;

        // Note: Field total_denda dkk mungkin error jika kolomnya tidak ada di tabel pengembalian
        // karena sekarang pindah ke tabel fines. Pastikan menyesuaikan jika fungsi ini masih dipakai.
        $totalDenda = ($pengembalian->denda_keterlambatan ?? 0) + $dendaKerusakan;
        $hasilPengecekan = $totalDenda > 0 ? 'ada_denda' : 'tidak_ada_denda';

        $pengembalian->update([
            'denda_kerusakan' => $dendaKerusakan,
            'total_denda' => $totalDenda,
            'hasil_pengecekan' => $hasilPengecekan,
            'status_pemeriksaan' => $request->status_pemeriksaan,
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Pengecekan kendaraan telah diperbarui.');
    }

    /**
     * 🔹 User membayar denda (tunai / transfer)
     */
    public function bayarDenda(Request $request, $id)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:transfer,tunai',
        ]);

        $pengembalian = Pengembalian::findOrFail($id);

        $pengembalian->update([
            'status_pembayaran_denda' => 'dibayar',
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        return redirect()->back()->with('success', 'Pembayaran denda berhasil dicatat.');
    }

    public function generateSnapToken($kode_pengembalian)
    {
        $pengembalian = Pengembalian::with('peminjaman.user')->where('kode_pengembalian', $kode_pengembalian)->firstOrFail();
        
        // Pastikan Model Pengembalian punya accessor/relasi ke tabel Fines untuk mengambil total denda
        $totalDenda = $pengembalian->total_outstanding_fine ?? 0; 

        if ($totalDenda <= 0) {
            return response()->json(['error' => 'Tidak ada denda yang perlu dibayar.'], 400);
        }

        if (Auth::id() !== $pengembalian->peminjaman->user_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        // Buat order ID & record transaksi
        $orderId = 'DND-' . $pengembalian->kode_pengembalian . '-' . time();

        PaymentTransaction::create([
            'peminjaman_id' => $pengembalian->peminjaman_id,
            'midtrans_transaction_id' => $orderId,
            'status' => 'pending',
            'amount' => $totalDenda,
            'tipe_transaksi' => 'denda',
        ]);

        // SNAP token dummy (simulasi)
        $snapToken = 'simulasi-' . $orderId . '-token';

        $pengembalian->update([
            'status' => 'menunggu_pembayaran_midtrans',
            'metode_pembayaran' => 'midtrans',
        ]);

        return response()->json(['snap_token' => $snapToken]);
    }

     public function selectManualPaymentMethod(Request $request, $kode_pengembalian)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:transfer,tunai',
        ]);

        $pengembalian = Pengembalian::where('kode_pengembalian', $kode_pengembalian)->firstOrFail();

        if (Auth::id() !== $pengembalian->peminjaman->user_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $totalDenda = $pengembalian->total_outstanding_fine ?? 0;

        if ($totalDenda <= 0 || 
            $pengembalian->status_pembayaran_denda === 'dibayar') 
        {
            return redirect()->back()->with('error', 'Tidak ada denda yang perlu dibayar atau sudah lunas.');
        }

        $newStatus = $request->metode_pembayaran === 'tunai'
            ? 'menunggu_pembayaran_tunai'
            : 'menunggu_verifikasi_transfer';

        $pengembalian->update([
            'status' => $newStatus,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        return redirect()->back()->with('success', 'Pilihan pembayaran dicatat. Menunggu konfirmasi dari Staff.');
    }
}