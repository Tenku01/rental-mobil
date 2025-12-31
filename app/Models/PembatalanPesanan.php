<?php

// app/Models/PembatalanPesanan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembatalanPesanan extends Model
{

    protected $table = 'pembatalan_pesanan';
    protected $fillable = [
        'peminjaman_id','user_id','cancelled_by','alasan',
        'refund_status','cancelled_at',
        'approval_status','persentase_refund','jumlah_refund','id_transaksi_refund'
    ];

    public function peminjaman(){ return $this->belongsTo(Peminjaman::class); }
    public function user(){ return $this->belongsTo(User::class); }
    protected $casts = [
    'cancelled_at' => 'datetime',
];

    
}
