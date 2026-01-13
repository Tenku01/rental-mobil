<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pelanggan.
     */
    public function index()
    {
        $users = User::where('role_id', 2)
                    ->with('pelanggan')
                    ->latest()
                    ->get();

        return view('resepsionis.user.index', compact('users'));
    }

    /**
     * Form tambah pelanggan.
     */
    public function create()
    {
        return view('resepsionis.user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'no_telepon' => 'nullable|string',
            'alamat'   => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => 2,
                'status'   => 'aktif',
            ]);

            $user->pelanggan()->create([
                'nama'       => $request->name,
                'no_telepon' => $request->no_telepon,
                'alamat'     => $request->alamat,
            ]);
        });

        return redirect()->route('resepsionis.user.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * MENAMBAHKAN METODE SHOW (Fix Error: Undefined Method)
     * Menampilkan detail profil pelanggan.
     */
    public function show($id)
    {
        // Mencari user dengan role pelanggan (ID 2) beserta relasi profilnya
        $user = User::where('role_id', 2)
                    ->with('pelanggan')
                    ->findOrFail($id);

        return view('resepsionis.user.show', compact('user'));
    }

    /**
     * Form edit pelanggan.
     */
    public function edit($id)
    {
        $user = User::where('role_id', 2)->with('pelanggan')->findOrFail($id);
        return view('resepsionis.user.edit', compact('user'));
    }

    /**
     * Update data pelanggan.
     */
    public function update(Request $request, $id)
    {
        $user = User::where('role_id', 2)->findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'status' => 'required|in:aktif,nonaktif',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name'   => $request->name,
                'email'  => $request->email,
                'status' => $request->status,
            ]);

            $user->pelanggan()->update([
                'nama'       => $request->name,
                'no_telepon' => $request->no_telepon,
                'alamat'     => $request->alamat,
            ]);
        });

        return redirect()->route('resepsionis.user.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    /**
     * Hapus data pelanggan.
     */
    public function destroy($id)
    {
        $user = User::where('role_id', 2)->findOrFail($id);
        $user->delete();

        return redirect()->route('resepsionis.user.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}