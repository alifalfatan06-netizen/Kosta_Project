{{-- ANGGOTA 1 --}}
@extends('layouts.app')
@section('judul', 'Tambah Kamar')

@section('isi')
<h3 class="mb-3">Tambah Kamar</h3>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.kamar.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.kamar._form')
    </form>
</div></div>
@endsection