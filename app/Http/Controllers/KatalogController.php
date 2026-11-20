<?php
// ANGGOTA 1  |  Katalog publik + filter tipe, harga maksimal, lantai
namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\TipeKamar;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Kamar::with('tipe')->tersedia();

        if ($request->filled('tipe')) {
            $query->where('tipe_kamar_id', $request->tipe);
        }
        if ($request->filled('harga_max')) {
            $query->where('harga_sewa', '<=', $request->harga_max);
        }
        if ($request->filled('lantai')) {
            $query->where('lantai', $request->lantai);
        }

        return view('katalog.index', [
            'kamar' => $query->orderBy('nomor_kamar')->get(),
            'tipe'  => TipeKamar::all(),
        ]);
    }
}