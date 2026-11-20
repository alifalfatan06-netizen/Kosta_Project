{{-- ANGGOTA 1 --}}
@extends('layouts.app')
@section('judul', 'Edit Kamar')

@section('isi')
<h3 class="mb-3">Edit Kamar {{ $kamar->nomor_kamar }}</h3>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.kamar.update', $kamar) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.kamar._form')
    </form>
</div></div>
@endsection