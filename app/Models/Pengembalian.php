<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';
    protected $primaryKey = 'kode_pengembalian';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_pengembalian',
        'peminjaman_id',
        'tanggal_pengembalian',
        'status', // 'menunggu pengecekan', 'selesai pengecekan'
    ];

    // Kolom virtual untuk JSON/Array response
    protected $appends = ['total_outstanding_fine'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Logika generate kode unik: PBL0000001
            if (empty($model->kode_pengembalian)) {
                // Ambil kode terakhir untuk increment
                $latest = static::orderBy('created_at', 'desc')->first();
                // Jika ada, ambil angkanya, jika tidak mulai dari 1
                $number = $latest ? (int) substr($latest->kode_pengembalian, 3) + 1 : 1;
                $model->kode_pengembalian = 'PBL' . str_pad($number, 7, '0', STR_PAD_LEFT);
            }
            
            // Default status
            if (empty($model->status)) {
                $model->status = 'menunggu pengecekan';
            }
        });
    }

    /** 🔹 Relasi ke peminjaman */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }
    /** * 🔹 Relasi ke denda (fines)
     * UPDATE: Karena di tabel fines (sesuai schema) tidak ada kolom pengembalian_kode,
     * kita relasikan via peminjaman_id (karena 1 peminjaman = 1 pengembalian).
     */
    public function fines()
    {
        return $this->hasMany(Fine::class, 'peminjaman_id', 'peminjaman_id');
    }

    /** 🔹 Relasi ke laporan kerusakan */
    public function damageReports()
    {
        return $this->hasMany(VehicleDamageReport::class, 'pengembalian_kode', 'kode_pengembalian');
    }

    /** 🔹 Relasi ke inspeksi kendaraan */
    public function inspections()
    {
        return $this->hasMany(VehicleInspection::class, 'pengembalian_kode', 'kode_pengembalian');
    }

    /**
     * ACCESSOR: Menghitung total denda yang BELUM DIBAYAR.
     * UPDATE: Menggunakan kolom 'total_denda' sesuai struktur tabel.
     */
    public function getTotalOutstandingFineAttribute()
    {
        return $this->fines()
                    ->where('status', 'belum dibayar') 
                    ->sum('total_denda');
    }
    
    /**
     * Helper untuk mendapatkan total denda keseluruhan (termasuk yang sudah dibayar)
     */
    public function getTotalDendaAttribute()
    {
        return $this->fines()->sum('total_denda');
    }
}