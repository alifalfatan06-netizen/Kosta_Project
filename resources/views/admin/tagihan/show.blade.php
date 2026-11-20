{{-- ANGGOTA 3: rincian + bayar tunai --}}
@extends('layouts.app')
@section('judul', 'Detail Tagihan')

@section('isi')
<div class="d-flex justify-content-between mb-3">
    <h3>Tagihan {{ ucfirst($tagihan->jenis) }} {{ $tagihan->periode }}</h3>
    <a href="{{ route('admin.tagihan.index') }}" class="btn btn-outline-secondary">Kembali</a>
</div>

<div class="row g-3">
    <div class="col-md-7"><div class="card"><div class="card-header">Rincian</div>
        <table class="table mb-0">
            <tr><th width="200">Penghuni</th><td>{{ $tagihan->kontrak->penghuni->nama }} (Kamar {{ $tagihan->kontrak->kamar->nomor_kamar }})</td></tr>
            <tr><th>Jatuh Tempo</th><td>{{ $tagihan->jatuh_tempo->format('d M Y') }}
                @if ($tagihan->hari_telat > 0 && $tagihan->status !== 'lunas')
                    <span class="badge bg-danger">telat {{ $tagihan->hari_telat }} hari</span>
                @endif
            </td></tr>
            <tr><th>{{ $tagihan->jenis === 'sewa' ? 'Biaya Sewa' : 'Deposit' }}</th><td>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td></tr>
            @if ($tagihan->jenis === 'sewa')
                <tr><th>Meter</th><td>{{ $tagihan->meter_awal }} &rarr; {{ $tagihan->meter_akhir ?? '(belum diisi)' }} kWh</td></tr>
                <tr><th>Biaya Listrik</th><td>Rp {{ number_format($tagihan->biaya_listrik, 0, ',', '.') }}</td></tr>
                <tr><th>Denda</th><td>Rp {{ number_format($tagihan->denda, 0, ',', '.') }}</td></tr>
            @endif
            <tr class="table-light"><th>TOTAL</th><th>Rp {{ number_format($tagihan->total, 0, ',', '.') }}</th></tr>
            <tr><th>Sudah Dibayar</th><td>Rp {{ number_format($tagihan->jumlah_dibayar, 0, ',', '.') }}</td></tr>
            <tr><th>Sisa</th><td>Rp {{ number_format($tagihan->sisa, 0, ',', '.') }}</td></tr>
            <tr><th>Status</th><td>{{ str_replace('_', ' ', $tagihan->status) }}</td></tr>
        </table>
    </div></div>

    <div class="col-md-5">
        @if ($tagihan->status !== 'lunas')
            <div class="card"><div class="card-header">Catat Pembayaran Tunai</div><div class="card-body">
                <form action="{{ route('admin.tagihan.bayar', $tagihan) }}" method="POST">
                    @csrf
                    <label class="form-label">Jumlah dibayar (Rp)</label>
                    <input type="number" name="jumlah" value="{{ old('jumlah', $tagihan->sisa) }}"
                           class="form-control mb-2 @error('jumlah') is-invalid @enderror" required>
                    @error('jumlah') <div class="invalid-feedback d-block mb-2">{{ $message }}</div> @enderror
                    <button class="btn btn-success w-100">Simpan Pembayaran</button>
                </form>
            </div></div>
        @else
            <div class="alert alert-success">Tagihan ini sudah LUNAS.</div>
        @endif
    </div>
</div>
@endsection