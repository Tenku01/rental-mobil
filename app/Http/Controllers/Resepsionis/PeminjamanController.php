<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Mobil;

class PeminjamanController extends Controller
{
    /**
     * Tampilkan daftar peminjaman.
     */
    public function index()
    {
        $peminjaman = Peminjaman::with(['user', 'mobil'])->latest()->paginate(10);

        return view('resepsionis.peminjaman.index', compact('peminjaman'));
    }

    /**
     * Form tambah peminjaman.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        $mobils = Mobil::orderBy('merek')->get();

        return view('resepsionis.peminjaman.create', compact('users', 'mobils'));
    }

    /**
     * Simpan data peminjaman baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'           => 'required|exists:users,id',
            'mobil_id'          => 'required|exists:mobils,id',
            'tanggal_sewa'      => 'required|date',
            'jam_sewa'          => 'required',
            'tanggal_kembali'   => 'required|date|after_or_equal:tanggal_sewa',
            'add_on_sopir'      => 'required|boolean',
            'total_harga'       => 'required|numeric|min:0',
            'dp_dibayarkan'     => 'nullable|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:255',
            'tipe_pembayaran'   => 'required|in:dp,lunas',
            'status'            => 'required|in:menunggu pembayaran,pembayaran dp,sudah dibayar lunas,berlangsung,selesai,dibatalkan',
        ]);

        // Hitung otomatis sisa bayar dan total dibayarkan
        $validated['sisa_bayar'] = $validated['total_harga'] - ($validated['dp_dibayarkan'] ?? 0);
        $validated['total_dibayarkan'] = $validated['dp_dibayarkan'] ?? 0;

        Peminjaman::create($validated);

        return redirect()->route('resepsionis.peminjaman.index')
                         ->with('success', 'Data peminjaman berhasil ditambahkan.');
    }

    /**
     * Form edit peminjaman.
     */
    public function edit($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $users = User::orderBy('name')->get();
        $mobils = Mobil::orderBy('merek')->get();

        return view('resepsionis.peminjaman.edit', compact('peminjaman', 'users', 'mobils'));
    }

    /**
     * Update data peminjaman.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id'           => 'required|exists:users,id',
            'mobil_id'          => 'required|exists:mobils,id',
            'tanggal_sewa'      => 'required|date',
            'jam_sewa'          => 'required',
            'tanggal_kembali'   => 'required|date|after_or_equal:tanggal_sewa',
            'add_on_sopir'      => 'required|boolean',
            'total_harga'       => 'required|numeric|min:0',
            'dp_dibayarkan'     => 'nullable|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:255',
            'tipe_pembayaran'   => 'required|in:dp,lunas',
            'status'            => 'required|in:menunggu pembayaran,pembayaran dp,sudah dibayar lunas,berlangsung,selesai,dibatalkan',
        ]);

        $peminjaman = Peminjaman::findOrFail($id);
        $validated['sisa_bayar'] = $validated['total_harga'] - ($validated['dp_dibayarkan'] ?? 0);
        $validated['total_dibayarkan'] = $validated['dp_dibayarkan'] ?? 0;

        $peminjaman->update($validated);

        return redirect()->route('resepsionis.peminjaman.index')
                         ->with('success', 'Data peminjaman berhasil diperbarui.');
    }

    /**
     * Hapus data peminjaman.
     */
    public function destroy($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();

        return redirect()->route('resepsionis.peminjaman.index')
                         ->with('success', 'Data peminjaman berhasil dihapus.');
    }
  
public function show($id)
{
    // Ambil data peminjaman beserta relasi pentingnya
    $peminjaman = Peminjaman::with(['user', 'mobil'])
        ->findOrFail($id);

    // Kirim ke view
    return view('resepsionis.peminjaman.show', compact('peminjaman'));
}

}
