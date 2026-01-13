<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi pembayaran.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil data transaksi dengan relasi ke peminjaman (jika ada relasi di model)
        // Menggunakan pagination 10 item per halaman
        // Diurutkan berdasarkan created_at descending (terbaru diatas)
        $transactions = PaymentTransaction::with('peminjaman.user') // Eager load relasi untuk performa
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('resepsionis.payment.index', compact('transactions'));
    }

    /**
     * Menampilkan detail transaksi pembayaran tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Mencari transaksi berdasarkan ID, jika tidak ketemu akan return 404
        $transaction = PaymentTransaction::with(['peminjaman.user', 'peminjaman.mobil'])
            ->findOrFail($id);

        return view('resepsionis.payment.show', compact('transaction'));
    }
}