{{-- ANGGOTA 2 Althaf: CRUD Kontrak - daftar --}}
@extends('layouts.app')
@section('judul', 'Data Kontrak')

@section('isi')
<div class="d-flex justify-content-between mb-3">
    <h3>Data Kontrak</h3>
    <a href="{{ route('admin.kontrak.create') }}" class="btn btn-dark">+ Buat Kontrak</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4"><input name="cari" value="{{ request('cari') }}" class="form-control" placeholder="Cari nama penghuni"></div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
        </select>
    </div>
    <div class="col-md-3"><button class="btn btn-outline-dark">Cari</button></div>
</form>

<div class="card"><div class="table-responsive">
<table class="table table-hover mb-0 align-middle">
    <thead class="table-light">
        <tr><th>Penghuni</th><th>Kamar</th><th>Mulai</th><th>Selesai</th><th>Harga</th><th>Status</th><th width="190">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($kontrak as $k)
        <tr>
            <td>{{ $k->penghuni->nama }}</td>
            <td>{{ $k->kamar->nomor_kamar }}</td>
            <td>{{ $k->tanggal_mulai->format('d M Y') }}</td>
            <td>{{ $k->tanggal_selesai->format('d M Y') }}</td>
            <td>Rp {{ number_format($k->harga_sewa, 0, ',', '.') }}</td>
            <td><span class="badge bg-{{ $k->status === 'aktif' ? 'success' : 'secondary' }}">{{ $k->status }}</span></td>
            <td>
                <a href="{{ route('admin.kontrak.show', $k) }}" class="btn btn-sm btn-outline-info">Detail</a>
                @if ($k->status === 'aktif')
                    <a href="{{ route('admin.kontrak.edit', $k) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                @endif
                <form action="{{ route('admin.kontrak.destroy', $k) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Hapus kontrak? (hanya bisa jika belum ada pembayaran)')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada kontrak.</td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $kontrak->links() }}</div>
@endsection