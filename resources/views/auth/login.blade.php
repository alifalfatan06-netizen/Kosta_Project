{{-- ANGGOTA 1 Mahendra --}}
@extends('layouts.app')
@section('judul', 'Login')

@section('isi')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm"><div class="card-body p-4">
            <h4 class="mb-3">Login KOSTA</h4>
            <form action="{{ route('login.proses') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror" required autofocus>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="ingat" value="1" class="form-check-input" id="ingat">
                    <label for="ingat" class="form-check-label">Ingat saya</label>
                </div>
                <button class="btn btn-dark w-100">Masuk</button>
            </form>
            <hr>
            <small class="text-muted">Admin: admin@kosta.test / password<br>Penghuni: budi@kosta.test / password</small>
        </div></div>
    </div>
</div>
@endsection