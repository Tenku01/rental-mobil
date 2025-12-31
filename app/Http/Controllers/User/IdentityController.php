<?php

// app/Http/Controllers/User/IdentityController.php

// app/Http/Controllers/User/IdentityController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserIdentification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class IdentityController extends Controller
{
    /**
     * Menampilkan form upload KTP dan SIM
     */
   public function showUploadForm()
    {
        // Ambil data status_approval dan tanggal_upload berdasarkan user yang sedang login
        $userIdentification = UserIdentification::where('user_id', Auth::id())->first();

        // Konversi tanggal_upload menjadi objek Carbon untuk memudahkan format
        if ($userIdentification && $userIdentification->tanggal_upload) {
            $userIdentification->tanggal_upload = \Carbon\Carbon::parse($userIdentification->tanggal_upload);
        }

        return view('user.identitas.upload_identity', compact('userIdentification'));
    }
    /**
     * Menyimpan file KTP dan SIM
     */
    public function store(Request $request)
    {
        // Validasi file yang di-upload
        $request->validate([
            'ktp' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            'sim' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // Menyimpan file KTP
        $ktpPath = $request->file('ktp')->store('ktp', 'public');

        // Menyimpan file SIM
        $simPath = $request->file('sim')->store('sim', 'public');

        // Menyimpan data KTP dan SIM ke dalam tabel user_identifications
        UserIdentification::create([
            'user_id' => Auth::id(), // Mendapatkan ID user yang sedang login
            'ktp' => $ktpPath,
            'sim' => $simPath,
            'tanggal_upload' => now(),  // Menyimpan tanggal saat ini tanpa waktu
            'status_approval' => 'menunggu',  // Menetapkan status_approval ke 'menunggu'
        ]);

        // Redirect ke halaman dashboard atau halaman lain setelah berhasil upload
        return redirect()->route('dashboard')->with('success', 'KTP dan SIM berhasil diupload.');
    }
 public function edit($id)
    {
        $userIdentification = UserIdentification::findOrFail($id);
        return view('user.identitas.edit_identity', compact('userIdentification'));
    }

    // Mengupdate data KTP dan SIM
    public function update(Request $request, $id)
    {
        $request->validate([
            'ktp' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'sim' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $userIdentification = UserIdentification::findOrFail($id);

        // Jika ada file KTP baru, hapus file lama dan simpan file baru
        if ($request->hasFile('ktp')) {
            Storage::delete('public/' . $userIdentification->ktp);
            $userIdentification->ktp = $request->file('ktp')->store('ktp', 'public');
        }

        // Jika ada file SIM baru, hapus file lama dan simpan file baru
        if ($request->hasFile('sim')) {
            Storage::delete('public/' . $userIdentification->sim);
            $userIdentification->sim = $request->file('sim')->store('sim', 'public');
        }

        $userIdentification->save();

        return redirect()->route('dashboard')->with('success', 'Data identitas berhasil diperbarui.');
    }

    // Menghapus data KTP dan SIM
    public function destroy($id)
    {
        $userIdentification = UserIdentification::findOrFail($id);

        // Hapus file yang di-upload
        Storage::delete('public/' . $userIdentification->ktp);
        Storage::delete('public/' . $userIdentification->sim);

        // Hapus data dari database
        $userIdentification->delete();

        return redirect()->route('dashboard')->with('success', 'Data identitas berhasil dihapus.');
    }
}
