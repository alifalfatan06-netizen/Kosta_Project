{{-- ANGGOTA 3 Alip: portal penghuni --}}
@extends('layouts.app')
@section('judul', 'Portal Penghuni')

@section('isi')
<h3 class="mb-3">Halo, {{ $penghuni->nama }}</h3>

@if ($kontrak)
    <div class="card mb-4"><div class="card-body">
        <h5>Kamar {{ $kontrak->kamar->nomor_kamar }} ({{ $kontrak->kamar->tipe->nama }})</h5>
        <div>Kontrak: {{ $kontrak->tanggal_mulai->format('d M Y') }} s/d {{ $kontrak->tanggal_selesai->format('d M Y') }}</div>
        <div>Sewa: Rp {{ number_format($kontrak->harga_sewa, 0, ',', '.') }} / bulan</div>
    </div></div>
@else
    <div class="alert alert-info">Anda tidak punya kontrak aktif.</div>
@endif

<h5>Tagihan Saya</h5>
<div class="card"><div class="table-responsive">
<table class="table mb-0">
    <thead class="table-light">
        <tr><th>Jenis</th><th>Periode</th><th>Jatuh Tempo</th><th>Listrik</th><th>Denda</th><th>Total</th><th>Dibayar</th><th>Status</th></tr>
    </thead>
    <tbody>
    @forelse ($tagihan as $t)
        <tr>
            <td>{{ $t->jenis }}</td>
            <td>{{ $t->periode ?: '-' }}</td>
            <td>{{ $t->jatuh_tempo->format('d M Y') }}</td>
            <td>Rp {{ number_format($t->biaya_listrik, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($t->denda, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($t->jumlah_dibayar, 0, ',', '.') }}</td>
            <td>
                @php
                    $warna = ['belum_bayar' => 'danger', 'sebagian' => 'warning', 'lunas' => 'success'][$t->status];
                @endphp
                <span class="badge bg-{{ $warna }}">{{ str_replace('_', ' ', $t->status) }}</span>
            </td>
        </tr>
    @empty
        <tr><td colspan="8" class="text-center text-muted py-3">Belum ada tagihan.</td></tr>
    @endforelse
    </tbody>
</table></div></div>
@endsection