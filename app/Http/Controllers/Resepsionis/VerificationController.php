<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerificationController extends Controller
{
    /**
     * Menampilkan daftar identitas pelanggan yang butuh verifikasi.
     * Hak Akses: Read
     */
    public function index()
    {
        $identities = DB::table('user_identifications')
            ->join('users', 'user_identifications.user_id', '=', 'users.id')
            ->select('user_identifications.*', 'users.name as user_name', 'users.email as user_email')
            ->orderBy('tanggal_upload', 'desc')
            ->get();

        return view('resepsionis.verifikasi.index', compact('identities'));
    }

    /**
     * Menampilkan detail dokumen (KTP & SIM).
     * Hak Akses: Read
     */
    public function show($id)
    {
        $identity = DB::table('user_identifications')
            ->join('users', 'user_identifications.user_id', '=', 'users.id')
            ->select('user_identifications.*', 'users.name as user_name')
            ->where('user_identifications.id', $id)
            ->first();

        if (!$identity) {
            return redirect()->route('resepsionis.verifikasi.index')->with('error', 'Data tidak ditemukan.');
        }

        return view('resepsionis.verifikasi.show', compact('identity'));
    }

    /**
     * Update status verifikasi (Approve/Reject).
     * Hak Akses: Write
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
        ]);

        DB::table('user_identifications')
            ->where('id', $id)
            ->update([
                'status_approval' => $request->status,
                'updated_at' => now()
            ]);

        return redirect()->route('resepsionis.verifikasi.index')
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }
}