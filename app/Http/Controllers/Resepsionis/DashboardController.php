<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pelanggan;
use App\Models\PembatalanPesanan;
use App\Models\Mobil;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('resepsionis.dashboard', [
            'title' => 'Dashboard Resepsionis',
            
            // Statistik Utama
            'totalMobil' => Mobil::count(),
            'totalPelanggan' => Pelanggan::count(),
            'totalPeminjaman' => Peminjaman::count(),
            
            // Status Peminjaman
            'peminjamanBerlangsung' => Peminjaman::where('status', 'berlangsung')->count(),
            'peminjamanSelesai' => Peminjaman::where('status', 'selesai')->count(),
            'peminjamanBaru' => Peminjaman::where('status', 'menunggu pembayaran')->count(),
            
            // Pembatalan & Verifikasi (Optional jika ada modelnya)
            'totalPembatalan' => PembatalanPesanan::count(),
            'pendingPembatalan' => PembatalanPesanan::where('approval_status', 'pending')->count(),

            // Data Tabel Terbaru (Limit 5)
            'recentPeminjaman' => Peminjaman::with(['user', 'mobil'])
                ->latest()
                ->limit(5)
                ->get()
        ]);
    }
}