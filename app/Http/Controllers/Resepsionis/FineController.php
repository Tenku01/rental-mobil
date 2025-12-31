<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use Illuminate\Http\Request;

class FineController extends Controller
{
    /**
     * Menampilkan daftar denda pelanggan.
     * Hak Akses: Read
     */
    public function index()
    {
        // Mengambil denda beserta data peminjaman, mobil, dan user terkait
        $fines = Fine::with(['peminjaman.user', 'peminjaman.mobil'])
            ->latest('tanggal_terdeteksi')
            ->get();

        return view('resepsionis.fine.index', compact('fines'));
    }

    /**
     * Memperbarui status denda menjadi lunas.
     * Hak Akses: Update Status (Write)
     */
    public function updateStatus(Request $request, $id)
    {
        $fine = Fine::findOrFail($id);

        // Hanya bisa update jika statusnya belum dibayar
        if ($fine->status === 'sudah dibayar') {
            return redirect()->back()->with('error', 'Denda ini sudah lunas sebelumnya.');
        }

        $request->validate([
            'metode_pembayaran' => 'required|string|max:50',
        ]);

        $fine->update([
            'status' => 'sudah dibayar',
            'metode_pembayaran' => $request->metode_pembayaran,
            'tanggal_pembayaran' => now(),
        ]);

        return redirect()->route('resepsionis.fine.index')
            ->with('success', 'Pembayaran denda berhasil dikonfirmasi.');
    }
}