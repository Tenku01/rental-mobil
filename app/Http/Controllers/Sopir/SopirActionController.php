<?php

namespace App\Http\Controllers\Sopir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Sopir; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SopirActionController extends Controller
{
    /**
     * Perbarui status ketersediaan sopir.
     * Menggunakan nilai 'tersedia' dan 'tidak tersedia' sesuai skema DB terbaru.
     */
    public function updateStatus(Request $request)
    {
        $sopir = Auth::user()->sopir;

        if (!$sopir) {
            return back()->withErrors('Akses ditolak. Profil sopir tidak ditemukan.');
        }

        // Contoh pengecekan role_id (jika role_id 4 = Sopir)
        $user = Auth::user();
        if ($user->role_id !== 4) {
             return back()->withErrors('Akses ditolak. Anda tidak memiliki izin sopir.');
        }

        $validated = $request->validate([
            // Nilai status disesuaikan dengan enum DB: 'tidak tersedia', 'tersedia', 'bekerja'
            'status' => ['required', Rule::in(['tidak tersedia', 'tersedia'])],
        ]);

        if ($sopir->status === 'bekerja') {
            return back()->withErrors('Status tidak dapat diubah saat Anda sedang dalam penugasan aktif (Bekerja).');
        }

        $sopir->update($validated);
        
        // Ubah pesan untuk mencerminkan status yang lebih deskriptif
        $statusText = $validated['status'] === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia';

        return back()->with('success', 'Status ketersediaan berhasil diperbarui menjadi ' . $statusText . '.');
    }
    
    /**
     * Logika untuk menyelesaikan tugas dan mencatat kondisi mobil.
     */
    public function completeTask(Request $request, Peminjaman $peminjaman)
    {
        $sopir = Auth::user()->sopir;

        // 1. Verifikasi Otorisasi Sopir
        if (!$sopir || $peminjaman->sopir_id !== $sopir->id) {
            return back()->withErrors('Anda tidak berhak menyelesaikan tugas ini.');
        }

        // 2. Verifikasi Status Peminjaman (hanya bisa diselesaikan jika 'berlangsung')
        if ($peminjaman->status !== 'berlangsung') {
             return back()->withErrors('Tugas ID ' . $peminjaman->id . ' tidak dapat diselesaikan karena statusnya bukan "Berlangsung".');
        }

        $validated = $request->validate([
            'kondisi_mobil' => 'required|string|max:500',
        ]);

        // 3. Update status peminjaman menjadi 'selesai' dan catat kondisi mobil
        $peminjaman->update([
            'status' => 'selesai',
            'kondisi_mobil' => $validated['kondisi_mobil'],
            // Di sini dapat ditambahkan logika perhitungan denda jika ada
        ]);
        
        // 4. Periksa apakah ada tugas aktif lain untuk sopir ini
        $hasActiveTasks = Peminjaman::where('sopir_id', $sopir->id)
            ->whereIn('status', ['berlangsung']) // Hanya mencari tugas yang benar-benar 'berlangsung'
            ->exists();
        
        // 5. Ubah status sopir menjadi 'tersedia' jika tidak ada tugas aktif lain
        if (!$hasActiveTasks) {
            // Mengubah status menjadi 'tersedia' sesuai enum DB
            $sopir->update(['status' => 'tersedia']); 
        }

        return redirect()->route('sopir.dashboard')->with('success', 'Tugas ID ' . $peminjaman->id . ' berhasil diselesaikan. Status ketersediaan Anda telah diperbarui.');
    }
}