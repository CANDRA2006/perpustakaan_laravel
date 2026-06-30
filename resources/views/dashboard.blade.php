@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <div class="p-4 p-md-5 rounded-4 text-white shadow-sm" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 45%, #0a58ca 100%);">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="bi bi-book-fill text-primary">Ringkasan Perpustakaan</span>
                <h1 class="display-6 fw-bold mb-3">Dashboard Sistem Perpustakaan</h1>
                <p class="lead mb-0 opacity-75">
                    Pantau kondisi koleksi buku, status anggota, dan data terbaru dalam satu tampilan.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-10 border border-white border-opacity-25" style="width: 110px; height: 110px;">
                    <i class="bi bi-book-half display-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted mb-1">Total Buku</p>
                        <h2 class="fw-bold mb-0">{{ $totalBuku }}</h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-book fs-4"></i>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge text-bg-success">Tersedia: {{ $bukuTersedia }}</span>
                    <span class="badge text-bg-danger">Habis: {{ $bukuHabis }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted mb-1">Total Anggota</p>
                        <h2 class="fw-bold mb-0">{{ $totalAnggota }}</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge text-bg-success">Aktif: {{ $anggotaAktif }}</span>
                    <span class="badge text-bg-secondary">Nonaktif: {{ $anggotaNonaktif }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-2">Quick Links</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('buku.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-book me-2"></i> Menu Buku
                    </a>
                    <a href="{{ route('anggota.index') }}" class="btn btn-outline-success text-start">
                        <i class="bi bi-people me-2"></i> Menu Anggota
                    </a>
                    <a href="{{ route('search') }}" class="btn btn-outline-secondary text-start">
                        <i class="bi bi-search me-2"></i> Pencarian Global
                    </a>
                    <a href="{{ route('transaksi.create') }}" class="btn btn-outline-info text-start">
                        <i class="bi bi-arrow-left-right me-2"></i> Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 {{ $totalTerlambat > 0 ? 'border-start border-danger border-4' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted mb-1">Buku Terlambat</p>
                        <h2 class="fw-bold mb-0 {{ $totalTerlambat > 0 ? 'text-danger' : '' }}">{{ $totalTerlambat }}</h2>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </div>
                </div>
                <a href="{{ route('transaksi.index', ['status' => 'Dipinjam']) }}" class="small text-decoration-none">
                    Lihat semua transaksi dipinjam <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="bi bi-book-fill text-primary"></i>Anggota dengan Buku Terlambat</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Anggota</th><th>Buku</th><th>Jatuh Tempo</th><th>Terlambat</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksiTerlambat->take(5) as $t)
                            <tr>
                                <td>{{ $t->anggota->nama }}</td>
                                <td>{{ $t->buku->judul }}</td>
                                <td>{{ $t->tanggal_kembali_rencana->format('d M Y') }}</td>
                                <td><span class="badge bg-danger">{{ $t->hari_terlambat }} hari</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada buku terlambat</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- CHARTS --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Trend Peminjaman (6 Bulan Terakhir)</h5>
            </div>
            <div class="card-body">
                <canvas id="chartTrend" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="bi bi-pie-chart-fill text-success me-2"></i>Distribusi Kategori Buku</h5>
            </div>
            <div class="card-body">
                <canvas id="chartKategori" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="bi bi-bar-chart-fill text-warning me-2"></i>Top 10 Buku Terpopuler</h5>
            </div>
            <div class="card-body">
                <canvas id="chartTopBuku" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="bi bi-circle-half text-info me-2"></i>Status Transaksi</h5>
            </div>
            <div class="card-body">
                <canvas id="chartStatus" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="bi bi-journal-text me-2 text-primary"></i>5 Buku Terbaru</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bukuTerbaru as $buku)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $buku->judul }}</div>
                                    <small class="text-muted">{{ $buku->kode_buku }}</small>
                                </td>
                                <td>{{ $buku->kategori }}</td>
                                <td>
                                    @if ($buku->stok > 0)
                                        <span class="badge text-bg-success">{{ $buku->stok }}</span>
                                    @else
                                        <span class="badge text-bg-danger">Habis</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Belum ada data buku.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="bi bi-people-fill me-2 text-success"></i>5 Anggota Terbaru</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($anggotaTerbaru as $anggota)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $anggota->nama }}</div>
                                    <small class="text-muted">{{ $anggota->email }}</small>
                                </td>
                                <td>{{ $anggota->kode_anggota }}</td>
                                <td>
                                    @if (strtolower($anggota->status) === 'aktif')
                                        <span class="badge text-bg-success">Aktif</span>
                                    @else
                                        <span class="badge text-bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Belum ada data anggota.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const palette = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#20c997', '#fd7e14', '#0dcaf0', '#6610f2', '#d63384'];

    // Chart 1: Line - Trend Peminjaman
    new Chart(document.getElementById('chartTrend'), {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: @json($trendData),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.15)',
                tension: 0.35,
                fill: true,
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    // Chart 2: Pie - Distribusi Kategori
    new Chart(document.getElementById('chartKategori'), {
        type: 'pie',
        data: {
            labels: @json($distribusiKategori->pluck('kategori')),
            datasets: [{
                data: @json($distribusiKategori->pluck('total')),
                backgroundColor: palette,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Chart 3: Bar - Top 10 Buku Terpopuler
    new Chart(document.getElementById('chartTopBuku'), {
        type: 'bar',
        data: {
            labels: @json($topBuku->pluck('judul')),
            datasets: [{
                label: 'Jumlah Dipinjam',
                data: @json($topBuku->pluck('total_pinjam')),
                backgroundColor: '#ffc107',
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    // Chart 4: Donut - Status Transaksi
    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Dipinjam', 'Dikembalikan'],
            datasets: [{
                data: [{{ $statusDipinjam }}, {{ $statusDikembalikan }}],
                backgroundColor: ['#0d6efd', '#198754'],
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
@endpush
@endsection
