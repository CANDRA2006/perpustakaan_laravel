<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

/*
| Public Routes
*/
Route::get('/', function () {
    return view('home');
})->name('home');

/*
| Authentication Routes
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
| Protected Routes (Require Authentication)
*/

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Global Search (lintas modul: Buku, Anggota, Transaksi)
    Route::get('/search', [GlobalSearchController::class, 'index'])->name('search');

    // Buku - rute spesifik harus didefinisikan sebelum resource route
    Route::get('/buku/search', [BukuController::class, 'search'])->name('buku.search');
    Route::get('/buku/kategori/{kategori}', [BukuController::class, 'filterKategori'])
        ->name('buku.kategori');
    Route::post('/buku/bulk-delete', [BukuController::class, 'bulkDelete'])
        ->name('buku.bulk-delete');
    Route::get('/buku/bulk-delete', function () {
        return redirect()->route('buku.index')
            ->with('error', 'Metode request tidak valid untuk bulk delete.');
    });
    Route::get('/buku/export', [BukuController::class, 'export'])->name('buku.export');
    Route::resource('buku', BukuController::class);

    // Anggota
    Route::get('/anggota/search', [AnggotaController::class, 'search'])->name('anggota.search');
    Route::get('/anggota/export', [AnggotaController::class, 'export'])->name('anggota.export');
    Route::resource('anggota', AnggotaController::class);

    // Kategori (CRUD penuh, terhubung relasi belongsTo/hasMany dengan Buku)
    Route::resource('kategori', KategoriController::class);

    // Transaksi
    Route::get('/transaksi/laporan', [TransaksiController::class, 'laporan'])->name('transaksi.laporan');
    Route::get('/transaksi/laporan/pdf', [TransaksiController::class, 'laporanPdf'])->name('transaksi.laporan.pdf');
    Route::post('/transaksi/{transaksi}/kembalikan', [TransaksiController::class, 'kembalikan'])->name('transaksi.kembalikan');
    Route::resource('transaksi', TransaksiController::class)->except(['edit', 'update', 'destroy']);
});
