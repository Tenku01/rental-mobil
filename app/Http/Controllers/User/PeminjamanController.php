<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Mobil;
use App\Models\Peminjaman;
use App\Models\PaymentTransaction;
use App\Models\Sopir; // Pastikan Model Sopir diimport
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Midtrans\Snap;
use Midtrans\Config;
use App\Jobs\AssignDriver; 

class PeminjamanController extends Controller
{
    
    public function checkDriver(Request $request)
{
    // SELALU RETURN ADA SOPIR UNTUK TESTING
    return response()->json([
        'available'     => true,
        'total_sopir'   => 2,
        'sedang_tugas'  => 0,
        'sisa'          => 2,
        'message'       => 'DEBUG: Selalu return tersedia'
    ]);
}

    public function show($id)
    {
        // Ambil data peminjaman berdasarkan ID
        $peminjaman = Peminjaman::findOrFail($id); 
    
        // Kirim data ke tampilan
        return view('user.pesanan.pesanan', compact('peminjaman'));
    }
    
    public function index()
    {
        $peminjaman = Peminjaman::with([
                'mobil',
                'paymentTransactions',
                'pembatalan', // ⬅︎ penting: agar UI bisa baca approval_status tanpa N+1
            ])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    
        return view('user.pesanan.pesanan', compact('peminjaman'));
    }

    /**
     * 🔹 Simulasikan Midtrans Notification Handler untuk konfirmasi pembayaran sukses.
     */
    public function handlePaymentSuccess(Request $request)
    {
        $peminjamanId = $request->input('peminjaman_id');
        $tipeTransaksi = $request->input('tipe_transaksi'); 
        
        $peminjaman = Peminjaman::find($peminjamanId);

        if (!$peminjaman) {
            return response()->json(['error' => 'Peminjaman tidak ditemukan'], 404);
        }

        // Logika update status peminjaman
        if ($tipeTransaksi === 'lunas' || $tipeTransaksi === 'sisa') {
            $peminjaman->status = 'sudah dibayar lunas';
            $peminjaman->total_dibayarkan = $peminjaman->total_harga; 
        } elseif ($tipeTransaksi === 'dp') {
            $peminjaman->status = 'pembayaran dp';
        }
        
        $peminjaman->save();

        // --- Logic Penugasan Sopir ---
        if ($peminjaman->add_on_sopir) {
            // Pemicu otomatis penugasan sopir setelah pembayaran lunas/DP sukses
            AssignDriver::dispatch($peminjaman);
        }
        // --- End Logic Penugasan Sopir ---

        return response()->json(['message' => 'Status peminjaman diperbarui dan sopir ditugaskan jika diperlukan.'], 200);
    }

    /**
     * 🔹 Cek kondisi mobil saat pengembalian
     */
   public function cekKondisi(Request $request, Peminjaman $peminjaman)
    {
        try {
            $request->validate([
                'kondisi' => 'nullable|string', 
            ]);

            if ($peminjaman->status !== 'sudah dibayar lunas') {
                return response()->json(['error' => 'Mobil belum dilunasi, tidak bisa melakukan pengecekan kondisi.'], 400);
            }
            
            $existingKondisi = $peminjaman->kondisi_mobil ?? '';
            $newKondisi = $existingKondisi;
            if ($request->kondisi) {
                $newKondisi = $existingKondisi ? $existingKondisi . ' dan ' . $request->kondisi : $request->kondisi;
            }

            $mobil = $peminjaman->mobil;
            $mobil->update(['status' => 'tersedia']); 

            $peminjaman->update([
                'status' => 'berlangsung',
                'kondisi_mobil' => $newKondisi, 
                'tanggal_pengembalian' => Carbon::now(), 
            ]);

            return response()->json(['success' => '✅ Kondisi mobil berhasil dikonfirmasi dan status diperbarui menjadi Berlangsung.']);
        } catch (\Exception $e) {
            Log::error('Cek kondisi gagal: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memproses pengecekan kondisi.'], 500);
        }
    }
    
    /**
     * 🔹 Menampilkan form peminjaman
     */
     public function create($mobil_id)
    {
        $mobil = Mobil::findOrFail($mobil_id);

        $hasIdentification = DB::table('user_identifications')
            ->where('user_id', Auth::id())
            ->exists();

        if (!$hasIdentification) {
            return redirect()->back()
                ->with('warning', '⚠ Identitas belum dilengkapi. Silakan unggah identitas Anda terlebih dahulu sebelum melakukan peminjaman.');
        }

        // Catatan: $isDriverAvailable di sini hanya cek status statis 'tersedia' sebagai inisial.
        // Pengecekan real-time berdasarkan tanggal dilakukan via AJAX ke checkDriverAvailability.
        $isDriverAvailable = Sopir::where('status', 'tersedia')->exists();

        $bookedDates = [];
        foreach ($mobil->peminjaman as $p) {
            $start =Carbon::parse($p->tanggal_sewa);
            $end = Carbon::parse($p->tanggal_kembali);
            for ($date = $start; $date->lte($end); $date->addDay()) {
                $bookedDates[] = $date->format('d-m-Y'); 
            }
        }

        return view('user.pesanan.peminjaman', compact('mobil', 'bookedDates', 'isDriverAvailable'));
    }

    /**
     * 🔹 Menyimpan data peminjaman dan membuat transaksi Midtrans DP
     */
    public function store(Request $request)
    {
        $request->validate([
            'mobil_id' => 'required|exists:mobils,id',
            'tanggal_sewa' => 'required|string', 
            'jam_sewa' => 'required',
            'tanggal_kembali' => 'required|string|different:tanggal_sewa',
            'add_on_sopir' => 'required|boolean',
            'metode_pembayaran' => 'nullable|string',
            'tipe_pembayaran' => 'required|in:dp,lunas',
            'dp' => [
                'nullable',
                'numeric',
                'min:1000', 
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->tipe_pembayaran === 'dp') {
                        if (is_null($value)) {
                            $fail('Nominal DP wajib diisi.');
                        } elseif ($value > 10000000) {
                            $fail('Nominal DP terlalu besar.');
                        }
                    }
    
                    if ($request->tipe_pembayaran === 'lunas' && !is_null($value) && $value > 0) {
                        $fail('DP tidak perlu diisi untuk pembayaran lunas.');
                    }
                },
            ],
        ]);
    
        $mobil = Mobil::findOrFail($request->mobil_id);
    
        // ✅ Ubah format tanggal dari dd-mm-yyyy ke yyyy-mm-dd
        $tanggalSewa = Carbon::createFromFormat('d-m-Y', $request->tanggal_sewa)->format('Y-m-d');
        $tanggalKembali = Carbon::createFromFormat('d-m-Y', $request->tanggal_kembali)->format('Y-m-d');

        $sopirId = null;

