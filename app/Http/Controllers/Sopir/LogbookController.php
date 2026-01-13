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
use Carbon\Carbon;

class LogbookController extends Controller
{
    /**
     * Menampilkan daftar tugas aktif sopir untuk logbook.
     */
    public function index()
    {
        // Set locale Carbon ke Bahasa Indonesia agar diffForHumans() otomatis berbahasa Indonesia
        Carbon::setLocale('id');

        $sopir = Sopir::where('user_id', Auth::id())->firstOrFail();
        
        $tasks = Peminjaman::with('mobil', 'user.pelanggan')
            ->where('sopir_id', $sopir->id)
            ->where('status', 'berlangsung')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Mengambil data logbook terakhir hari ini untuk setiap tugas untuk info status "Real Time"
        $tasks->each(function ($peminjaman) {
            $peminjaman->latest_logbook = DriverLogbook::where('peminjaman_id', $peminjaman->id)
                ->whereDate('tanggal_aktivitas', now()->toDateString())
                ->orderBy('waktu_log', 'desc')
                ->first();
        });

        return view('sopir.logbook_index', compact('tasks'));
    }

    /**
     * Menampilkan formulir Logbook untuk peminjaman tertentu.
     */
    public function show(Peminjaman $peminjaman)
    {
        // Set locale Carbon ke Bahasa Indonesia
        Carbon::setLocale('id');

        $sopir = Sopir::where('user_id', Auth::id())->firstOrFail();
        
        if ($peminjaman->sopir_id !== $sopir->id) {
            abort(403, 'Anda tidak ditugaskan pada peminjaman ini.');
        }
        
        if ($peminjaman->status === 'selesai') {
            return redirect()->route('sopir.logbook.index')
                ->with('error', 'Tugas ini sudah selesai.');
        }

        // Ambil riwayat logbook menggunakan pagination
        $logbooks = DriverLogbook::where('peminjaman_id', $peminjaman->id)
            ->orderBy('tanggal_aktivitas', 'desc')
            ->orderBy('waktu_log', 'desc')
            ->paginate(10);

        // Ambil logbook terakhir hari ini untuk informasi status di form
        $logbook_hari_ini = DriverLogbook::where('peminjaman_id', $peminjaman->id)
            ->whereDate('tanggal_aktivitas', now()->toDateString())
            ->orderBy('waktu_log', 'desc')
            ->first();

        return view('sopir.logbook_form', compact('peminjaman', 'logbooks', 'logbook_hari_ini'));
    }

    /**
     * Menyimpan entri Logbook baru.
     */
    public function store(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'deskripsi_aktivitas' => 'required|string|min:10|max:500',
            'status_log' => ['required', Rule::in([
                'mulai_kerja', 
                'dalam_perjalanan', 
                'selesai_hari_ini', 
                'selesai_peminjaman'
            ])],
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $sopir = Sopir::where('user_id', Auth::id())->firstOrFail();
        
        if ($peminjaman->sopir_id !== $sopir->id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        return DB::transaction(function () use ($request, $peminjaman, $sopir) {
            $fotoPath = null;
            if ($request->hasFile('foto_bukti')) {
                $fotoPath = $request->file('foto_bukti')->store('logbook_photos', 'public');
            }

            // Simpan logbook dengan waktu_log saat ini
            DriverLogbook::create([
                'peminjaman_id' => $peminjaman->id,
                'tanggal_aktivitas' => now()->toDateString(),
                'waktu_log' => now(), 
                'deskripsi_aktivitas' => strip_tags($request->deskripsi_aktivitas),
                'status_log' => $request->status_log,
                'foto_bukti' => $fotoPath,
            ]);

            // Logika update status sopir dan peminjaman
            if ($request->status_log === 'selesai_peminjaman') {
                $peminjaman->update(['status' => 'selesai', 'tanggal_selesai' => now()]);
                $sopir->update(['status' => 'tersedia']);
                
                return redirect()->route('sopir.dashboard')
                    ->with('success', 'Tugas selesai! Status Anda kembali tersedia.');
            }

            if ($request->status_log === 'mulai_kerja') {
                $sopir->update(['status' => 'bekerja']);
            }

            if ($request->status_log === 'selesai_hari_ini') {
                $sopir->update(['status' => 'tersedia']);
            }

            return redirect()->route('sopir.logbook.show', $peminjaman)
                ->with('success', 'Aktivitas berhasil dicatat!');
        });
    }

    /**
     * Menampilkan riwayat logbook sopir secara keseluruhan.
     */
    public function history(Request $request)
    {
        // Set locale Carbon ke Bahasa Indonesia
        Carbon::setLocale('id');

        $sopir = Sopir::where('user_id', Auth::id())->firstOrFail();
        
        $query = DriverLogbook::with('peminjaman.mobil')
            ->whereHas('peminjaman', function ($q) use ($sopir) {
                $q->where('sopir_id', $sopir->id);
            });
            
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_aktivitas', $request->tanggal);
        }
        
        $logbooks = $query->orderBy('tanggal_aktivitas', 'desc')
            ->orderBy('waktu_log', 'desc')
            ->paginate(15);
            
        return view('sopir.logbook_history', compact('logbooks'));
    }
}