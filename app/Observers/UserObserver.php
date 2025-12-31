<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Sopir;
use App\Models\Resepsionis;
use App\Models\Staff;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        switch ($user->role_id) {
            case 2: // Pelanggan
                Pelanggan::firstOrCreate(
                    ['user_id' => $user->id],
                    ['nama' => $user->name, 'status' => 'aktif']
                );
                break;

            case 3: // Resepsionis
                Resepsionis::firstOrCreate(
                    ['user_id' => $user->id],
                    ['nama' => $user->name, 'status' => 'tidak aktif']
                );
                break;

            case 4: // Sopir
                Sopir::firstOrCreate(
                    ['user_id' => $user->id],
                    ['nama' => $user->name, 'status' => 'Tidak Tersedia']
                );
                break;

            case 5: // Staff
                Staff::firstOrCreate(
                    ['user_id' => $user->id],
                    ['nama' => $user->name, 'status' => 'tidak aktif']
                );
                break;

            default:
                // role_id 1 (admin) atau role lain tidak di-handle
                break;
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged('role_id')) {
            // Hapus data lama
            Pelanggan::where('user_id', $user->id)->delete();
            Sopir::where('user_id', $user->id)->delete();
            Resepsionis::where('user_id', $user->id)->delete();
            Staff::where('user_id', $user->id)->delete();

            // Buat data baru sesuai role baru
            $this->created($user);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        Pelanggan::where('user_id', $user->id)->delete();
        Sopir::where('user_id', $user->id)->delete();
        Resepsionis::where('user_id', $user->id)->delete();
        Staff::where('user_id', $user->id)->delete();
    }
}
