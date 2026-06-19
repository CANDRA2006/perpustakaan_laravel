@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-file-text"></i> Laporan Transaksi</h1>
    <a href="{{ route('transaksi.laporan.pdf', request()->query()) }}" class="btn btn-danger">
        <i class="bi bi-file-pdf"></i> Export PDF
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('transaksi.laporan') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select">
                    <option value="Semua">Semua</option>
                    <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Anggota</label>
                <select name="anggota_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($anggotaList as $a)
                        <option value="{{ $a->id }}" {{ request('anggota_id') == $a->id ? 'selected' : '' }}>{{ $a->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-primary">
            <div class="card-body">
                <h6 class="text-muted">Total Transaksi</h6>
                <h2>{{ $totalTransaksi }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-danger">
            <div class="card-body">
                <h6 class="text-muted">Total Denda</h6>
                <h2>Rp {{ number_format($totalDenda, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th><th>Buku</th><th>Anggota</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th><th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksis as $t)
                        <tr>
                            <td><code>{{ $t->kode_transaksi }}</code></td>
                            <td>{{ $t->buku->judul }}</td>
                            <td>{{ $t->anggota->nama }}</td>
                            <td>{{ $t->tanggal_pinjam->format('d M Y') }}</td>
                            <td>{{ $t->tanggal_kembali_aktual ? $t->tanggal_kembali_aktual->format('d M Y') : '-' }}</td>
                            <td><span class="badge bg-{{ $t->status === 'Dikembalikan' ? 'success' : 'primary' }}">{{ $t->status }}</span></td>
                            <td>Rp {{ number_format($t->denda, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Tidak ada data transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
