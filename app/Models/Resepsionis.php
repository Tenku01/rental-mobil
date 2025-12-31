<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Resepsionis extends Model
{


    protected $table = 'resepsionis';

    protected $fillable = [
        'user_id',
        'nama',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
