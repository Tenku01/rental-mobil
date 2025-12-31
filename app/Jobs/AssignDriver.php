<?php

namespace App\Jobs;

use App\Models\Peminjaman;
use App\Models\Sopir;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AssignDriver implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $peminjaman;

    /**
     * Create a new job instance.
     * @param Peminjaman $peminjaman
     */
    public function __construct(Peminjaman $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $peminjaman = $this->peminjaman;
        
        // Cek apakah sopir sudah ditugaskan atau tidak diperlukan
        if (!$peminjaman->add_on_sopir || $peminjaman->sopir_id) {
            return;
        }

        // 1. Cari sopir yang statusnya 'tersedia'
        $availableDriver = Sopir::where('status', 'tersedia')
            ->orderBy('updated_at', 'asc') // Pilih sopir yang paling lama tidak aktif
            ->first();

        if ($availableDriver) {
            
            // 2. Tugas Diterima: Update Peminjaman
            $peminjaman->update([
                'sopir_id' => $availableDriver->id,
            ]);

            // 3. Tugas Diterima: Update Status Sopir menjadi 'bekerja'
            $availableDriver->update(['status' => 'bekerja']);

            Log::info("Sopir ID {$availableDriver->id} berhasil ditugaskan ke Peminjaman ID {$peminjaman->id}.");
            
            // 4. Kirim Notifikasi (Implementasi notifikasi ke sopir)
            
        } else {
            // Log peringatan: Tidak ada sopir yang tersedia
            Log::warning("Tidak ada sopir yang tersedia untuk Peminjaman ID: {$peminjaman->id}.");
            
            // Jika tidak ada sopir, atur status Peminjaman ke status khusus
            // Contoh: $peminjaman->update(['status' => 'membutuhkan sopir']);
        }
    }
}