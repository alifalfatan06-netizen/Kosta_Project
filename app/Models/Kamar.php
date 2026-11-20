<?php
// ANGGOTA 1
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $table = 'kamar';
    protected $fillable = ['tipe_kamar_id', 'nomor_kamar', 'lantai', 'harga_sewa', 'deposit', 'fasilitas', 'foto', 'status'];

    public function tipe()
    {
        return $this->belongsTo(TipeKamar::class, 'tipe_kamar_id');
    }

    public function kontrak()
    {
        return $this->hasMany(Kontrak::class);
    }

    // Kamar::tersedia()->get()  (dipakai katalog & form kontrak Anggota 2)
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    // $kamar->foto_url
    public function getFotoUrlAttribute()
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : 'https://placehold.co/600x400?text=Kamar+' . urlencode($this->nomor_kamar);
    }
}