<?php
// app/Models/VehicleInspection.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleInspection extends Model
{
    use HasFactory;
 protected $table = 'vehicle_inspections';
    protected $fillable = [
        'mobil_id',
        'staff_id',
        'pengembalian_kode',
        'condition',
        'keterangan'
    ];
    /**
     * Relasi ke tabel mobil
     */
    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }

    /**
     * Relasi ke tabel user (staff)
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
