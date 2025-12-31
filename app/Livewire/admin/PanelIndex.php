<?php

namespace App\Livewire\admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Mobil;
use App\Models\Peminjaman;
use App\Models\PaymentTransaction;
use App\Models\UserIdentification;

class PanelIndex extends Component
{
    #[Layout('layouts.admin')]
    public function render()
    {
        // 1. KARTU STATISTIK UTAMA
        $totalPendapatan = PaymentTransaction::where('status', 'settlement')->sum('amount');
        
        $totalMobil = Mobil::count();
        $mobilTersedia = Mobil::where('status', 'tersedia')->count();
        $mobilDisewa = Mobil::where('status', 'disewa')->count();
        
        $transaksiAktif = Peminjaman::where('status', 'berlangsung')->count();
        $pendingVerifikasi = UserIdentification::where('status_approval', 'menunggu')->count();

        // 2. DATA UNTUK GRAFIK PENDAPATAN (Line Chart)
        // Mengambil data pendapatan per bulan di tahun ini
        $monthlyRevenue = PaymentTransaction::select(
            DB::raw('SUM(amount) as total'),
            DB::raw('MONTH(created_at) as month')
        )
        ->where('status', 'settlement')
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month')
        ->toArray();

        // Format array agar indeks 1-12 terisi (jika bulan kosong diisi 0)
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyRevenue[$i] ?? 0;
        }

        // 3. TRANSAKSI TERBARU (Table)
        $recentTransactions = Peminjaman::with('user', 'mobil')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard-index', [
            'totalPendapatan' => $totalPendapatan,
            'totalMobil' => $totalMobil,
            'mobilTersedia' => $mobilTersedia,
            'mobilDisewa' => $mobilDisewa,
            'transaksiAktif' => $transaksiAktif,
            'pendingVerifikasi' => $pendingVerifikasi,
            'chartData' => $chartData,
            'recentTransactions' => $recentTransactions
        ]);
    }
}