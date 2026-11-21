{{-- ANGGOTA 2 Althaf: edit / perpanjang kontrak --}}
@extends('layouts.app')
@section('judul', 'Edit Kontrak')

@section('isi')
<h3 class="mb-3">Edit Kontrak - Kamar {{ $kontrak->kamar->nomor_kamar }}</h3>
<div class="card"><div class="card-body">
<form action="{{ route('admin.kontrak.update', $kontrak) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nama Lengkap</label>
            <input name="nama" value="{{ old('nama', $kontrak->penghuni->nama) }}" class="form-control @error('nama') is-invalid @enderror" required>
            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">No. HP</label>
            <input name="no_hp" value="{{ old('no_hp', $kontrak->penghuni->no_hp) }}" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">No. KTP</label>
            <input name="no_ktp" value="{{ old('no_ktp', $kontrak->penghuni->no_ktp) }}" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" rows="2" class="form-control">{{ old('alamat', $kontrak->penghuni->alamat) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Ganti Foto KTP (opsional)</label>
            <input type="file" name="foto_ktp" accept="image/*" class="form-control @error('foto_ktp') is-invalid @enderror">
            @error('foto_ktp') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Durasi (bulan) - naikkan untuk MEMPERPANJANG</label>
            <input type="number" name="durasi_bulan" min="1" value="{{ old('durasi_bulan', $kontrak->durasi_bulan) }}"
                   class="form-control @error('durasi_bulan') is-invalid @enderror" required>
            <small class="text-muted">Mulai {{ $kontrak->tanggal_mulai->format('d M Y') }}, selesai saat ini {{ $kontrak->tanggal_selesai->format('d M Y') }}</small>
        </div>
        <div class="col-12">
            <label class="form-label">Catatan</label>
            <textarea name="catatan" rows="2" class="form-control">{{ old('catatan', $kontrak->catatan) }}</textarea>
        </div>
    </div>
    <div class="mt-4">
        <button class="btn btn-dark">Simpan Perubahan</button>
        <a href="{{ route('admin.kontrak.show', $kontrak) }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
</div></div>
@endsection