if ((int) $request->add_on_sopir === 1) {

    // ✅ FIX FORMAT TANGGAL
    $start = Carbon::parse($tanggalSewa)->format('Y-m-d');
    $end   = Carbon::parse($tanggalKembali)->format('Y-m-d');

    $sopirBentrokIds = Peminjaman::whereNotNull('sopir_id')
        ->whereIn('status', [
            'menunggu pembayaran',
            'pembayaran dp',
            'sudah dibayar lunas',
            'berlangsung'
        ])
        ->where('tanggal_sewa', '<=', $end)
        ->where('tanggal_kembali', '>=', $start)
        ->pluck('sopir_id')
        ->unique();

    $sopir = Sopir::where('status', 'tersedia')
        ->whereNotIn('id', $sopirBentrokIds)
        ->first();

    if (!$sopir) {
        return response()->json([
            'error' => 'Maaf, semua sopir sedang bertugas pada tanggal tersebut.'
        ], 422);
    }

    $sopir->update([
        'status' => 'bekerja'
    ]);

    $sopirId = $sopir->id;
}

    
        // 🔍 Cek bentrok booking mobil
        $conflict = Peminjaman::where('mobil_id', $mobil->id)
            ->where(function ($query) use ($tanggalSewa, $tanggalKembali) {
                $query->whereBetween('tanggal_sewa', [$tanggalSewa, $tanggalKembali])
                    ->orWhereBetween('tanggal_kembali', [$tanggalSewa, $tanggalKembali])
                    ->orWhere(function ($q) use ($tanggalSewa, $tanggalKembali) {
                        $q->where('tanggal_sewa', '<=', $tanggalSewa)
                            ->where('tanggal_kembali', '>=', $tanggalKembali);
                    });
            })
            ->exists();
    
        if ($conflict) {
            return response()->json([
                'error' => 'Mobil sudah dibooking pada tanggal yang dipilih.'
            ], 422);
        }
    
        // 🔹 Hitung lama sewa
        $start = Carbon::parse($tanggalSewa . ' ' . $request->jam_sewa);
        $end = Carbon::parse($tanggalKembali . ' ' . $request->jam_sewa);
        $lama = ceil($start->diffInHours($end) / 24);
        $lama = max($lama, 1);
    
        $biayaSewa = $lama * $mobil->harga;
        $biayaSopir = $request->add_on_sopir ? 1000 * $lama : 0; // ASLI 150k/hari
        $total = $biayaSewa + $biayaSopir;
    
        // 🔹 Tentukan logika pembayaran
        if ($request->tipe_pembayaran === 'dp') {
            $dp = $request->dp;
            $sisa = $total - $dp;
            $status = 'menunggu pembayaran';
            $tipeTransaksi = 'dp';
            $jumlahBayar = $dp;
        } else {
            $dp = 0.00;
            $sisa = 0.00;
            $status = 'menunggu pembayaran';
            $tipeTransaksi = 'lunas';
            $jumlahBayar = $total;
        }
    
        if ($jumlahBayar > $total) {
            return response()->json(['error' => 'Nominal pembayaran tidak valid.'], 400);
        }

    
        // 🔸 Simpan peminjaman
        $peminjaman = Peminjaman::create([
            'user_id' => Auth::id(),
            'mobil_id' => $mobil->id,
            'sopir_id' => $sopirId,
            'tanggal_sewa' => $tanggalSewa,
            'jam_sewa' => $request->jam_sewa,
            'tanggal_kembali' => $tanggalKembali,
            'add_on_sopir' => $request->add_on_sopir,
            'total_harga' => $total,
            'dp_dibayarkan' => $dp,
            'sisa_bayar' => $sisa,
            'total_dibayarkan' => $jumlahBayar,
            'status' => $status,
            'tipe_pembayaran' => $tipeTransaksi,
            'metode_pembayaran' => $request->metode_pembayaran ?? 'transfer',
            'bukti_transaksi' => null,
        ]);
    
        $mobil->update(['status' => 'disewa']);
    
        try {
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;
    
            $orderId = strtoupper($tipeTransaksi) . '-' . $peminjaman->id . '-' . time();
    
            $midtransParams = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $jumlahBayar,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                 'enabled_payments' => [
                'shopeepay',
                'qris',
                'bank_transfer',
                'credit_card',
                 ],
            'gopay' => [
                'enable_callback' => true,
            ],
            'qris' => [
                'acquirer' => 'gopay', 
            ],  
            ];
    
            $snapToken = Snap::getSnapToken($midtransParams);
    
            PaymentTransaction::create([
                'peminjaman_id' => $peminjaman->id,
                'midtrans_transaction_id' => $orderId,
                'status' => 'pending',
                'amount' => $jumlahBayar,
                'tipe_transaksi' => $tipeTransaksi,
                'midtrans_response' => json_encode($midtransParams),
            ]);
    
            return response()->json([
                'snap_token' => $snapToken,
                'peminjaman_id' => $peminjaman->id,
                'tipe_pembayaran' => $request->tipe_pembayaran,
                'jumlah_bayar' => $jumlahBayar,
                'total' => $total,
                'sisa' => $sisa,
            ]);
        } catch (\Exception $e) {
            $mobil->update(['status' => 'tersedia']);
            $peminjaman->delete();
    
            return response()->json([
                'error' => '❌ Gagal membuat transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function cancel($id)
    {
        $peminjaman = Peminjaman::find($id);
    
        if (!$peminjaman) {
            return response()->json(['error' => 'Data peminjaman tidak ditemukan'], 404);
        }
    
        // 🔹 Hapus transaksi Midtrans yang pending
        PaymentTransaction::where('peminjaman_id', $peminjaman->id)
            ->where('status', 'pending')
            ->delete();
    
        // 🔹 Ubah status mobil jadi tersedia lagi
        if ($peminjaman->mobil) {
            $peminjaman->mobil->update(['status' => 'tersedia']);
        }
    
        // 🔹 Hapus data peminjaman
        $peminjaman->delete();
    
        return response()->json(['message' => 'Peminjaman dibatalkan dan data dihapus']);
    }
    
    
    /**
     * 🔹 Halaman daftar pesanan user
     */
    public function pesananSaya()
    {
        $peminjaman = Peminjaman::where('user_id', Auth::id())
            ->with('mobil')
            ->get();
    
        return view('user.pesanan.pesanan', compact('peminjaman'));
    }
    
    /**
     * 🔹 Proses pelunasan sisa bayar
     */
    public function bayarSisa(Request $request, $id)
    {
        $peminjaman = Peminjaman::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    
        if ($peminjaman->status !== 'pembayaran dp') {
            return response()->json(['error' => 'Peminjaman tidak memerlukan pelunasan.'], 400);
        }
    
        $sisa = $peminjaman->sisa_bayar;
    
        try {
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;
    
            $orderId = 'SEWA-LUNAS-' . $peminjaman->id . '-' . time();
    
            $midtransParams = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $sisa,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'enabled_payments' => ['shopeepay',
                'qris','bank_transfer','credit_card'], 
                'gopay' => [
                'enable_callback' => true,
            ],
            'qris' => [
                'acquirer' => 'gopay', 
            ], 
            ];
    
            $snapToken = Snap::getSnapToken($midtransParams);
    
            PaymentTransaction::create([
                'peminjaman_id' => $peminjaman->id,
                'midtrans_transaction_id' => $orderId,
                'status' => 'pending',
                'amount' => $sisa,
                'tipe_transaksi' => 'sisa',
                'midtrans_response' => json_encode($midtransParams),
            ]);
    
            return response()->json([
                'snap_token' => $snapToken,
                'peminjaman_id' => $peminjaman->id,
                'sisa' => $sisa,
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => '❌ Gagal membuat transaksi pelunasan: ' . $e->getMessage()
            ], 500);
        }
    }
}