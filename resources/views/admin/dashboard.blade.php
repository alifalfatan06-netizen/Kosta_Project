{{-- ANGGOTA 1 Mahendra: dashboard --}}
@extends('layouts.app')
@section('judul', 'Dashboard')

@section('isi')
<h3 class="mb-3">Dashboard</h3>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card text-bg-primary"><div class="card-body">
        <div>Okupansi</div><div class="fs-3 fw-bold">{{ $okupansi }}%</div>
        <small>{{ $terisi }} terisi dari {{ $totalKamar }} kamar</small></div></div></div>
    <div class="col-md-3"><div class="card text-bg-success"><div class="card-body">
        <div>Kamar Tersedia</div><div class="fs-3 fw-bold">{{ $tersedia }}</div>
        <small>{{ $kontrakAktif }} kontrak aktif</small></div></div></div>
    <div class="col-md-3"><div class="card text-bg-info"><div class="card-body">
        <div>Pemasukan Bulan Ini</div>
        <div class="fs-5 fw-bold">Rp {{ number_format($pemasukan, 0, ',', '.') }}</div>
        <small>Deposit tidak dihitung</small></div></div></div>
    <div class="col-md-3"><div class="card text-bg-danger"><div class="card-body">
        <div>Total Tunggakan</div>
        <div class="fs-5 fw-bold">Rp {{ number_format($tunggakan, 0, ',', '.') }}</div></div></div></div>
</div>

<div class="card">
    <div class="card-header">Kontrak berakhir dalam 30 hari</div>
    <div class="card-body">
        @forelse ($akanBerakhir as $k)
            <div>Kamar {{ $k->kamar->nomor_kamar }} - {{ $k->penghuni->nama }} (berakhir {{ $k->tanggal_selesai->format('d M Y') }})</div>
        @empty
            <span class="text-muted">Tidak ada.</span>
        @endforelse
    </div>
</div>
@endsection