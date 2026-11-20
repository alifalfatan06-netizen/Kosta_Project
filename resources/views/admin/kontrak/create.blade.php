{{-- ANGGOTA 2: form buat kontrak + data penghuni --}}
@extends('layouts.app')
@section('judul', 'Buat Kontrak')

@section('isi')
<h3 class="mb-3">Buat Kontrak Baru</h3>
<div class="card"><div class="card-body">
<form action="{{ route('admin.kontrak.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <h5>Kamar & Periode</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label">Kamar (hanya yang tersedia)</label>
            <select name="kamar_id" class="form-select @error('kamar_id') is-invalid @enderror" required>
                <option value="">-- pilih kamar --</option>
                @foreach ($kamarTersedia as $k)
                    <option value="{{ $k->id }}" @selected(old('kamar_id') == $k->id)>
                        {{ $k->nomor_kamar }} - Rp {{ number_format($k->harga_sewa, 0, ',', '.') }}/bln
                        (deposit Rp {{ number_format($k->deposit, 0, ',', '.') }})
                    </option>
                @endforeach
            </select>
            @error('kamar_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                   class="form-control @error('tanggal_mulai') is-invalid @enderror" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Durasi (bulan)</label>
            <input type="number" name="durasi_bulan" value="{{ old('durasi_bulan', 6) }}" min="1"
                   class="form-control @error('durasi_bulan') is-invalid @enderror" required>
        </div>
    </div>

    <h5>Data Penghuni <small class="text-muted fs-6">(akun login dibuat otomatis)</small></h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nama Lengkap</label>
            <input name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror" required>
            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Email (untuk login)</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">No. HP / WhatsApp</label>
            <input name="no_hp" value="{{ old('no_hp') }}" class="form-control @error('no_hp') is-invalid @enderror" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">No. KTP</label>
            <input name="no_ktp" value="{{ old('no_ktp') }}" class="form-control @error('no_ktp') is-invalid @enderror" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Foto KTP (maks 2 MB)</label>
            <input type="file" name="foto_ktp" accept="image/*" class="form-control @error('foto_ktp') is-invalid @enderror">
            @error('foto_ktp') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
            <label class="form-label">Alamat Asal</label>
            <textarea name="alamat" rows="2" class="form-control">{{ old('alamat') }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Catatan Kontrak</label>
            <textarea name="catatan" rows="2" class="form-control">{{ old('catatan') }}</textarea>
        </div>
    </div>
    <div class="mt-4">
        <button class="btn btn-dark">Simpan Kontrak</button>
        <a href="{{ route('admin.kontrak.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
</div></div>
@endsection