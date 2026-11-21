<?php
// ANGGOTA 3 Alip  |  LOGIKA INTI: hitung listrik, denda, total
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';
    protected $fillable = [
        'kontrak_id', 'jenis', 'periode', 'jatuh_tempo', 'nominal',
        'meter_awal', 'meter_akhir', 'tarif_listrik', 'biaya_listrik',
        'denda', 'total', 'jumlah_dibayar', 'status', 'tanggal_bayar', 'catatan',
    ];
    protected $casts = ['jatuh_tempo' => 'date', 'tanggal_bayar' => 'date'];

    public function kontrak()
    {
        return $this->belongsTo(Kontrak::class);
    }

    public function getSisaAttribute()
    {
        return max(0, $this->total - $this->jumlah_dibayar);
    }

    public function getHariTelatAttribute()
    {
        $selisih = $this->jatuh_tempo->copy()->startOfDay()->diffInDays(Carbon::today(), false);
        return max(0, (int) floor($selisih));
    }

    // total = sewa + (pemakaian x tarif) + denda. Tagihan LUNAS tidak dihitung ulang.
    public function hitungUlang(): self
    {
        if ($this->status === 'lunas') {
            return $this;
        }

        if ($this->jenis === 'sewa' && $this->meter_akhir !== null) {
            $this->biaya_listrik = max(0, $this->meter_akhir - $this->meter_awal) * $this->tarif_listrik;
        }

        $this->denda = 0;
        if ($this->jenis === 'sewa' && $this->hari_telat > 0) {
            $perHari = Pengaturan::ambil('denda_per_hari', 5000);
            $batas   = Pengaturan::ambil('batas_denda', 50000);
            $this->denda = min($this->hari_telat * $perHari, $batas);
        }

        $this->total = $this->nominal + $this->biaya_listrik + $this->denda;
        $this->perbaruiStatus();
        return $this;
    }

    public function perbaruiStatus(): self
    {
        if ($this->jumlah_dibayar >= $this->total && $this->total > 0) {
            $this->status = 'lunas';
        } elseif ($this->jumlah_dibayar > 0) {
            $this->status = 'sebagian';
        } else {
            $this->status = 'belum_bayar';
        }
        return $this;
    }
}