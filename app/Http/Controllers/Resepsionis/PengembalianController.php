<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    /**
     * Menampilkan daftar riwayat pengembalian.
     */
    public function index()
    {
        $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.mobil'])
            ->orderBy('tanggal_pengembalian', 'desc')
            ->get();

        return view('resepsionis.pengembalian.index', compact('pengembalian'));
    }

    /**
     * Form untuk memproses pengembalian baru.
     */
    public function create()
    {
        // Hanya tampilkan peminjaman yang sedang 'berlangsung'
        $peminjamans = Peminjaman::where('status', 'berlangsung')
            ->with(['user', 'mobil'])
            ->get();

        return view('resepsionis.pengembalian.create', compact('peminjamans'));
    }

    /**
     * Simpan data pengembalian dan update status peminjaman otomatis.
     */
    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'tanggal_pengembalian' => 'required|date',
            'status' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Ambil data peminjaman
                $peminjaman = Peminjaman::findOrFail($request->peminjaman_id);

                // 2. Buat record pengembalian
                // kode_pengembalian biasanya dihandle oleh Trigger DB, 
                // jika tidak, kita bisa buat manual di sini:
                $kode = 'PBL' . str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT);

                Pengembalian::create([
                    'kode_pengembalian' => $kode,
                    'peminjaman_id' => $request->peminjaman_id,
                    'tanggal_pengembalian' => $request->tanggal_pengembalian,
                    'status' => $request->status,
                ]);

                // 3. Update status peminjaman menjadi 'selesai'
                $peminjaman->update([
                    'status' => 'selesai'
                ]);

                // 4. Update status mobil menjadi 'tersedia'
                Mobil::where('id', $peminjaman->mobil_id)->update([
                    'status' => 'tersedia'
                ]);
            });

            return redirect()->route('resepsionis.pengembalian.index')
                ->with('success', 'Pengembalian berhasil diproses. Status peminjaman dan mobil telah diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail pengembalian.
     */
    public function show($id)
    {
        $data = Pengembalian::with(['peminjaman.user', 'peminjaman.mobil'])->findOrFail($id);
        return view('resepsionis.pengembalian.show', compact('data'));
    }

    /**
     * Form edit data pengembalian (Jika ada kesalahan input tanggal/status).
     */
    public function edit($id)
    {
        $pengembalian = Pengembalian::findOrFail($id);
        return view('resepsionis.pengembalian.edit', compact('pengembalian'));
    }

    /**
     * Update data pengembalian.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_pengembalian' => 'required|date',
            'status' => 'required|string',
        ]);

        $pengembalian = Pengembalian::findOrFail($id);
        $pengembalian->update([
            'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'status' => $request->status,
        ]);

        return redirect()->route('resepsionis.pengembalian.index')
            ->with('success', 'Data pengembalian berhasil diperbarui.');
    }

    /**
     * Hapus data riwayat pengembalian.
     */
    public function destroy($id)
    {
        $pengembalian = Pengembalian::findOrFail($id);
        $pengembalian->delete();

        return redirect()->route('resepsionis.pengembalian.index')
            ->with('success', 'Riwayat pengembalian berhasil dihapus.');
    }
}