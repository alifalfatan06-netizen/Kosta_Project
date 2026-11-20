<?php
// Aturan: tiap anggota HANYA menambah route di blok miliknya (biar Git tidak bentrok)
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KamarController;
use App\Http\Controllers\Admin\KontrakController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;

// ---------- [ANGGOTA 1] Halaman publik & login ----------
Route::get('/', [KatalogController::class, 'index'])->name('katalog');
Route::get('/login', [AuthController::class, 'form'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'proses'])->name('login.proses')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ---------- Area ADMIN ----------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // [ANGGOTA 1] Dashboard + CRUD Kamar
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('kamar', KamarController::class)->parameters(['kamar' => 'kamar']);

    // [ANGGOTA 2] CRUD Kontrak + akhiri kontrak
    Route::post('kontrak/{kontrak}/akhiri', [KontrakController::class, 'akhiri'])->name('kontrak.akhiri');
    Route::resource('kontrak', KontrakController::class)->parameters(['kontrak' => 'kontrak']);

    // [ANGGOTA 3] CRUD Tagihan + bayar tunai + generate bulanan
    Route::post('generate-tagihan', [TagihanController::class, 'generate'])->name('tagihan.generate');
    Route::post('tagihan/{tagihan}/bayar', [TagihanController::class, 'bayar'])->name('tagihan.bayar');
    Route::resource('tagihan', TagihanController::class)->parameters(['tagihan' => 'tagihan']);
});

// ---------- [ANGGOTA 3] Portal PENGHUNI ----------
Route::middleware(['auth', 'role:penghuni'])->group(function () {
    Route::get('/portal', [PortalController::class, 'index'])->name('portal.index');
});