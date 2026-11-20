{{-- ANGGOTA 1: katalog publik + filter --}}
@extends('layouts.app')
@section('judul', 'Kamar Tersedia')

@section('isi')
<h3 class="mb-3">Kamar Tersedia</h3>

<form method="GET" class="row g-2 mb-4">
    <div class="col-md-3">
        <select name="tipe" class="form-select">
            <option value="">Semua tipe</option>
            @foreach ($tipe as $t)
                <option value="{{ $t->id }}" @selected(request('tipe') == $t->id)>{{ $t->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3"><input type="number" name="harga_max" value="{{ request('harga_max') }}" class="form-control" placeholder="Harga maksimal"></div>
    <div class="col-md-2"><input type="number" name="lantai" value="{{ request('lantai') }}" class="form-control" placeholder="Lantai"></div>
    <div class="col-md-4">
        <button class="btn btn-dark">Filter</button>
        <a href="{{ route('katalog') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="row g-3">
    @forelse ($kamar as $k)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <img src="{{ $k->foto_url }}" class="card-img-top" style="height:200px;object-fit:cover">
                <div class="card-body">
                    <h5>Kamar {{ $k->nomor_kamar }} <span class="badge bg-secondary">{{ $k->tipe->nama }}</span></h5>
                    <p class="mb-1">Lantai {{ $k->lantai }}</p>
                    <p class="mb-1 fw-bold">Rp {{ number_format($k->harga_sewa, 0, ',', '.') }} / bulan</p>
                    <p class="mb-1 text-muted">Deposit Rp {{ number_format($k->deposit, 0, ',', '.') }}</p>
                    <small>{{ $k->fasilitas }}</small>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-info">Tidak ada kamar yang cocok.</div></div>
    @endforelse
</div>
@endsection