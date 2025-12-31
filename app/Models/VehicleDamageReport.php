<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDamageReport extends Model
{
    use HasFactory;
    protected $table = 'vehicle_damage_reports';
    protected $primaryKey = 'kode_laporan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'mobil_id',
        'pengembalian_kode',
        'damage_description',
        'damage_cost'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_laporan)) {
                $model->kode_laporan = 'DMG' .
                    str_pad($model->mobil_id, 4, '0', STR_PAD_LEFT) .
                    str_pad($model->peminjaman_id, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /** 🔹 Relasi ke mobil */
    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }

    /** 🔹 Relasi ke peminjaman */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    /** 🔹 Relasi ke pengembalian */
    public function pengembalian()
    {
        return $this->belongsTo(Pengembalian::class, 'peminjaman_id', 'peminjaman_id');
    }
}
