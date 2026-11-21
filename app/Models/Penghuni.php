<?php
// ANGGOTA 2 Althaf
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    protected $table = 'penghuni';
    protected $fillable = ['user_id', 'nama', 'no_hp', 'no_ktp', 'alamat', 'foto_ktp', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kontrak()
    {
        return $this->hasMany(Kontrak::class);
    }

    // 0812xxx -> 62812xxx (untuk link wa.me)
    public function getWaAttribute()
    {
        $nomor = preg_replace('/\D/', '', $this->no_hp);
        return str_starts_with($nomor, '0') ? '62' . substr($nomor, 1) : $nomor;
    }
}