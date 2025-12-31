<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pelanggan;
use App\Models\PembatalanPesanan;
use App\Models\Mobil;

class DashboardController extends Controller
{
    public function index()
    {
        return view('resepsionis.dashboard', [
            'title' => 'Dashboard Resepsionis',
             'totalMobil' => Mobil::count(),
            'totalPelanggan' => Pelanggan::count(),
            'totalPeminjaman' => Peminjaman::count(),
            'peminjamanBerlangsung' => Peminjaman::where('status', 'berlangsung')->count(),
             'peminjamanSelesai' => Peminjaman::where('status', 'selesai')->count(),
            'totalPembatalan' => PembatalanPesanan::count(),
       
        ]);
    }
}
