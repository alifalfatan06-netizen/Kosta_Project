<?php
// ANGGOTA 1
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeKamar extends Model
{
    protected $table = 'tipe_kamar';
    protected $fillable = ['nama', 'deskripsi'];

    public function kamar()
    {
        return $this->hasMany(Kamar::class, 'tipe_kamar_id');
    }
}