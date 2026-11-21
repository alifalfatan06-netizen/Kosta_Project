{{-- ANGGOTA 3 Alip: tagihan sewa manual --}}
@extends('layouts.app')
@section('judul', 'Tagihan Manual')

@section('isi')
<h3 class="mb-3">Buat Tagihan Sewa Manual</h3>
<div class="card"><div class="card-body">
<form action="{{ route('admin.tagihan.store') }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Kontrak Aktif</label>
            <select name="kontrak_id" class="form-select @error('kontrak_id') is-invalid @enderror" required>
                <option value="">-- pilih --</option>
                @foreach ($kontrak as $k)
                    <option value="{{ $k->id }}" @selected(old('kontrak_id') == $k->id)>Kamar {{ $k->kamar->nomor_kamar }} - {{ $k->penghuni->nama }}</option>
                @endforeach
            </select>
            @error('kontrak_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Periode (bulan)</label>
            <input type="month" name="periode" value="{{ old('periode', date('Y-m')) }}"
                   class="form-control @error('periode') is-invalid @enderror" required>
            @error('periode') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Jatuh Tempo</label>
            <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo', date('Y-m-10')) }}"
                   class="form-control @error('jatuh_tempo') is-invalid @enderror" required>
        </div>
        <div class="col-12">
            <label class="form-label">Catatan</label>
            <input name="catatan" value="{{ old('catatan') }}" class="form-control">
        </div>
    </div>
    <div class="mt-4">
        <button class="btn btn-dark">Simpan</button>
        <a href="{{ route('admin.tagihan.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
</div></div>
@endsection