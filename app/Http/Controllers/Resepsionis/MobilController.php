<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobilController extends Controller
{
    public function index()
    {
        $mobils = Mobil::latest()->paginate(10);

        return view('resepsionis.mobil.index', compact('mobils'));
    }

    public function create()
    {
        return view('resepsionis.mobil.create', [
            'title' => 'Tambah Mobil'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe' => 'required',
            'merek' => 'required',
            'warna' => 'required',
            'transmisi' => 'required|in:manual,otomatis',
            'kursi' => 'required|in:5,7,9',
            'harga' => 'required|numeric',
            'foto' => 'nullable|image|max:2048',
            'status' => 'required|in:tersedia,disewa,pemeliharaan,dibersihkan',
        ]);

        // Upload foto
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('mobils', 'public');
        }

        Mobil::create($validated);

        return redirect()->route('resepsionis.mobil.index')->with('success', 'Mobil berhasil ditambahkan!');
    }

    public function show(Mobil $mobil)
    {
        return view('resepsionis.mobil.show', compact('mobil'));
    }

    public function edit(Mobil $mobil)
    {
        return view('resepsionis.mobil.edit', compact('mobil'));
    }

    public function update(Request $request, Mobil $mobil)
    {
        $validated = $request->validate([
            'tipe' => 'required',
            'merek' => 'required',
            'warna' => 'required',
            'transmisi' => 'required|in:manual,otomatis',
            'kursi' => 'required|in:5,7,9',
            'harga' => 'required|numeric',
            'foto' => 'nullable|image|max:2048',
            'status' => 'required|in:tersedia,disewa,pemeliharaan,dibersihkan',
        ]);

        if ($request->hasFile('foto')) {
            if ($mobil->foto) {
                Storage::disk('public')->delete($mobil->foto);
            }
            $validated['foto'] = $request->file('foto')->store('mobils', 'public');
        }

        $mobil->update($validated);

        return redirect()->route('resepsionis.mobil.index')->with('success', 'Mobil berhasil diperbarui!');
    }
}
