<?php

namespace App\Http\Controllers\User;

use App\Models\Peminjaman;
use App\Models\PembatalanPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class PembatalanPesananController extends Controller
{
    /**
     * USER mengajukan pembatalan (status peminjaman TIDAK berubah).
     * Hanya boleh saat status "sudah dibayar lunas" atau "berlangsung".
     * Tercatat sebagai approval_status = 'pending' menunggu admin.
     */
    public function store(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'alasan' => 'nullable|string|max:2000',
        ]);

        // Pastikan pemilik
        if ($peminjaman->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'error'   => 'Unauthorized'
            ], 403);
        }

        // Batasi hanya 2 status ini yang bisa diajukan pembatalan
        if (! in_array($peminjaman->status, ['sudah dibayar lunas', 'berlangsung'])) {
            return response()->json([
                'success' => false,
                'error'   => 'Status tidak mengizinkan pembatalan'
            ], 422);
        }

        // Cegah duplikasi pengajuan pending
        if ($peminjaman->pembatalan && $peminjaman->pembatalan->approval_status === 'pending') {
            return response()->json([
                'success' => false,
                'error'   => 'Pengajuan pembatalan sudah ada dan menunggu persetujuan admin'
            ], 422);
        }

        // Kebijakan refund default saat pengajuan:
        // - Kalau "sudah dibayar lunas" → pending_refund (akan diputus admin)
        // - Kalau "berlangsung" → no_refund (umumnya tidak direfund; bisa diubah oleh admin)
        $refundStatus = $peminjaman->status === 'sudah dibayar lunas' ? 'pending_refund' : 'no_refund';

        $pembatalan = PembatalanPesanan::create([
            'peminjaman_id'  => $peminjaman->id,
            'user_id'        => Auth::id(),
            'cancelled_by'   => 'user',
            'alasan'         => $request->alasan,
            'refund_status'  => $refundStatus,
            'cancelled_at'   => now(),
            'approval_status'=> 'pending',   // ← kunci: tunggu admin
        ]);

        // Penting: JANGAN ubah status peminjaman di sini
        // $peminjaman->status = 'dibatalkan';  // ❌ NO
        // $peminjaman->save();                 // ❌ NO

        return response()->json([
            'success'         => true,
            'pembatalan_id'   => $pembatalan->id,
            'message'         => 'Pengajuan pembatalan dikirim. Menunggu persetujuan admin.'
        ]);
    }
}
