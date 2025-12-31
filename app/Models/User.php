<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

use App\Models\Role;
use App\Models\Pelanggan;
use App\Models\Sopir; // Pastikan ini diimpor
use App\Models\Resepsionis;
use App\Models\Staff;
use App\Models\Peminjaman;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    // 🔹 Relasi hasOne ke Staff
    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id');
    }

    // 🔹 PENTING: Relasi hasOne ke Sopir
    // Ini memungkinkan pemanggilan: Auth::user()->sopir
    public function sopir()
    {
        return $this->hasOne(Sopir::class, 'user_id');
    }

    public function pelanggan()
{
    return $this->hasOne(Pelanggan::class, 'user_id');
}

public function resepsionis()
{
    return $this->hasOne(Resepsionis::class, 'user_id');
}

    // 🔹 Relasi lainnya
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
      public function hasRole($roleName)
    {
        return $this->role && $this->role->role_name === $roleName;
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    // 🔹 Mutator password
    // public function setPasswordAttribute($value)
    // {
    //     if ($value) {
    //         $this->attributes['password'] = Hash::make($value);
    //     }
    // }

    // 🔹 Hook otomatis untuk create/update role table
    protected static function booted()
    {
        // Saat user baru dibuat
        static::created(function ($user) {
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
                    // Catatan: Pastikan nilai status 'Tidak Tersedia' sesuai dengan enum DB: 'tidak tersedia'
                    Sopir::firstOrCreate(
                        ['user_id' => $user->id],
                        ['nama' => $user->name, 'status' => 'tidak tersedia']
                    );
                    break;
                case 5: // Staff
                    Staff::firstOrCreate(
                        ['user_id' => $user->id],
                        ['nama' => $user->name, 'status' => 'tidak aktif']
                    );
                    break;
            }
        });

        // Saat user diupdate
        static::updated(function ($user) {
            if ($user->wasChanged('role_id')) {
                // Hapus data lama di tabel role
                Pelanggan::where('user_id', $user->id)->delete();
                Resepsionis::where('user_id', $user->id)->delete();
                Sopir::where('user_id', $user->id)->delete();
                Staff::where('user_id', $user->id)->delete();

                // Buat record baru sesuai role baru
                $user->refresh(); // pastikan data fresh
                static::created($user); // panggil kembali hook create
            }
        });

        // Saat user dihapus
        static::deleted(function ($user) {
            Pelanggan::where('user_id', $user->id)->delete();
            Resepsionis::where('user_id', $user->id)->delete();
            Sopir::where('user_id', $user->id)->delete();
            Staff::where('user_id', $user->id)->delete();
        });
    }
}