{{-- ANGGOTA 1: detail kamar + riwayat kontrak --}}
@extends('layouts.app')
@section('judul', 'Detail Kamar')

@section('isi')
<h3 class="mb-3">Kamar {{ $kamar->nomor_kamar }}</h3>
<div class="row g-3">
    <div class="col-md-5"><img src="{{ $kamar->foto_url }}" class="img-fluid rounded"></div>
    <div class="col-md-7">
        <table class="table">
            <tr><th width="150">Tipe</th><td>{{ $kamar->tipe->nama }}</td></tr>
            <tr><th>Lantai</th><td>{{ $kamar->lantai }}</td></tr>
            <tr><th>Harga Sewa</th><td>Rp {{ number_format($kamar->harga_sewa, 0, ',', '.') }}</td></tr>
            <tr><th>Deposit</th><td>Rp {{ number_format($kamar->deposit, 0, ',', '.') }}</td></tr>
            <tr><th>Fasilitas</th><td>{{ $kamar->fasilitas ?: '-' }}</td></tr>
            <tr><th>Status</th><td>{{ $kamar->status }}</td></tr>
        </table>
        <a href="{{ route('admin.kamar.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>

<h5 class="mt-4">Riwayat Kontrak</h5>
<table class="table table-sm">
    <thead><tr><th>Penghuni</th><th>Mulai</th><th>Selesai</th><th>Status</th></tr></thead>
    <tbody>
    @forelse ($kamar->kontrak as $k)
        <tr>
            <td>{{ $k->penghuni->nama }}</td>
            <td>{{ $k->tanggal_mulai->format('d M Y') }}</td>
            <td>{{ $k->tanggal_selesai->format('d M Y') }}</td>
            <td>{{ $k->status }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-muted">Belum pernah disewa.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection