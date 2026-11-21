<?php
// ANGGOTA 2 Althaf
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kontrak extends Model
{
    protected $table = 'kontrak';
    protected $fillable = ['kamar_id', 'penghuni_id', 'tanggal_mulai', 'tanggal_selesai', 'durasi_bulan', 'harga_sewa', 'deposit', 'status', 'catatan'];
    protected $casts = ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date'];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }

    public function tagihan() // relasi ke milik Alip
    {
        return $this->hasMany(Tagihan::class);
    }
}