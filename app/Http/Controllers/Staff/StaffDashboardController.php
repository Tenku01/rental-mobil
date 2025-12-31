<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Pengembalian;
use App\Models\Fine;
use App\Models\VehicleDamageReport;
use App\Models\VehicleInspection;
use App\Models\Peminjaman;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StaffDashboardController extends Controller
{
    public function dashboard()
    {
        $totalCompleted = Pengembalian::where('status', 'Selesai')->count();
        $needsReview = Pengembalian::where('status', 'menunggu pengecekan')->count();

      $metrics = [
        [
        'label' => 'Perlu Cek',
        'value' => $needsReview,
        'icon_path' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
            1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
            0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
        'color' => 'yellow',
    ],
    [
        'label' => 'Total Selesai',
        'value' => $totalCompleted,
        'icon_path' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M5 13l4 4L19 7"></path>',
        'color' => 'green',
    ],
    
];

        $latestChecks = Pengembalian::latest('tanggal_pengembalian')
            ->whereNotNull('staff_id')
            ->with('staff.user')
            ->take(5)
            ->get();

        return view('staff.dashboard', compact('metrics', 'latestChecks'));
    }

    public function index()
    {
        return view('staff.pengecekan.index');
    }

    public function cek(Request $request, $kode_pengembalian)
    {
        $pengembalian = Pengembalian::where('kode_pengembalian', $kode_pengembalian)
            ->with(['peminjaman.mobil', 'peminjaman.user', 'fines', 'damageReports'])
            ->first();

        if (!$pengembalian) {
            return redirect()->route('staff.pengecekan.index')
                ->with('error', 'Kode pengembalian tidak ditemukan.');
        }

        if ($pengembalian->status == 'Selesai') {
            return redirect()->route('staff.pengecekan.index')
                ->with('warning', 'Pengembalian ini sudah selesai.');
        }

        // Hitung keterlambatan
        $due = Carbon::parse($pengembalian->peminjaman->tanggal_kembali);
        $returned = Carbon::parse($pengembalian->tanggal_pengembalian);

        $lateDays = max(0, $due->diffInDays($returned, false));
        $lateFine = $lateDays * 50000;

        $totalDendaAwal = $pengembalian->getTotalDendaAttribute();

        return view('staff.pengecekan.detail', compact(
            'pengembalian',
            'lateDays',
            'lateFine',
            'totalDendaAwal'
        ));
    }

    public function finalisasiPengecekan(Request $request, $kode_pengembalian)
    {
        $request->validate([
            'inspection_condition' => 'required|string',
            'inspection_notes' => 'nullable|string',
            'damage_description' => 'nullable|string',
            'damage_cost' => 'nullable|numeric|min:0',
            'late_fine' => 'nullable|numeric|min:0',
            'late_days' => 'nullable|numeric|min:0',
            // 'payment_status' => 'required|string',
            // 'payment_method' => 'nullable|string',
        ]);

        $pengembalian = Pengembalian::where('kode_pengembalian', $kode_pengembalian)->firstOrFail();
        $staff = Staff::where('user_id', Auth::id())->first();

        // 1️⃣ Catat denda keterlambatan
        if ($request->late_fine > 0) {
            Fine::create([
                'peminjaman_id' => $pengembalian->peminjaman_id,
                'pengembalian_kode' => $kode_pengembalian,
                'jumlah_denda' => $request->late_fine,
                'status' => 'belum dibayar',
                'tanggal_terdeteksi' => Carbon::today(),
                'keterangan' => "Keterlambatan {$request->late_days} hari"
            ]);
        }

        // 2️⃣ Catat kerusakan (jika ada)
        if ($request->damage_description && $request->damage_cost > 0) {
            VehicleDamageReport::create([
                'mobil_id' => $pengembalian->peminjaman->mobil_id,
                'pengembalian_kode' => $kode_pengembalian,
                'damage_description' => $request->damage_description,
                'damage_cost' => $request->damage_cost
            ]);
        }

        // 3️⃣ Catat hasil inspeksi
        VehicleInspection::create([
            'mobil_id' => $pengembalian->peminjaman->mobil_id,
            'staff_id' => $staff?->id,
            'pengembalian_kode' => $kode_pengembalian,
            'condition' => $request->inspection_condition,
            'keterangan' => $request->inspection_notes
        ]);
        
// 4️⃣ Update status pengembalian
$pengembalian->update([
    'status' => 'selesai pengecekan'
]);


        // // 4️⃣ Update tabel pengembalian
        // $pengembalian->update([
        //     'staff_id' => $staff?->id,
        //     'status_pembayaran_denda' => $request->payment_status,
        //     'metode_pembayaran' => $request->payment_method,
        //     'status' => 'Selesai'
        // ]);

        return redirect()->route('staff.dashboard')
            ->with('success', "Pengecekan pengembalian $kode_pengembalian selesai diproses.");
    }

   public function detail($kode_pengembalian)
{
    // Ambil data pengembalian lengkap dengan relasi
    $pengembalian = Pengembalian::with([
        'peminjaman',
        'peminjaman.user',
        'peminjaman.mobil',
        'fines',
        'damageReports'
    ])
    ->where('kode_pengembalian', $kode_pengembalian)
    ->firstOrFail();

    // Hitung keterlambatan
    $due = Carbon::parse($pengembalian->peminjaman->tanggal_kembali);
    $returned = Carbon::parse($pengembalian->tanggal_pengembalian ?? now());

    $lateDays = max(0, $due->diffInDays($returned, false));
    $dendaKeterlambatan = $lateDays * 50000; // atur sesuai kebutuhan

    // Hitung total denda awal dari fines + damageReports sebelumnya
    $totalFines = $pengembalian->fines->sum('jumlah_denda');
    $totalDamageCost = $pengembalian->damageReports->sum('damage_cost');

    $totalDendaAwal = $totalFines + $totalDamageCost;

    return view('staff.pengecekan.detail', compact(
        'pengembalian',
        'lateDays',
        'dendaKeterlambatan',
        'totalDendaAwal'
    ));
}


}
