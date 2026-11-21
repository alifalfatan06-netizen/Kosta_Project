<?php
// ANGGOTA 2  |  CRUD KONTRAK (+ penghuni & akun otomatis)
// store() otomatis: buat user -> penghuni -> kontrak -> kamar "terisi" -> tagihan deposit & sewa pertama
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Kontrak;
use App\Models\Pengaturan;
use App\Models\Penghuni;
use App\Models\Tagihan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KontrakController extends Controller
{
    // READ: daftar + filter
    public function index(Request $request)
    {
        $query = Kontrak::with(['kamar', 'penghuni']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->whereHas('penghuni', fn ($q) => $q->where('nama', 'like', "%$cari%"));
        }

        $kontrak = $query->latest()->paginate(10)->withQueryString();
        return view('admin.kontrak.index', compact('kontrak'));
    }

    // CREATE: form (hanya kamar tersedia)
    public function create()
    {
        return view('admin.kontrak.create', [
            'kamarTersedia' => Kamar::tersedia()->orderBy('nomor_kamar')->get(),
        ]);
    }

    // CREATE: simpan (dibungkus transaction: gagal satu langkah = batal semua)
    public function store(Request $request)
    {
        $data = $request->validate([
            'kamar_id'      => 'required|exists:kamar,id',
            'nama'          => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'no_hp'         => 'required|string|max:20',
            'no_ktp'        => 'required|string|max:20',
            'alamat'        => 'nullable|string|max:500',
            'foto_ktp'      => 'nullable|image|max:2048',
            'tanggal_mulai' => 'required|date',
            'durasi_bulan'  => 'required|integer|min:1|max:60',
            'catatan'       => 'nullable|string|max:500',
        ]);
        $durasiBulan = (int) $data['durasi_bulan'];

        $kamar = Kamar::findOrFail($data['kamar_id']);

        // ATURAN BISNIS: satu kamar satu kontrak aktif
        if ($kamar->status !== 'tersedia' || $kamar->kontrak()->where('status', 'aktif')->exists()) {
            return back()->withInput()->withErrors(['kamar_id' => 'Kamar tidak tersedia / sudah punya kontrak aktif.']);
        }

        $passwordAwal = Str::random(8);

        $kontrak = DB::transaction(function () use ($request, $data, $kamar, $passwordAwal, $durasiBulan) {
            // 1) akun login
            $user = User::create([
                'name' => $data['nama'], 'email' => $data['email'],
                'password' => $passwordAwal, 'role' => 'penghuni',
            ]);

            // 2) profil penghuni
            $fotoKtp = $request->hasFile('foto_ktp') ? $request->file('foto_ktp')->store('ktp', 'public') : null;
            $penghuni = Penghuni::create([
                'user_id' => $user->id, 'nama' => $data['nama'], 'no_hp' => $data['no_hp'],
                'no_ktp' => $data['no_ktp'], 'alamat' => $data['alamat'] ?? null,
                'foto_ktp' => $fotoKtp, 'status' => 'aktif',
            ]);

            // 3) kontrak
            $mulai = Carbon::parse($data['tanggal_mulai']);
            $kontrak = Kontrak::create([
                'kamar_id' => $kamar->id, 'penghuni_id' => $penghuni->id,
                'tanggal_mulai' => $mulai,
                'tanggal_selesai' => $mulai->copy()->addMonthsNoOverflow($durasiBulan),
                'durasi_bulan' => $durasiBulan,
                'harga_sewa' => $kamar->harga_sewa, 'deposit' => $kamar->deposit,
                'status' => 'aktif', 'catatan' => $data['catatan'] ?? null,
            ]);

            // 4) kamar jadi terisi
            $kamar->update(['status' => 'terisi']);

            // 5) tagihan deposit + sewa pertama (tabel milik Anggota 3)
            Tagihan::create([
                'kontrak_id' => $kontrak->id, 'jenis' => 'deposit', 'jatuh_tempo' => $mulai,
                'nominal' => $kontrak->deposit, 'total' => $kontrak->deposit, 'status' => 'belum_bayar',
            ]);
            $sewa = new Tagihan([
                'kontrak_id' => $kontrak->id, 'jenis' => 'sewa', 'periode' => $mulai->format('Y-m'),
                'jatuh_tempo' => $mulai, 'nominal' => $kontrak->harga_sewa, 'meter_awal' => 0,
                'tarif_listrik' => Pengaturan::ambil('tarif_listrik', 1500),
            ]);
            $sewa->hitungUlang()->save();

            return $kontrak;
        });

        return redirect()->route('admin.kontrak.show', $kontrak)
            ->with('sukses', 'Kontrak berhasil dibuat. Akun penghuni dibuat otomatis.')
            ->with('password_awal', $passwordAwal);
    }

    // READ: detail
    public function show(Kontrak $kontrak)
    {
        $kontrak->load(['kamar.tipe', 'penghuni.user', 'tagihan']);
        return view('admin.kontrak.show', compact('kontrak'));
    }

    // UPDATE: form
    public function edit(Kontrak $kontrak)
    {
        $kontrak->load(['kamar', 'penghuni']);
        return view('admin.kontrak.edit', compact('kontrak'));
    }

    // UPDATE: simpan (naikkan durasi = memperpanjang)
    public function update(Request $request, Kontrak $kontrak)
    {
        $data = $request->validate([
            'nama'         => 'required|string|max:100',
            'no_hp'        => 'required|string|max:20',
            'no_ktp'       => 'required|string|max:20',
            'alamat'       => 'nullable|string|max:500',
            'foto_ktp'     => 'nullable|image|max:2048',
            'durasi_bulan' => 'required|integer|min:1|max:60',
            'catatan'      => 'nullable|string|max:500',
        ]);
        $durasiBulan = (int) $data['durasi_bulan'];

        DB::transaction(function () use ($request, $data, $kontrak, $durasiBulan) {
            $penghuni = $kontrak->penghuni;

            if ($request->hasFile('foto_ktp')) {
                if ($penghuni->foto_ktp) {
                    Storage::disk('public')->delete($penghuni->foto_ktp);
                }
                $penghuni->foto_ktp = $request->file('foto_ktp')->store('ktp', 'public');
            }

            $penghuni->update([
                'nama' => $data['nama'], 'no_hp' => $data['no_hp'],
                'no_ktp' => $data['no_ktp'], 'alamat' => $data['alamat'] ?? null,
            ]);
            $penghuni->user->update(['name' => $data['nama']]);

            $kontrak->update([
                'durasi_bulan' => $durasiBulan,
                'tanggal_selesai' => $kontrak->tanggal_mulai->copy()->addMonthsNoOverflow($durasiBulan),
                'catatan' => $data['catatan'] ?? null,
            ]);
        });

        return redirect()->route('admin.kontrak.show', $kontrak)->with('sukses', 'Kontrak berhasil diperbarui.');
    }

    // DELETE: hanya kalau belum ada pembayaran (salah input). Selain itu pakai "Akhiri Kontrak"
    public function destroy(Kontrak $kontrak)
    {
        if ($kontrak->tagihan()->where('jumlah_dibayar', '>', 0)->exists()) {
            return back()->with('galat', 'Kontrak sudah punya pembayaran, tidak bisa dihapus. Gunakan "Akhiri Kontrak".');
        }

        DB::transaction(function () use ($kontrak) {
            $penghuni = $kontrak->penghuni;
            $kontrak->tagihan()->delete();
            $kontrak->kamar->update(['status' => 'tersedia']);
            $kontrak->delete();

            if (!$penghuni->kontrak()->exists()) {
                if ($penghuni->foto_ktp) {
                    Storage::disk('public')->delete($penghuni->foto_ktp);
                }
                $user = $penghuni->user;
                $penghuni->delete();
                $user->delete();
            }
        });

        return redirect()->route('admin.kontrak.index')->with('sukses', 'Kontrak berhasil dihapus.');
    }

    // AKSI: akhiri kontrak (checkout sederhana)
    public function akhiri(Kontrak $kontrak)
    {
        if ($kontrak->status !== 'aktif') {
            return back()->with('galat', 'Kontrak sudah berakhir.');
        }

        // tidak boleh keluar kalau masih menunggak sewa (deposit = titipan, tidak dihitung)
        if ($kontrak->tagihan()->where('jenis', 'sewa')->where('status', '!=', 'lunas')->exists()) {
            return back()->with('galat', 'Masih ada tagihan sewa yang belum lunas.');
        }

        DB::transaction(function () use ($kontrak) {
            $kontrak->update(['status' => 'selesai']);
            $kontrak->kamar->update(['status' => 'tersedia']);
            $kontrak->penghuni->update(['status' => 'mantan']);
            $kontrak->penghuni->user->update(['is_active' => false]); // dinonaktifkan, tidak dihapus
        });

        return redirect()->route('admin.kontrak.show', $kontrak)->with('sukses', 'Kontrak diakhiri. Kamar kembali tersedia.');
    }
}
