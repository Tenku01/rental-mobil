<?php
// app/Models/UserIdentification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserIdentification extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id', 'ktp', 'sim',
         'tanggal_upload',
        'status_approval',
    ];

    /**
     * Relasi ke tabel User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getKtpThumbnail()
{
    if ($this->ktp) {
        $url = asset('storage/' . $this->ktp);
        return "<img src=\"{$url}\" style=\"height:80px; object-fit:cover; border-radius:4px;\" alt=\"KTP\">";
    }
    return '<span class="text-muted">—</span>';
}

public function getSimThumbnail()
{
    if ($this->sim) {
        $url = asset('storage/' . $this->sim);
        return "<img src=\"{$url}\" style=\"height:80px; object-fit:cover; border-radius:4px;\" alt=\"SIM\">";
    }
    return '<span class=\"text-muted\">—</span>';
}
}


