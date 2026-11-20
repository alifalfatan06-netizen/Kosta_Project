<?php
// ANGGOTA 1 (fondasi)  |  Supaya semua anggota bisa kerja tanpa saling menunggu
namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\Kontrak;
use App\Models\Pengaturan;
use App\Models\Penghuni;
use App\Models\Tagihan;
use App\Models\TipeKamar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // admin
        User::create(['name' => 'Admin KOSTA', 'email' => 'admin@kosta.test', 'password' => 'password', 'role' => 'admin']);

        // pengaturan
        foreach ([
            ['tarif_listrik', 1500, 'Tarif listrik per kWh'],
            ['denda_per_hari', 5000, 'Denda per hari'],
            ['batas_denda', 50000, 'Batas maksimal denda'],
            ['lama_booking', 3, 'Lama booking (hari)'],
        ] as [$kunci, $nilai, $ket]) {
            Pengaturan::create(['kunci' => $kunci, 'nilai' => $nilai, 'keterangan' => $ket]);
        }

        // tipe kamar
        $standard = TipeKamar::create(['nama' => 'Standard', 'deskripsi' => 'Kamar mandi luar']);
        $deluxe   = TipeKamar::create(['nama' => 'Deluxe', 'deskripsi' => 'Kamar mandi dalam']);
        $vip      = TipeKamar::create(['nama' => 'VIP', 'deskripsi' => 'AC & water heater']);

        // kamar
        $k101 = Kamar::create(['tipe_kamar_id' => $standard->id, 'nomor_kamar' => '101', 'lantai' => 1,
            'harga_sewa' => 800000, 'deposit' => 800000, 'fasilitas' => 'Kasur, Lemari, Kipas', 'status' => 'terisi']);
        Kamar::create(['tipe_kamar_id' => $standard->id, 'nomor_kamar' => '102', 'lantai' => 1,
            'harga_sewa' => 800000, 'deposit' => 800000, 'fasilitas' => 'Kasur, Lemari, Kipas']);
        Kamar::create(['tipe_kamar_id' => $deluxe->id, 'nomor_kamar' => '201', 'lantai' => 2,
            'harga_sewa' => 1200000, 'deposit' => 1200000, 'fasilitas' => 'AC, Kasur, KM Dalam']);
        Kamar::create(['tipe_kamar_id' => $deluxe->id, 'nomor_kamar' => '202', 'lantai' => 2,
            'harga_sewa' => 1200000, 'deposit' => 1200000, 'fasilitas' => 'AC, Kasur, KM Dalam']);
        Kamar::create(['tipe_kamar_id' => $vip->id, 'nomor_kamar' => '301', 'lantai' => 3,
            'harga_sewa' => 1800000, 'deposit' => 1800000, 'fasilitas' => 'AC, Water Heater, TV']);

        // contoh penghuni + kontrak + tagihan (untuk Anggota 2 & 3)
        $userBudi = User::create(['name' => 'Budi Santoso', 'email' => 'budi@kosta.test', 'password' => 'password', 'role' => 'penghuni']);
        $budi = Penghuni::create([
            'user_id' => $userBudi->id, 'nama' => 'Budi Santoso', 'no_hp' => '081234567890',
            'no_ktp' => '3273010101010001', 'alamat' => 'Bandung', 'status' => 'aktif',
        ]);
        $mulai = Carbon::today()->subMonth()->startOfMonth();
        $kontrak = Kontrak::create([
            'kamar_id' => $k101->id, 'penghuni_id' => $budi->id,
            'tanggal_mulai' => $mulai, 'tanggal_selesai' => $mulai->copy()->addMonthsNoOverflow(6),
            'durasi_bulan' => 6, 'harga_sewa' => $k101->harga_sewa, 'deposit' => $k101->deposit, 'status' => 'aktif',
        ]);

        Tagihan::create([
            'kontrak_id' => $kontrak->id, 'jenis' => 'deposit', 'jatuh_tempo' => $mulai,
            'nominal' => $kontrak->deposit, 'total' => $kontrak->deposit,
            'jumlah_dibayar' => $kontrak->deposit, 'status' => 'lunas', 'tanggal_bayar' => $mulai,
        ]);

        // sewa bulan lalu, belum bayar & lewat jatuh tempo -> kena denda
        $sewa = new Tagihan([
            'kontrak_id' => $kontrak->id, 'jenis' => 'sewa', 'periode' => $mulai->format('Y-m'),
            'jatuh_tempo' => $mulai->copy()->addDays(9), 'nominal' => $kontrak->harga_sewa,
            'meter_awal' => 0, 'meter_akhir' => 40, 'tarif_listrik' => 1500,
        ]);
        $sewa->hitungUlang()->save();
    }
}