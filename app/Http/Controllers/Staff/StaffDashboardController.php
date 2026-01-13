<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Pengembalian;
use App\Models\Fine;
use App\Models\VehicleDamageReport;
use App\Models\VehicleInspection;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StaffDashboardController extends Controller
{
    /**
     * Menampilkan dashboard staff dengan metrik ringkas.
     */
    public function dashboard()
    {
        $totalCompleted = Pengembalian::whereIn('status', ['selesai', 'selesai pengecekan'])->count();
        $needsReview = Pengembalian::where('status', 'menunggu pengecekan')->count();

        $metrics = [
            [
                'label' => 'Perlu Cek',
                'value' => $needsReview,
                'icon_path' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
                'color' => 'yellow',
            ],
            [
                'label' => 'Total Selesai',
                'value' => $totalCompleted,
                'icon_path' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>',
                'color' => 'green',
            ],
        ];

        $latestChecks = Pengembalian::with(['peminjaman.user', 'peminjaman.mobil'])
            ->orderByRaw("FIELD(status, 'menunggu pengecekan') DESC")
            ->orderBy('tanggal_pengembalian', 'desc')
            ->take(5)
            ->get();

        return view('staff.dashboard', compact('metrics', 'latestChecks'));
    }

    /**
     * Menampilkan daftar semua antrean pengembalian.
     */
    public function index()
    {
        $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.mobil'])
            ->orderByRaw("FIELD(status, 'menunggu pengecekan', 'selesai pengecekan', 'selesai')")
            ->orderBy('tanggal_pengembalian', 'desc')
            ->paginate(10);
            
        return view('staff.pengecekan.index', compact('pengembalian'));
    }

    /**
     * Halaman form pengecekan.
     */
    public function cek(Request $request, $kode_pengembalian)
    {
        $pengembalian = Pengembalian::where('kode_pengembalian', $kode_pengembalian)
            ->with(['peminjaman.mobil', 'peminjaman.user', 'fines', 'damageReports'])
            ->first();

        if (!$pengembalian || !$pengembalian->peminjaman) {
            return redirect()->route('staff.pengecekan.index')
                ->with('error', 'Data tidak ditemukan.');
        }

        $mobil = $pengembalian->peminjaman->mobil;
        $tglKembali = $pengembalian->peminjaman->tanggal_kembali;
        $jamSewa = $pengembalian->peminjaman->jam_sewa; 
        $hargaPerHari = $mobil->harga ?? 0;
        
        $infoMobil = $mobil 
            ? "{$mobil->merek} {$mobil->tipe} ({$mobil->id})" 
            : "Plat: " . ($pengembalian->peminjaman->mobil_id ?? 'N/A');
        
        $dueDateTime = Carbon::parse($tglKembali . ' ' . $jamSewa);
        $returnedDateTime = $pengembalian->tanggal_pengembalian 
                            ? Carbon::parse($pengembalian->tanggal_pengembalian) 
                            : Carbon::now();

        $totalHoursDiff = $dueDateTime->diffInHours($returnedDateTime, false);
        $jamTerlambat = max(0, $totalHoursDiff);

        // Rumus denda: 10% harga harian dikali jam keterlambatan
        $lateFine = ($hargaPerHari * 0.1) * $jamTerlambat;

        // Ambil denda dari DB (kerusakan sebelumnya)
        $existingDenda = $pengembalian->fines->sum('total_denda') ?? 0;

        // Tentukan nilai awal untuk AlpineJS (Denda DB + Denda Keterlambatan Baru)
        $totalFines = $existingDenda + $lateFine;

        return view('staff.pengecekan.detail', compact(
            'pengembalian',
            'jamTerlambat',
            'lateFine',
            'hargaPerHari',
            'totalFines',
            'infoMobil'
        ));
    }

    /**
     * Finalisasi pengecekan.
     */
    public function finalisasiPengecekan(Request $request, $kode_pengembalian)
    {
        $request->validate([
            'inspection_condition' => 'required|string',
            'inspection_notes' => 'nullable|string',
            'damage_description' => 'nullable|string',
            'damage_cost' => 'nullable|numeric|min:0',
            'late_fine' => 'nullable|numeric|min:0',
        ]);

        $pengembalian = Pengembalian::where('kode_pengembalian', $kode_pengembalian)->firstOrFail();
        $staff = Staff::where('user_id', Auth::id())->first();

        $existingFine = Fine::where('peminjaman_id', $pengembalian->peminjaman_id)->first();
        $oldDamageCost = $existingFine ? $existingFine->denda_kerusakan : 0;

        // Simpan ke tabel fines
        Fine::updateOrCreate(
            ['peminjaman_id' => $pengembalian->peminjaman_id],
            [
                'denda_keterlambatan' => $request->late_fine ?? 0,
                'denda_kerusakan' => ($request->damage_cost ?? 0) + $oldDamageCost,
                'total_denda' => ($request->late_fine ?? 0) + ($request->damage_cost ?? 0) + $oldDamageCost,
                'status' => 'belum dibayar',
                'tanggal_terdeteksi' => Carbon::today(),
                'keterangan' => $request->damage_description ?? 'Selesai Pengecekan'
            ]
        );

        if ($request->damage_description && $request->damage_cost > 0) {
            VehicleDamageReport::create([
                'mobil_id' => $pengembalian->peminjaman->mobil_id,
                'pengembalian_kode' => $kode_pengembalian,
                'damage_description' => $request->damage_description,
                'damage_cost' => $request->damage_cost
            ]);
        }

        VehicleInspection::create([
            'mobil_id' => $pengembalian->peminjaman->mobil_id,
            'staff_id' => $staff?->id ?? Auth::id(),
            'pengembalian_kode' => $kode_pengembalian,
            'condition' => $request->inspection_condition,
            'keterangan' => $request->inspection_notes
        ]);
        
        $pengembalian->update(['status' => 'selesai pengecekan']);

        return redirect()->route('staff.dashboard')->with('success', 'Pengecekan berhasil diselesaikan.');
    }

    public function detail($kode_pengembalian)
    {
        $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.mobil', 'fines', 'damageReports', 'inspections'])
            ->where('kode_pengembalian', $kode_pengembalian)
            ->firstOrFail();

        $mobil = $pengembalian->peminjaman->mobil;
        $infoMobil = $mobil ? "{$mobil->merek} {$mobil->tipe} ({$mobil->id})" : "Plat: " . ($pengembalian->peminjaman->mobil_id ?? 'N/A');
        
        $dueDateTime = Carbon::parse($pengembalian->peminjaman->tanggal_kembali . ' ' . $pengembalian->peminjaman->jam_sewa);
        $returnedDateTime = Carbon::parse($pengembalian->tanggal_pengembalian);
        
        $jamTerlambat = max(0, $dueDateTime->diffInHours($returnedDateTime, false));
        $totalFines = $pengembalian->fines->sum('total_denda');
        $lateFine = $pengembalian->fines->sum('denda_keterlambatan');
        $hargaPerHari = $mobil->harga ?? 0;

        return view('staff.pengecekan.history_detail', compact(
            'pengembalian', 'totalFines', 'jamTerlambat', 'lateFine', 'hargaPerHari', 'infoMobil'
        ));
    }
}