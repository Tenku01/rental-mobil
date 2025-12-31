<?php

namespace App\Livewire\admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Models\Mobil;
use App\Models\Peminjaman;
use App\Models\PaymentTransaction;
use App\Models\UserIdentification;
use Barryvdh\DomPDF\Facade\Pdf; 

class HomeIndex extends Component
{
    //Properti untuk Filter Export
    public $showExportModal = false;
    public $dateStart;
    public $dateEnd;
    public $filterStatusExport = ''; 

    #[Layout('layouts.admin')] 
    public function render()
    {
        // 1. DATA KARTU STATISTIK
        $totalPendapatan = PaymentTransaction::where('status', 'settlement')->sum('amount');
        
        $totalMobil = Mobil::count();
        $mobilTersedia = Mobil::where('status', 'tersedia')->count();
        $mobilDisewa = Mobil::where('status', 'disewa')->count();
        
        $transaksiAktif = Peminjaman::where('status', 'berlangsung')->count();
        $pendingVerifikasi = UserIdentification::where('status_approval', 'menunggu')->count();

        // 2. GRAFIK PENDAPATAN BULANAN
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

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyRevenue[$i] ?? 0;
        }

        // 3. TRANSAKSI TERBARU
        $recentTransactions = Peminjaman::with('user', 'mobil')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('livewire.admin.home-index', [
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

    //LOGIKA EXPORT LAPORAN PDF

    public function openExportModal()
    {
        // Reset filter saat modal dibuka
        $this->reset(['dateStart', 'dateEnd', 'filterStatusExport']);
        
        // Set default tanggal (Awal bulan ini s/d Hari ini)
        $this->dateStart = date('Y-m-01'); 
        $this->dateEnd = date('Y-m-d');
        
        $this->showExportModal = true;
    }

    public function downloadReport()
    {
        // Validasi Input Tanggal
        $this->validate([
            'dateStart' => 'required|date',
            'dateEnd' => 'required|date|after_or_equal:dateStart',
        ]);

        // Ambil Data Sesuai Filter
        $data = Peminjaman::with(['user', 'mobil'])
            ->whereBetween('tanggal_sewa', [$this->dateStart, $this->dateEnd])
            ->when($this->filterStatusExport, function($q) {
                $q->where('status', $this->filterStatusExport);
            })
            ->orderBy('tanggal_sewa', 'asc')
            ->get();

        // Generate PDF menggunakan View 'reports.transaksi_pdf'
        $pdf = Pdf::loadView('reports.transaksi_pdf', [
            'data' => $data,
            'startDate' => $this->dateStart,
            'endDate' => $this->dateEnd,
            'totalOmzet' => $data->sum('total_harga')
        ]);

        // Set Ukuran Kertas Landscape (Melebar)
        $pdf->setPaper('a4', 'landscape');

        // Tutup Modal
        $this->showExportModal = false;

        // Download File
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan-Rental-' . date('d-m-Y-His') . '.pdf');
    }
}