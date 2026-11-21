{{-- ANGGOTA 3 Alip: CRUD Tagihan - daftar + generate + WA --}}
@extends('layouts.app')
@section('judul', 'Data Tagihan')

@section('isi')
<div class="d-flex justify-content-between mb-3">
    <h3>Data Tagihan</h3>
    <div>
        <form action="{{ route('admin.tagihan.generate') }}" method="POST" class="d-inline"
              onsubmit="return confirm('Buat tagihan sewa bulan ini untuk semua kontrak aktif?')">
            @csrf
            <button class="btn btn-outline-dark">Generate Tagihan Bulan Ini</button>
        </form>
        <a href="{{ route('admin.tagihan.create') }}" class="btn btn-dark">+ Tagihan Manual</a>
    </div>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            @foreach (['belum_bayar' => 'Belum bayar', 'sebagian' => 'Sebagian', 'lunas' => 'Lunas'] as $v => $l)
                <option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3"><input type="month" name="periode" value="{{ request('periode') }}" class="form-control"></div>
    <div class="col-md-3"><button class="btn btn-outline-dark">Filter</button></div>
</form>

<div class="card"><div class="table-responsive">
<table class="table table-hover mb-0 align-middle">
    <thead class="table-light">
        <tr><th>Penghuni</th><th>Kamar</th><th>Jenis</th><th>Periode</th><th>Jatuh Tempo</th><th>Total</th><th>Sisa</th><th>Status</th><th width="230">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($tagihan as $t)
        <tr>
            <td>{{ $t->kontrak->penghuni->nama }}</td>
            <td>{{ $t->kontrak->kamar->nomor_kamar }}</td>
            <td>{{ $t->jenis }}</td>
            <td>{{ $t->periode ?: '-' }}</td>
            <td>{{ $t->jatuh_tempo->format('d M Y') }}</td>
            <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($t->sisa, 0, ',', '.') }}</td>
            <td>
                @php
                    $warna = ['belum_bayar' => 'danger', 'sebagian' => 'warning', 'lunas' => 'success'][$t->status];
                @endphp
                <span class="badge bg-{{ $warna }}">{{ str_replace('_', ' ', $t->status) }}</span>
            </td>
            <td>
                <a href="{{ route('admin.tagihan.show', $t) }}" class="btn btn-sm btn-outline-info">Detail</a>
                @if ($t->status !== 'lunas')
                    <a href="{{ route('admin.tagihan.edit', $t) }}" class="btn btn-sm btn-outline-warning">Meter</a>
                    {{-- pengingat WhatsApp lewat wa.me --}}
                    <a target="_blank" class="btn btn-sm btn-outline-success"
                       href="https://wa.me/{{ $t->kontrak->penghuni->wa }}?text={{ rawurlencode('Halo ' . $t->kontrak->penghuni->nama . ', pengingat tagihan kos kamar ' . $t->kontrak->kamar->nomor_kamar . ' sebesar Rp ' . number_format($t->sisa, 0, ',', '.') . ', jatuh tempo ' . $t->jatuh_tempo->format('d M Y') . '. Terima kasih.') }}">WA</a>
                @endif
                <form action="{{ route('admin.tagihan.destroy', $t) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Hapus tagihan ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="9" class="text-center text-muted py-4">Belum ada tagihan.</td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $tagihan->links() }}</div>
@endsection