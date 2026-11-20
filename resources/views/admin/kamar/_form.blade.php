{{-- ANGGOTA 1: form kamar --}}
@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Nomor Kamar</label>
        <input name="nomor_kamar" value="{{ old('nomor_kamar', $kamar->nomor_kamar) }}"
               class="form-control @error('nomor_kamar') is-invalid @enderror" required>
        @error('nomor_kamar') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Tipe Kamar</label>
        <select name="tipe_kamar_id" class="form-select @error('tipe_kamar_id') is-invalid @enderror" required>
            <option value="">-- pilih --</option>
            @foreach ($tipe as $t)
                <option value="{{ $t->id }}" @selected(old('tipe_kamar_id', $kamar->tipe_kamar_id) == $t->id)>{{ $t->nama }}</option>
            @endforeach
        </select>
        @error('tipe_kamar_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Lantai</label>
        <input type="number" name="lantai" value="{{ old('lantai', $kamar->lantai ?? 1) }}"
               class="form-control @error('lantai') is-invalid @enderror" required>
        @error('lantai') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Harga Sewa / bulan (Rp)</label>
        <input type="number" name="harga_sewa" value="{{ old('harga_sewa', $kamar->harga_sewa) }}"
               class="form-control @error('harga_sewa') is-invalid @enderror" required>
        @error('harga_sewa') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Deposit (Rp)</label>
        <input type="number" name="deposit" value="{{ old('deposit', $kamar->deposit) }}"
               class="form-control @error('deposit') is-invalid @enderror" required>
        @error('deposit') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            @foreach (['tersedia', 'dibooking', 'terisi', 'nonaktif'] as $s)
                <option value="{{ $s }}" @selected(old('status', $kamar->status ?? 'tersedia') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Fasilitas</label>
        <textarea name="fasilitas" rows="2" class="form-control">{{ old('fasilitas', $kamar->fasilitas) }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Foto Kamar (maks 2 MB)</label>
        <input type="file" name="foto" accept="image/*" class="form-control @error('foto') is-invalid @enderror">
        @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @if ($kamar->foto) <img src="{{ $kamar->foto_url }}" width="150" class="mt-2 rounded"> @endif
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-dark">Simpan</button>
    <a href="{{ route('admin.kamar.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>