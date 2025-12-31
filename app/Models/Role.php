<?php

// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    use HasFactory;

    protected $fillable = ['role_name'];

    /**
     * Relasi ke tabel users
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
}
