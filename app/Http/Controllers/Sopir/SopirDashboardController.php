<?php

namespace App\Http\Controllers\Sopir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\Sopir;
use Carbon\Carbon;

class SopirDashboardController extends Controller
{
    /**
     * Helper function untuk memuat data Sopir, Tugas Aktif, dan Riwayat.
     */
    protected function loadSopirData()
    {
        $sopir = Auth::user()->sopir;

        if (!$sopir) {
            return null;
        }

        // 1. Ambil Penugasan Aktif
        $tugasAktif = Peminjaman::where('sopir_id', $sopir->id)
            ->whereIn('status', ['sudah dibayar lunas', 'berlangsung'])
            ->with(['user', 'mobil'])
            ->orderBy('tanggal_sewa', 'asc')
            ->get();

        // 2. Ambil Riwayat Tugas (selesai atau dibatalkan)
        $riwayat = Peminjaman::where('sopir_id', $sopir->id)
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->with(['mobil'])
            ->orderBy('updated_at', 'desc')
            ->limit(20) // Batas 20 untuk halaman riwayat
            ->get();
            
        // 3. Logika Otomatisasi Status Sopir (Failsafe)
        $isCurrentlyWorking = $tugasAktif->contains(function ($tugas) {
            return $tugas->status === 'berlangsung';
        });

        if ($isCurrentlyWorking && $sopir->status !== 'bekerja') {
            $sopir->update(['status' => 'bekerja']);
            $sopir->refresh(); 
        } elseif (!$isCurrentlyWorking && $sopir->status === 'bekerja') {
             $hasPendingTasks = $tugasAktif->contains(function ($tugas) {
                return $tugas->status === 'sudah dibayar lunas';
            });

            if (!$hasPendingTasks) {
                $sopir->update(['status' => 'tersedia']);
                $sopir->refresh();
            }
        }
        
        return [
            'sopir' => $sopir,
            'tugasAktif' => $tugasAktif,
            'riwayat' => $riwayat,
        ];
    }
    
    /**
     * Tampilkan dashboard sopir.
     */
    public function dashboard()
    {
        $data = $this->loadSopirData();
        
        if (!$data) {
             return redirect('/home')->withErrors('Anda tidak terdaftar sebagai Sopir.');
        }

        // Dashboard hanya menampilkan data ringkas dari loadSopirData()
        return view('sopir.SopirDashboard', array_merge($data, ['title' => 'Dashboard Sopir']));
    }
    
    /**
     * Tampilkan halaman Tugas Aktif secara terpisah.
     */
    public function activeTasks()
    {
        $data = $this->loadSopirData();
        
        if (!$data) {
             return redirect('/home')->withErrors('Anda tidak terdaftar sebagai Sopir.');
        }
        
        // Hanya kirim tugas aktif ke view ini
        return view('sopir.ActiveTasks', array_merge($data, ['title' => 'Tugas Aktif']));
    }

    /**
     * Tampilkan halaman Riwayat Kerja secara terpisah.
     */
    public function history()
    {
        $data = $this->loadSopirData();
        
        if (!$data) {
             return redirect('/home')->withErrors('Anda tidak terdaftar sebagai Sopir.');
        }

        // Hanya kirim riwayat ke view ini
        return view('sopir.History', array_merge($data, ['title' => 'Riwayat Kerja']));
    }
}