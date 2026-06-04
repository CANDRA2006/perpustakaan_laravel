<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerpustakaanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\AnggotaController;

// Home
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Perpustakaan
Route::get('/perpustakaan', [PerpustakaanController::class, 'index'])->name('perpustakaan.index');
Route::get('/perpustakaan/{id}', [PerpustakaanController::class, 'show'])->name('perpustakaan.show');
Route::get('/about', [PerpustakaanController::class, 'about'])->name('about');

// Buku — route khusus harus didaftarkan SEBELUM Route::resource
Route::get('/buku/search', [BukuController::class, 'search'])->name('buku.search');
Route::get('/buku/export', [BukuController::class, 'export'])->name('buku.export');
Route::post('/buku/bulk-delete', [BukuController::class, 'bulkDelete'])->name('buku.bulk-delete');
Route::get('/buku/kategori/{kategori}', [BukuController::class, 'kategori'])->name('buku.kategori');
Route::resource('buku', BukuController::class);

// Anggota
Route::resource('anggota', AnggotaController::class);

// Kategori
Route::resource('kategori', KategoriController::class);

// Testing Accessor & Scope
Route::get('/test-accessor-scope', function () {

    $dummyBuku = collect([
        (object)['id' => 1, 'judul' => 'Belajar Laravel 11',         'pengarang' => 'Budi Raharjo',  'tahun_terbit' => 2024, 'harga' => 125000, 'stok' => 0],
        (object)['id' => 2, 'judul' => 'PHP Modern',                 'pengarang' => 'Andi Nugroho',  'tahun_terbit' => 2024, 'harga' => 89000,  'stok' => 3],
        (object)['id' => 3, 'judul' => 'MySQL Lanjutan',             'pengarang' => 'Siti Aminah',   'tahun_terbit' => 2023, 'harga' => 75000,  'stok' => 10],
        (object)['id' => 4, 'judul' => 'JavaScript ES2023',          'pengarang' => 'Ahmad Wijaya',  'tahun_terbit' => 2023, 'harga' => 95000,  'stok' => 20],
        (object)['id' => 5, 'judul' => 'Clean Code Indonesia',       'pengarang' => 'Rudi Hermawan', 'tahun_terbit' => 2025, 'harga' => 115000, 'stok' => 4],
        (object)['id' => 6, 'judul' => 'Algoritma dan Struktur Data','pengarang' => 'Hasan Basri',   'tahun_terbit' => 2021, 'harga' => 60000,  'stok' => 18],
    ]);

    $getStatusStokBadge = function ($stok) {
        if ($stok === 0)  return '<span class="badge bg-danger">Habis</span>';
        if ($stok <= 5)   return '<span class="badge bg-warning text-dark">Menipis</span>';
        if ($stok <= 15)  return '<span class="badge bg-info text-dark">Sedang</span>';
        return '<span class="badge bg-success">Aman</span>';
    };

    $getTahunLabel   = fn($tahun) => $tahun >= 2024 ? 'Buku Baru' : 'Buku Lama';
    $bukuTerbaru     = $dummyBuku->filter(fn($b) => $b->tahun_terbit >= 2024)->values();
    $bukuStokMenipis = $dummyBuku->filter(fn($b) => $b->stok < 5)->values();
    $bukuHargaRange  = $dummyBuku->filter(fn($b) => $b->harga >= 50000 && $b->harga <= 100000)->values();

    $dummyAnggota = collect([
        (object)['id' => 1, 'nama' => 'Budi Santoso',   'status' => 'Aktif',    'tanggal_lahir' => '2005-03-15', 'jenis_kelamin' => 'L', 'created_at' => now()],
        (object)['id' => 2, 'nama' => 'Siti Nurhaliza', 'status' => 'Aktif',    'tanggal_lahir' => '1990-07-22', 'jenis_kelamin' => 'P', 'created_at' => now()],
        (object)['id' => 3, 'nama' => 'Ahmad Rahman',   'status' => 'Aktif',    'tanggal_lahir' => '1985-11-08', 'jenis_kelamin' => 'L', 'created_at' => now()->subMonth()],
        (object)['id' => 4, 'nama' => 'Dewi Lestari',   'status' => 'Nonaktif', 'tanggal_lahir' => '1968-05-30', 'jenis_kelamin' => 'P', 'created_at' => now()->subMonths(3)],
        (object)['id' => 5, 'nama' => 'Roni Wijaya',    'status' => 'Aktif',    'tanggal_lahir' => '2001-12-01', 'jenis_kelamin' => 'L', 'created_at' => now()],
    ]);

    $getStatusBadge  = fn($status) => $status === 'Aktif'
        ? '<span class="badge bg-success">Aktif</span>'
        : '<span class="badge bg-secondary">Nonaktif</span>';

    $getKategoriUsia = function ($tgl) {
        $umur = \Carbon\Carbon::parse($tgl)->age;
        if ($umur < 20)  return 'Remaja';
        if ($umur <= 50) return 'Dewasa';
        return 'Senior';
    };

    $anggotaLaki     = $dummyAnggota->filter(fn($a) => $a->jenis_kelamin === 'L')->values();
    $anggotaBulanIni = $dummyAnggota->filter(function ($a) {
        return \Carbon\Carbon::parse($a->created_at)->month === now()->month
            && \Carbon\Carbon::parse($a->created_at)->year  === now()->year;
    })->values();

    $bulan = now()->locale('id')->isoFormat('MMMM YYYY');

    $html = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Accessor & Scope — Tugas P9</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .section-card { border-left: 4px solid; }
        .section-buku    { border-color: #0d6efd; }
        .section-anggota { border-color: #198754; }
        .section-scope   { border-color: #fd7e14; }
        code { background: #f1f3f5; padding: 2px 6px; border-radius: 4px; font-size: .85em; }
    </style>
</head>
<body>
<div class="container py-5">

    <div class="mb-5">
        <h1 class="fw-bold"><i class="bi bi-bug"></i> Testing Accessor &amp; Scope</h1>
        <p class="text-muted">Tugas Pemrograman Web 2 | Candra Sya'bana Putra Gunadi</p>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Data di halaman ini adalah <strong>dummy data</strong> yang mensimulasikan hasil Eloquent Model.
        </div>
    </div>

    <div class="card shadow-sm mb-4 section-card section-buku">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-book"></i> Model Buku — Accessor</h4>
        </div>
        <div class="card-body">
            <p><code>getStatusStokBadgeAttribute()</code> dan <code>getTahunLabelAttribute()</code></p>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Judul</th><th>Pengarang</th>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Status Stok</th>
                            <th class="text-center">Label</th>
                        </tr>
                    </thead>
                    <tbody>
HTML;

    foreach ($dummyBuku as $buku) {
        $badge      = $getStatusStokBadge($buku->stok);
        $label      = $getTahunLabel($buku->tahun_terbit);
        $labelClass = $buku->tahun_terbit >= 2024 ? 'text-success fw-semibold' : 'text-secondary';
        $html .= "<tr>
            <td><strong>{$buku->judul}</strong></td>
            <td>{$buku->pengarang}</td>
            <td class='text-center'>{$buku->tahun_terbit}</td>
            <td class='text-center'>{$buku->stok}</td>
            <td class='text-center'>{$badge}</td>
            <td class='text-center'><span class='{$labelClass}'>{$label}</span></td>
        </tr>";
    }

    $html .= <<<HTML
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4 section-card section-scope">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><i class="bi bi-funnel"></i> Model Buku — Scope</h4>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <h6 class="fw-bold text-primary"><i class="bi bi-star"></i> scopeTerbaru()</h6>
                    <p class="text-muted small">Buku dengan <code>tahun_terbit &gt;= 2024</code></p>
                    <ul class="list-group list-group-flush">
HTML;

    foreach ($bukuTerbaru as $b) {
        $html .= "<li class='list-group-item'><strong>{$b->judul}</strong> <span class='badge bg-primary'>{$b->tahun_terbit}</span></li>";
    }

    $html .= "<li class='list-group-item text-muted'><span class='badge bg-primary'>{$bukuTerbaru->count()} buku</span></li></ul></div>";

    $html .= <<<HTML
                <div class="col-md-4">
                    <h6 class="fw-bold text-danger"><i class="bi bi-exclamation-triangle"></i> scopeStokMenipis()</h6>
                    <p class="text-muted small">Buku dengan <code>stok &lt; 5</code></p>
                    <ul class="list-group list-group-flush">
HTML;

    foreach ($bukuStokMenipis as $b) {
        $html .= "<li class='list-group-item d-flex justify-content-between'><strong>{$b->judul}</strong> <span class='text-danger fw-bold'>Stok: {$b->stok}</span></li>";
    }

    $html .= "<li class='list-group-item'><span class='badge bg-danger'>{$bukuStokMenipis->count()} buku</span></li></ul></div>";

    $html .= <<<HTML
                <div class="col-md-4">
                    <h6 class="fw-bold text-success"><i class="bi bi-cash-coin"></i> scopeHargaRange(50000, 100000)</h6>
                    <p class="text-muted small">Harga Rp 50.000 – Rp 100.000</p>
                    <ul class="list-group list-group-flush">
HTML;

    foreach ($bukuHargaRange as $b) {
        $hargaFmt = 'Rp ' . number_format($b->harga, 0, ',', '.');
        $html .= "<li class='list-group-item d-flex justify-content-between'><strong>{$b->judul}</strong> <span class='text-success'>{$hargaFmt}</span></li>";
    }

    $html .= "<li class='list-group-item'><span class='badge bg-success'>{$bukuHargaRange->count()} buku</span></li></ul></div></div></div></div>";

    $html .= <<<HTML
    <div class="card shadow-sm mb-4 section-card section-anggota">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="bi bi-people"></i> Model Anggota — Accessor</h4>
        </div>
        <div class="card-body">
            <p><code>getStatusBadgeAttribute()</code> dan <code>getKategoriUsiaAttribute()</code></p>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th class="text-center">Tgl Lahir</th>
                            <th class="text-center">JK</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Kategori Usia</th>
                        </tr>
                    </thead>
                    <tbody>
HTML;

    foreach ($dummyAnggota as $a) {
        $badge     = $getStatusBadge($a->status);
        $usia      = $getKategoriUsia($a->tanggal_lahir);
        $umur      = \Carbon\Carbon::parse($a->tanggal_lahir)->age;
        $jkLabel   = $a->jenis_kelamin === 'L'
            ? '<span class="badge bg-primary">L</span>'
            : '<span class="badge" style="background:#d63384;color:#fff">P</span>';
        $usiaClass = match($usia) {
            'Remaja' => 'text-info fw-semibold',
            'Dewasa' => 'text-success fw-semibold',
            'Senior' => 'text-warning fw-semibold',
            default  => '',
        };
        $html .= "<tr>
            <td><strong>{$a->nama}</strong></td>
            <td class='text-center'>{$a->tanggal_lahir} <small class='text-muted'>({$umur} th)</small></td>
            <td class='text-center'>{$jkLabel}</td>
            <td class='text-center'>{$badge}</td>
            <td class='text-center'><span class='{$usiaClass}'>{$usia}</span></td>
        </tr>";
    }

    $html .= <<<HTML
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4 section-card section-scope">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><i class="bi bi-funnel"></i> Model Anggota — Scope</h4>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary"><i class="bi bi-person"></i> scopeJenisKelamin('L')</h6>
                    <p class="text-muted small">Anggota laki-laki</p>
                    <ul class="list-group">
HTML;

    foreach ($anggotaLaki as $a) {
        $html .= "<li class='list-group-item'><i class='bi bi-person-fill text-primary'></i> {$a->nama}</li>";
    }

    $html .= "<li class='list-group-item'><span class='badge bg-primary'>{$anggotaLaki->count()} anggota</span></li></ul></div>";

    $html .= <<<HTML
                <div class="col-md-6">
                    <h6 class="fw-bold text-success"><i class="bi bi-calendar-check"></i> scopeTerdaftarBulanIni()</h6>
                    <p class="text-muted small">Mendaftar bulan <strong>{$bulan}</strong></p>
                    <ul class="list-group">
HTML;

    foreach ($anggotaBulanIni as $a) {
        $tgl   = \Carbon\Carbon::parse($a->created_at)->isoFormat('D MMMM YYYY');
        $html .= "<li class='list-group-item d-flex justify-content-between'><span><i class='bi bi-person-check text-success'></i> {$a->nama}</span> <small class='text-muted'>{$tgl}</small></li>";
    }

    $generatedAt = now()->format('d M Y H:i:s');

    $html .= <<<HTML
                    </ul>
                    <span class="badge bg-success mt-2">{$anggotaBulanIni->count()} anggota</span>
                </div>
            </div>
        </div>
    </div>

    <div class="text-muted mt-4 small">
        <i class="bi bi-clock"></i> Generated at: {$generatedAt}
    </div>

</div>
</body>
</html>
HTML;

    return $html;
});
