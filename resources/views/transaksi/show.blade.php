@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}">Transaksi</a></li>
        <li class="breadcrumb-item active">{{ $transaksi->kode_transaksi }}</li>
    </ol>
</nav>

@if ($transaksi->status === 'Dipinjam' && $transaksi->is_terlambat)
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <strong>Peringatan!</strong> Buku ini sudah melewati tanggal kembali
        ({{ $transaksi->tanggal_kembali_rencana->format('d F Y') }})
        selama <strong>{{ $transaksi->hari_terlambat }} hari</strong>.
        Estimasi denda saat ini: <strong>Rp {{ number_format($transaksi->denda_estimasi, 0, ',', '.') }}</strong>.
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-receipt"></i> Detail Transaksi</h4>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><td width="200" class="fw-bold">Kode Transaksi</td><td>: <code>{{ $transaksi->kode_transaksi }}</code></td></tr>
                    <tr><td class="fw-bold">Buku</td><td>: {{ $transaksi->buku->judul }}</td></tr>
                    <tr><td class="fw-bold">Anggota</td><td>: {{ $transaksi->anggota->nama }} ({{ $transaksi->anggota->kode_anggota }})</td></tr>
                    <tr><td class="fw-bold">Tanggal Pinjam</td><td>: {{ $transaksi->tanggal_pinjam->format('d F Y') }}</td></tr>
                    <tr><td class="fw-bold">Tanggal Kembali (Rencana)</td><td>: {{ $transaksi->tanggal_kembali_rencana->format('d F Y') }}</td></tr>
                    <tr><td class="fw-bold">Tanggal Kembali (Aktual)</td><td>: {{ $transaksi->tanggal_kembali_aktual ? $transaksi->tanggal_kembali_aktual->format('d F Y') : '-' }}</td></tr>
                    <tr>
                        <td class="fw-bold">Status</td>
                        <td>:
                            @if ($transaksi->status === 'Dikembalikan')
                                <span class="badge bg-success">Dikembalikan</span>
                            @elseif ($transaksi->is_terlambat)
                                <span class="badge bg-danger">Terlambat {{ $transaksi->hari_terlambat }} hari</span>
                            @else
                                <span class="badge bg-primary">Dipinjam</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total Denda</td>
                        <td>
                            : <span class="fw-bold {{ $transaksi->denda > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($transaksi->denda, 0, ',', '.') }}
                            </span>
                            @if ($transaksi->status === 'Dipinjam' && $transaksi->is_terlambat)
                                <small class="text-muted">(estimasi berjalan: Rp {{ number_format($transaksi->denda_estimasi, 0, ',', '.') }})</small>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="bi bi-gear"></i> Aksi</h6>
            </div>
            <div class="card-body d-grid gap-2">
                @if ($transaksi->status === 'Dipinjam')
                    <form action="{{ route('transaksi.kembalikan', $transaksi->id) }}" method="POST"
                          onsubmit="return confirm('Yakin ingin mengembalikan buku ini?')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-arrow-return-left"></i> Kembalikan Buku
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary w-100" disabled>
                        <i class="bi bi-check-circle"></i> Sudah Dikembalikan
                    </button>
                @endif
                <a href="{{ route('transaksi.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
