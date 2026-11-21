<?php
// ANGGOTA 1 Mahendra (fondasi)
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['password' => 'hashed', 'is_active' => 'boolean'];

    public function penghuni()
    {
        return $this->hasOne(Penghuni::class);
    }
}