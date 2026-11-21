<?php
// ANGGOTA 1 Mahendra |  CRUD KAMAR (+ upload foto)
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\TipeKamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KamarController extends Controller
{
    // READ: daftar + cari + filter status
    public function index(Request $request)
    {
        $query = Kamar::with('tipe');

        if ($request->filled('cari')) {
            $query->where('nomor_kamar', 'like', '%' . $request->cari . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kamar = $query->orderBy('nomor_kamar')->paginate(10)->withQueryString();
        return view('admin.kamar.index', compact('kamar'));
    }

    // CREATE: form
    public function create()
    {
        return view('admin.kamar.create', ['kamar' => new Kamar(), 'tipe' => TipeKamar::all()]);
    }

    // CREATE: simpan
    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kamar', 'public');
        }

        Kamar::create($data);
        return redirect()->route('admin.kamar.index')->with('sukses', 'Kamar berhasil ditambahkan.');
    }

    // READ: detail
    public function show(Kamar $kamar)
    {
        $kamar->load(['tipe', 'kontrak.penghuni']);
        return view('admin.kamar.show', compact('kamar'));
    }

    // UPDATE: form
    public function edit(Kamar $kamar)
    {
        return view('admin.kamar.edit', ['kamar' => $kamar, 'tipe' => TipeKamar::all()]);
    }

    // UPDATE: simpan
    public function update(Request $request, Kamar $kamar)
    {
        $data = $this->validasi($request, $kamar->id);

        if ($request->hasFile('foto')) {
            if ($kamar->foto) {
                Storage::disk('public')->delete($kamar->foto); // hapus foto lama
            }
            $data['foto'] = $request->file('foto')->store('kamar', 'public');
        }

        $kamar->update($data);
        return redirect()->route('admin.kamar.index')->with('sukses', 'Kamar berhasil diperbarui.');
    }

    // DELETE: kalau punya riwayat kontrak -> hanya dinonaktifkan (aturan bisnis)
    public function destroy(Kamar $kamar)
    {
        if ($kamar->kontrak()->exists()) {
            $kamar->update(['status' => 'nonaktif']);
            return back()->with('sukses', 'Kamar punya riwayat kontrak, jadi dinonaktifkan (tidak dihapus).');
        }

        if ($kamar->foto) {
            Storage::disk('public')->delete($kamar->foto);
        }
        $kamar->delete();
        return back()->with('sukses', 'Kamar berhasil dihapus.');
    }

    private function validasi(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'tipe_kamar_id' => 'required|exists:tipe_kamar,id',
            'nomor_kamar'   => ['required', 'string', 'max:10', Rule::unique('kamar', 'nomor_kamar')->ignore($ignoreId)],
            'lantai'        => 'required|integer|min:1|max:50',
            'harga_sewa'    => 'required|integer|min:0',
            'deposit'       => 'required|integer|min:0',
            'fasilitas'     => 'nullable|string|max:500',
            'status'        => 'required|in:tersedia,dibooking,terisi,nonaktif',
            'foto'          => 'nullable|image|max:2048',
        ]);
    }
}