<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Mobil;
use Illuminate\Http\Request;

class MobilControllerUser extends Controller
{
    public function show($id)
{
    $mobil = Mobil::findOrFail($id);
    return view('user.armada.mobil-detail', compact('mobil'));
}

    public function mobil(Request $request)
    {
        // Query dasar mobil
     $query = Mobil::query();

        // 🔹 Cek apakah user sudah upload identitas (hanya jika sudah login)
        $hasIdentification = false;
        if (Auth::check()) {
            $hasIdentification = DB::table('user_identifications')
                ->where('user_id', Auth::id())
                ->where('status_approval', 'disetujui')
                ->exists();
        }

        // 🔹 Filter jumlah kursi (jika ada)
        if ($request->filled('jumlah_kursi')) {
            $query->where('kursi', $request->jumlah_kursi);
        }

        // 🔹 Filter transmisi (jika ada)
        if ($request->filled('transmisi')) {
            $query->where('transmisi', $request->transmisi);
        }

        // 🔹 Ambil data mobil dengan pagination
        $mobils = $query->paginate(6); // 6 per halaman

        // 🔹 Kirim data ke view
        return view('user.armada.mobil', compact('mobils', 'hasIdentification'));
    }
}
