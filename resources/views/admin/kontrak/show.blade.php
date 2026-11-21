{{-- ANGGOTA 2 Althaf: detail kontrak + tagihannya --}}
@extends('layouts.app')
@section('judul', 'Detail Kontrak')

@section('isi')
{{-- password sementara akun penghuni, tampil SEKALI --}}
@if (session('password_awal'))
    <div class="alert alert-warning">
        <strong>Akun penghuni dibuat.</strong> Email: <code>{{ $kontrak->penghuni->user->email }}</code>,
        password sementara: <code>{{ session('password_awal') }}</code><br>
        <small>Catat dan berikan ke penghuni, password ini tidak ditampilkan lagi.</small>
    </div>
@endif

<div class="d-flex justify-content-between mb-3">
    <h3>Kontrak Kamar {{ $kontrak->kamar->nomor_kamar }}
        <span class="badge bg-{{ $kontrak->status === 'aktif' ? 'success' : 'secondary' }}">{{ $kontrak->status }}</span>
    </h3>
    <div>
        @if ($kontrak->status === 'aktif')
            <a href="{{ route('admin.kontrak.edit', $kontrak) }}" class="btn btn-outline-warning">Edit / Perpanjang</a>
            <form action="{{ route('admin.kontrak.akhiri', $kontrak) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Akhiri kontrak? Kamar jadi tersedia & akun penghuni dinonaktifkan.')">
                @csrf
                <button class="btn btn-danger">Akhiri Kontrak</button>
            </form>
        @endif
        <a href="{{ route('admin.kontrak.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6"><div class="card"><div class="card-header">Penghuni</div>
        <table class="table mb-0">
            <tr><th width="130">Nama</th><td>{{ $kontrak->penghuni->nama }}</td></tr>
            <tr><th>Email</th><td>{{ $kontrak->penghuni->user->email }}</td></tr>
            <tr><th>No. HP</th><td>{{ $kontrak->penghuni->no_hp }}</td></tr>
            <tr><th>No. KTP</th><td>{{ $kontrak->penghuni->no_ktp }}</td></tr>
            <tr><th>Alamat</th><td>{{ $kontrak->penghuni->alamat ?: '-' }}</td></tr>
            <tr><th>Status</th><td>{{ $kontrak->penghuni->status }}</td></tr>
            <tr><th>Foto KTP</th><td>
                @if ($kontrak->penghuni->foto_ktp)
                    <a href="{{ asset('storage/' . $kontrak->penghuni->foto_ktp) }}" target="_blank">Lihat foto</a>
                @else - @endif
            </td></tr>
        </table>
    </div></div>
    <div class="col-md-6"><div class="card"><div class="card-header">Kontrak</div>
        <table class="table mb-0">
            <tr><th width="130">Kamar</th><td>{{ $kontrak->kamar->nomor_kamar }} ({{ $kontrak->kamar->tipe->nama }})</td></tr>
            <tr><th>Mulai</th><td>{{ $kontrak->tanggal_mulai->format('d M Y') }}</td></tr>
            <tr><th>Selesai</th><td>{{ $kontrak->tanggal_selesai->format('d M Y') }}</td></tr>
            <tr><th>Durasi</th><td>{{ $kontrak->durasi_bulan }} bulan</td></tr>
            <tr><th>Harga Sewa</th><td>Rp {{ number_format($kontrak->harga_sewa, 0, ',', '.') }}</td></tr>
            <tr><th>Deposit</th><td>Rp {{ number_format($kontrak->deposit, 0, ',', '.') }}</td></tr>
            <tr><th>Catatan</th><td>{{ $kontrak->catatan ?: '-' }}</td></tr>
        </table>
    </div></div>
</div>

<h5 class="mt-4">Tagihan Kontrak Ini</h5>
<table class="table table-sm">
    <thead><tr><th>Jenis</th><th>Periode</th><th>Jatuh Tempo</th><th>Total</th><th>Status</th></tr></thead>
    <tbody>
    @forelse ($kontrak->tagihan as $t)
        <tr>
            <td>{{ $t->jenis }}</td>
            <td>{{ $t->periode ?: '-' }}</td>
            <td>{{ $t->jatuh_tempo->format('d M Y') }}</td>
            <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
            <td>{{ $t->status }}</td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-muted">Belum ada tagihan.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection