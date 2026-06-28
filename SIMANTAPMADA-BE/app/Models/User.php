<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'nama_lengkap',
        'username',
        'password',
        'role',
        'foto',
    ];

    protected $hidden = [
        'password',
    ];

    // Setiap kali 'foto' diakses, ubah dari path relatif jadi URL lengkap.
    // Kalau belum ada foto, kembalikan null (frontend akan pakai ikon default).
    public function getFotoAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }
}
