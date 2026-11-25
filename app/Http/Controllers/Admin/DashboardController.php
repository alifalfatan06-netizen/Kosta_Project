<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Kontrak;
use App\Models\Tagihan;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKamar = Kamar::where('status', '!=', 'nonaktif')->count();
        $terisi     = Kamar::where('status', 'terisi')->count();

        return view('admin.dashboard', [
            'totalKamar'   => $totalKamar,
            'terisi'       => $terisi,
            'tersedia'     => Kamar::where('status', 'tersedia')->count(),
            'okupansi'     => $totalKamar > 0 ? round($terisi / $totalKamar * 100) : 0,
            'kontrakAktif' => Kontrak::where('status', 'aktif')->count(),
            'tunggakan'    => Tagihan::where('status', '!=', 'lunas')->get()->sum('sisa'),
            // pemasukan hanya dari sewa (deposit = titipan, tidak dihitung)
            'pemasukan'    => Tagihan::where('jenis', 'sewa')
                                ->whereMonth('tanggal_bayar', now()->month)
                                ->whereYear('tanggal_bayar', now()->year)
                                ->sum('jumlah_dibayar'),
            'akanBerakhir' => Kontrak::with(['kamar', 'penghuni'])
                                ->where('status', 'aktif')
                                ->whereBetween('tanggal_selesai', [now(), now()->addDays(30)])
                                ->get(),
        ]);
    }
}