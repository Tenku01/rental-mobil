<?php
// app/Models/PaymentTransaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

   protected $fillable = [
    'peminjaman_id', 'midtrans_transaction_id', 'status', 'amount', 'tipe_transaksi', 'midtrans_response','id_transaksi_awal'
];


    /**
     * Relasi ke tabel peminjaman
     */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }
}
