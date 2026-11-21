{{-- ANGGOTA 1 Mahendra: CRUD Kamar - daftar --}}
@extends('layouts.app')
@section('judul', 'Data Kamar')

@section('isi')
<div class="d-flex justify-content-between mb-3">
    <h3>Data Kamar</h3>
    <a href="{{ route('admin.kamar.create') }}" class="btn btn-dark">+ Tambah Kamar</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4"><input name="cari" value="{{ request('cari') }}" class="form-control" placeholder="Cari nomor kamar"></div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            @foreach (['tersedia', 'dibooking', 'terisi', 'nonaktif'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3"><button class="btn btn-outline-dark">Cari</button></div>
</form>

<div class="card"><div class="table-responsive">
<table class="table table-hover mb-0 align-middle">
    <thead class="table-light">
        <tr><th>Foto</th><th>No</th><th>Tipe</th><th>Lantai</th><th>Harga</th><th>Deposit</th><th>Status</th><th width="190">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($kamar as $k)
        <tr>
            <td><img src="{{ $k->foto_url }}" width="70" class="rounded"></td>
            <td>{{ $k->nomor_kamar }}</td>
            <td>{{ $k->tipe->nama }}</td>
            <td>{{ $k->lantai }}</td>
            <td>Rp {{ number_format($k->harga_sewa, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($k->deposit, 0, ',', '.') }}</td>
            <td>
                @php
                    $warna = ['tersedia' => 'success', 'dibooking' => 'warning', 'terisi' => 'primary', 'nonaktif' => 'secondary'][$k->status];
                @endphp
                <span class="badge bg-{{ $warna }}">{{ $k->status }}</span>
            </td>
            <td>
                <a href="{{ route('admin.kamar.show', $k) }}" class="btn btn-sm btn-outline-info">Detail</a>
                <a href="{{ route('admin.kamar.edit', $k) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                <form action="{{ route('admin.kamar.destroy', $k) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Hapus kamar {{ $k->nomor_kamar }}?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data kamar.</td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $kamar->links() }}</div>
@endsection