<?php

namespace App\Http\Controllers\Sopir;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\DriverLogbook;
use App\Models\Sopir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LogbookController extends Controller
{
    /**
     * Menampilkan dashboard tugas aktif sopir.
     * Tugas didefinisikan sebagai Peminjaman yang statusnya 'berlangsung' dan ditugaskan kepada sopir ini.
     */
    public function index()
    {
        // Mendapatkan instance Sopir yang sedang login
        $sopir = Sopir::where('user_id', Auth::id())->firstOrFail();
        
        // Ambil peminjaman yang ditugaskan dan statusnya 'berlangsung'
        $tasks = Peminjaman::with('mobil', 'user.pelanggan')
            ->where('sopir_id', $sopir->id)
            ->where('status', 'berlangsung')
            ->get();

        // Cek status logbook harian untuk setiap tugas
        $tasks->each(function ($peminjaman) {
            $peminjaman->logbook_hari_ini = DriverLogbook::where('peminjaman_id', $peminjaman->id)
                ->whereDate('tanggal_aktivitas', now()->toDateString())
                ->exists();
        });

        // Tampilkan view dashboard sopir
        return view('sopir.dashboard', compact('tasks'));
    }

    /**
     * Menampilkan formulir Logbook atau riwayat Logbook untuk peminjaman tertentu.
     */
    public function show(Peminjaman $peminjaman)
    {
        // Memastikan sopir yang login berhak mengakses tugas ini
        $sopir = Sopir::where('user_id', Auth::id())->firstOrFail();
        if ($peminjaman->sopir_id !== $sopir->id) {
            abort(403, 'Anda tidak ditugaskan pada peminjaman ini.');
        }
        
        // Redirect jika tugas sudah selesai
        if ($peminjaman->status === 'selesai') {
             return redirect()->route('sopir.dashboard')->with('error', 'Tugas ini sudah selesai.');
        }

        // Ambil riwayat logbook
        $logbooks = DriverLogbook::where('peminjaman_id', $peminjaman->id)
            ->orderBy('tanggal_aktivitas', 'desc')
            ->get();

        // Tampilkan view formulir logbook
        return view('sopir.logbook_form', compact('peminjaman', 'logbooks'));
    }

    /**
     * Menyimpan entri Logbook baru.
     */
    public function store(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'deskripsi_aktivitas' => 'required|string',
            'status_log' => ['required', Rule::in(['mulai_kerja', 'dalam_perjalanan', 'selesai_hari_ini', 'selesai_peminjaman'])],
            'foto_bukti' => 'nullable|image|max:2048', // Batas 2MB
        ]);

        // Gunakan transaksi database untuk operasi ganda (insert & update)
        DB::transaction(function () use ($request, $peminjaman) {
            $sopir = Sopir::where('user_id', Auth::id())->firstOrFail();
            
            // Verifikasi otorisasi dan status peminjaman
            if ($peminjaman->sopir_id !== $sopir->id) {
                return redirect()->back()->with('error', 'Akses ditolak.');
            }
            if ($peminjaman->status !== 'berlangsung') {
                return redirect()->back()->with('error', 'Peminjaman tidak dalam status berlangsung.');
            }

            $fotoPath = null;
            if ($request->hasFile('foto_bukti')) {
                // Simpan foto bukti di storage publik
                $fotoPath = $request->file('foto_bukti')->store('logbook_photos', 'public');
            }

            // 1. INSERT entri Logbook
            DriverLogbook::create([
                'peminjaman_id' => $peminjaman->id,
                'tanggal_aktivitas' => now()->toDateString(), 
                'deskripsi_aktivitas' => $request->deskripsi_aktivitas,
                'status_log' => $request->status_log,
                'foto_bukti' => $fotoPath,
            ]);
            
            // 2. Logika Penyelesaian Tugas
            if ($request->status_log === 'selesai_peminjaman') {
                // Tandai peminjaman sebagai 'selesai'
                $peminjaman->status = 'selesai'; 
                $peminjaman->save();

                // Kembalikan status sopir menjadi 'tersedia'
                $sopir->status = 'tersedia';
                $sopir->save();

                return redirect()->route('sopir.dashboard')->with('success', 'Tugas peminjaman berhasil diselesaikan. Status Anda kini tersedia.');
            }
        });

        // Redirect ke halaman logbook setelah entri harian
        return redirect()->route('sopir.logbook.show', $peminjaman)->with('success', 'Logbook harian berhasil disimpan.');
    }
}