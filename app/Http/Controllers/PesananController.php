<?php

namespace App\Http\Controllers;
use App\Models\Peminjaman;

class PesananController extends Controller
{
    public function index()
    {
        // Ambil pesanan yang terkait dengan user yang login
        $pesanans = Peminjaman::where('user_id', auth())->get();

        return view('user.pesanan.index', compact('pesanans'));
    }
}
