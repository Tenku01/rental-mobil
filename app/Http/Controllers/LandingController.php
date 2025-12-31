<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
class LandingController extends Controller
{
    public function index()
    {

        // Ambil semua data mobil (atau sesuai kebutuhan)
        $mobils = Mobil::all();

        // Kirim data ke view landing
        return view('welcome', compact('mobils'));
    }
}
