{{-- ANGGOTA 3: input meter listrik --}}
@extends('layouts.app')
@section('judul', 'Input Meter')

@section('isi')
<h3 class="mb-3">Input Meter - Kamar {{ $tagihan->kontrak->kamar->nomor_kamar }} ({{ $tagihan->kontrak->penghuni->nama }})</h3>
<div class="card"><div class="card-body">
<form action="{{ route('admin.tagihan.update', $tagihan) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3">
        @if ($tagihan->jenis === 'sewa')
            <div class="col-md-4">
                <label class="form-label">Meter Awal (kWh)</label>
                <input type="number" name="meter_awal" value="{{ old('meter_awal', $tagihan->meter_awal) }}"
                       class="form-control @error('meter_awal') is-invalid @enderror" required>
                @error('meter_awal') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Meter Akhir (kWh)</label>
                <input type="number" name="meter_akhir" value="{{ old('meter_akhir', $tagihan->meter_akhir) }}"
                       class="form-control @error('meter_akhir') is-invalid @enderror" required>
                @error('meter_akhir') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Tarif Listrik</label>
                <input class="form-control" value="Rp {{ number_format($tagihan->tarif_listrik, 0, ',', '.') }} / kWh" disabled>
            </div>
        @endif
        <div class="col-md-4">
            <label class="form-label">Jatuh Tempo</label>
            <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo', $tagihan->jatuh_tempo->format('Y-m-d')) }}"
                   class="form-control @error('jatuh_tempo') is-invalid @enderror" required>
        </div>
        <div class="col-md-8">
            <label class="form-label">Catatan</label>
            <input name="catatan" value="{{ old('catatan', $tagihan->catatan) }}" class="form-control">
        </div>
    </div>
    <p class="text-muted mt-3 mb-0">Total dihitung ulang otomatis: sewa + (pemakaian x tarif) + denda.</p>
    <div class="mt-3">
        <button class="btn btn-dark">Simpan & Hitung Ulang</button>
        <a href="{{ route('admin.tagihan.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
</div></div>
@endsection