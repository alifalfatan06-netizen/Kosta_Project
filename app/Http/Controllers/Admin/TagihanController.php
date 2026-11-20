<?php
// ANGGOTA 3  |  CRUD TAGIHAN  |  total = sewa + (pemakaian x tarif) + denda
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kontrak;
use App\Models\Pengaturan;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    // READ: daftar + filter
    public function index(Request $request)
    {
        // DENDA OTOMATIS: hitung ulang tagihan sewa yang belum lunas & lewat jatuh tempo
        Tagihan::where('jenis', 'sewa')->where('status', '!=', 'lunas')
            ->whereDate('jatuh_tempo', '<', today())
            ->get()
            ->each(fn ($t) => $t->hitungUlang()->save());

        $query = Tagihan::with(['kontrak.penghuni', 'kontrak.kamar']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }

        $tagihan = $query->latest()->paginate(10)->withQueryString();
        return view('admin.tagihan.index', compact('tagihan'));
    }

    // CREATE: form tagihan sewa manual
    public function create()
    {
        return view('admin.tagihan.create', [
            'kontrak' => Kontrak::with(['kamar', 'penghuni'])->where('status', 'aktif')->get(),
        ]);
    }

    // CREATE: simpan
    public function store(Request $request)
    {
        $data = $request->validate([
            'kontrak_id'  => 'required|exists:kontrak,id',
            'periode'     => 'required|date_format:Y-m',
            'jatuh_tempo' => 'required|date',
            'catatan'     => 'nullable|string|max:255',
        ]);

        $kontrak = Kontrak::findOrFail($data['kontrak_id']);

        if ($this->buatTagihanSewa($kontrak, $data['periode'], $data['jatuh_tempo'], $data['catatan'] ?? null) === null) {
            return back()->withInput()->withErrors(['periode' => 'Tagihan sewa periode ini sudah ada.']);
        }

        return redirect()->route('admin.tagihan.index')->with('sukses', 'Tagihan berhasil dibuat.');
    }

    // READ: detail + form bayar
    public function show(Tagihan $tagihan)
    {
        $tagihan->hitungUlang()->save();
        $tagihan->load(['kontrak.penghuni', 'kontrak.kamar']);
        return view('admin.tagihan.show', compact('tagihan'));
    }

    // UPDATE: form input meter
    public function edit(Tagihan $tagihan)
    {
        if ($tagihan->status === 'lunas') {
            return back()->with('galat', 'Tagihan lunas tidak bisa diubah.');
        }
        $tagihan->load(['kontrak.penghuni', 'kontrak.kamar']);
        return view('admin.tagihan.edit', compact('tagihan'));
    }

    // UPDATE: simpan -> total DIHITUNG ULANG otomatis
    public function update(Request $request, Tagihan $tagihan)
    {
        if ($tagihan->status === 'lunas') {
            return back()->with('galat', 'Tagihan lunas tidak bisa diubah.');
        }

        $rules = ['jatuh_tempo' => 'required|date', 'catatan' => 'nullable|string|max:255'];
        if ($tagihan->jenis === 'sewa') {
            $rules['meter_awal']  = 'required|integer|min:0';
            $rules['meter_akhir'] = 'required|integer|gte:meter_awal';
        }
        $data = $request->validate($rules);

        $tagihan->fill($data);
        $tagihan->hitungUlang()->save();

        return redirect()->route('admin.tagihan.show', $tagihan)->with('sukses', 'Tagihan diperbarui & total dihitung ulang.');
    }

    // DELETE: hanya kalau belum ada pembayaran
    public function destroy(Tagihan $tagihan)
    {
        if ($tagihan->jumlah_dibayar > 0) {
            return back()->with('galat', 'Tagihan sudah ada pembayaran, tidak bisa dihapus.');
        }
        $tagihan->delete();
        return redirect()->route('admin.tagihan.index')->with('sukses', 'Tagihan dihapus.');
    }

    // AKSI: catat pembayaran tunai
    public function bayar(Request $request, Tagihan $tagihan)
    {
        $tagihan->hitungUlang();
        $data = $request->validate([
            'jumlah' => 'required|integer|min:1|max:' . max(1, $tagihan->sisa),
        ]);

        $tagihan->jumlah_dibayar += $data['jumlah'];
        $tagihan->perbaruiStatus();
        $tagihan->tanggal_bayar = today();
        $tagihan->save();

        return back()->with('sukses', 'Pembayaran Rp ' . number_format($data['jumlah'], 0, ',', '.') . ' dicatat. Status: ' . $tagihan->status);
    }

    // AKSI: generate tagihan sewa bulan ini untuk semua kontrak aktif
    public function generate()
    {
        $periode = now()->format('Y-m');
        $dibuat = 0;

        foreach (Kontrak::with('kamar')->where('status', 'aktif')->get() as $kontrak) {
            $jatuhTempo = Carbon::createFromFormat('Y-m-d', $periode . '-10');
            if ($this->buatTagihanSewa($kontrak, $periode, $jatuhTempo->toDateString()) !== null) {
                $dibuat++;
            }
        }

        return back()->with('sukses', "$dibuat tagihan sewa periode $periode berhasil dibuat.");
    }

    // helper: dipakai store() & generate(). Return null kalau periode itu sudah ada
    private function buatTagihanSewa(Kontrak $kontrak, string $periode, string $jatuhTempo, ?string $catatan = null): ?Tagihan
    {
        if (Tagihan::where('kontrak_id', $kontrak->id)->where('jenis', 'sewa')->where('periode', $periode)->exists()) {
            return null;
        }

        // meter awal = meter akhir bulan sebelumnya
        $sebelumnya = Tagihan::where('kontrak_id', $kontrak->id)->where('jenis', 'sewa')
            ->whereNotNull('meter_akhir')->orderByDesc('periode')->first();

        $tagihan = new Tagihan([
            'kontrak_id'    => $kontrak->id,
            'jenis'         => 'sewa',
            'periode'       => $periode,
            'jatuh_tempo'   => $jatuhTempo,
            'nominal'       => $kontrak->harga_sewa,
            'meter_awal'    => $sebelumnya?->meter_akhir ?? 0,
            'tarif_listrik' => Pengaturan::ambil('tarif_listrik', 1500),
            'catatan'       => $catatan,
        ]);
        $tagihan->hitungUlang()->save();

        return $tagihan;
    }
}