<?php
// ANGGOTA 3 Alip  |  Portal penghuni: hanya melihat data MILIKNYA sendiri
namespace App\Http\Controllers;

use App\Models\Kontrak;
use App\Models\Tagihan;

class PortalController extends Controller
{
    public function index()
    {
        $penghuni = auth()->user()->penghuni;

        $kontrak = Kontrak::with('kamar.tipe')
            ->where('penghuni_id', $penghuni->id)
            ->where('status', 'aktif')
            ->first();

        $tagihan = Tagihan::whereHas('kontrak', fn ($q) => $q->where('penghuni_id', $penghuni->id))
            ->orderByDesc('jatuh_tempo')
            ->get()
            ->each(fn ($t) => $t->hitungUlang()); // denda terbaru (tidak disimpan)

        return view('portal.index', compact('penghuni', 'kontrak', 'tagihan'));
    }
}