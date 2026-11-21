<?php
// ANGGOTA 1 Mahendra  |  Pemakaian: Pengaturan::ambil('tarif_listrik', 1500)
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';
    protected $fillable = ['kunci', 'nilai', 'keterangan'];

    public static function ambil(string $kunci, $default = 0)
    {
        $row = static::where('kunci', $kunci)->first();
        return $row ? (int) $row->nilai : $default;
    }
}