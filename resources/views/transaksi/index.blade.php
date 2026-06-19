@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-arrow-left-right"></i> Daftar Transaksi</h1>
    <div>
        <a href="{{ route('transaksi.laporan') }}" class="btn btn-outline-primary me-2">
            <i class="bi bi-file-text"></i> Laporan
        </a>
        <a href="{{ route('transaksi.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Pinjam Buku
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Buku</th>
                        <th>Anggota</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksis as $t)
                        <tr>
                            <td><code>{{ $t->kode_transaksi }}</code></td>
                            <td>{{ $t->buku->judul }}</td>
                            <td>{{ $t->anggota->nama }}</td>
                            <td>{{ $t->tanggal_pinjam->format('d M Y') }}</td>
                            <td>{{ $t->tanggal_kembali_rencana->format('d M Y') }}</td>
                            <td>
                                @if ($t->status === 'Dikembalikan')
                                    <span class="badge bg-success">Dikembalikan</span>
                                @elseif ($t->is_terlambat)
                                    <span class="badge bg-danger">Terlambat {{ $t->hari_terlambat }} hari</span>
                                @else
                                    <span class="badge bg-primary">Dipinjam</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('transaksi.show', $t->id) }}" class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Belum ada data transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
