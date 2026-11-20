{{-- ANGGOTA 1 (fondasi): layout utama --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'KOSTA') - Manajemen Kos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('katalog') }}">KOSTA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                @auth
                    @if (auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        {{-- Menu Anggota 1 --}}
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.kamar.index') }}">Kamar</a></li>
                        {{-- Menu Anggota 2 --}}
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.kontrak.index') }}">Kontrak</a></li>
                        {{-- Menu Anggota 3 --}}
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.tagihan.index') }}">Tagihan</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('portal.index') }}">Portal Saya</a></li>
                    @endif
                @endauth
            </ul>
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item"><span class="navbar-text me-3">{{ auth()->user()->name }}</span></li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-light btn-sm">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Login</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container pb-5">
    @if (session('sukses'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if (session('galat'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('galat') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @yield('isi')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>