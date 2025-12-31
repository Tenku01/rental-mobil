<?php

namespace App\Providers;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman; // Import Model yang dibutuhkan

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // PENTING: View Composer untuk Dashboard Sopir
        View::composer('sopir.SopirDashboard', function ($view) {
            
            // Pastikan user sudah login
            if (Auth::check() && Auth::user()->role_id === 4) { 
                $sopir = Auth::user()->sopir; 

                if ($sopir) {
                    $tugasAktif = Peminjaman::where('sopir_id', $sopir->id)
                        ->whereIn('status', ['berlangsung', 'sudah dibayar lunas', 'pembayaran dp'])
                        // Asumsi: relasi 'user' dan 'mobil' sudah ada di model Peminjaman
                        ->with(['user', 'mobil']) 
                        ->orderBy('tanggal_sewa', 'asc')
                        ->get();
                    
                    $riwayat = Peminjaman::where('sopir_id', $sopir->id)
                        ->whereIn('status', ['selesai', 'dibatalkan'])
                        ->with(['user', 'mobil'])
                        ->orderBy('tanggal_kembali', 'desc')
                        ->limit(10)
                        ->get();
                        
                    $view->with([
                        'sopir' => $sopir,
                        'tugasAktif' => $tugasAktif,
                        'riwayat' => $riwayat,
                    ]);
                } else {
                     // Jika user login tapi data sopir tidak ditemukan di tabel sopirs
                     $view->with(['sopir' => null, 'tugasAktif' => collect(), 'riwayat' => collect()]);
                }
            } else {
                // Jika tidak login atau bukan sopir, kirim data kosong
                $view->with(['sopir' => null, 'tugasAktif' => collect(), 'riwayat' => collect()]);
            }
        });
    
        User::observe(UserObserver::class);
        
    }
}
