<?php

namespace App\Http\Controllers\Resepsionis;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::all();
        return view('resepsionis.pelanggan.index', compact('pelanggans'));
    }

    public function create()
    {
        $users = User::all();
        return view('resepsionis.pelanggan.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
        ]);

        Pelanggan::create($request->all());
        return redirect()->route('resepsionis.pelanggan.index')->with('success', 'Pelanggan berhasil dibuat');
    }

    public function edit(Pelanggan $pelanggan)
    {
        $users = User::all();
        return view('resepsionis.pelanggan.edit', compact('pelanggan', 'users'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
        ]);

        $pelanggan->update($request->all());
        return redirect()->route('resepsionis.pelanggan.index')->with('success', 'Pelanggan berhasil diupdate');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();
        return redirect()->route('resepsionis.pelanggan.index')->with('success', 'Pelanggan berhasil dihapus');
    }
}
