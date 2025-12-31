<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverLogbook extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'driver_logbooks';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'peminjaman_id',
        'tanggal_aktivitas',
        'deskripsi_aktivitas',
        'status_log',
        'foto_bukti',
    ];

    // Casting untuk mengkonversi tipe data dari database ke tipe PHP yang sesuai
    protected $casts = [
        'tanggal_aktivitas' => 'date',
        'waktu_log' => 'datetime',
    ];

    /**
     * Dapatkan peminjaman yang terkait dengan logbook ini (Relasi One-to-Many terbalik).
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }
}