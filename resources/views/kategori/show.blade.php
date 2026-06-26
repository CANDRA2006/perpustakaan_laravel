@extends('layouts.app')

@section('title', 'Detail Kategori - ' . $kategori->nama_kategori)

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bi bi-house"></i> Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}"><i class="bi bi-list"></i> Kategori Buku</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $kategori->nama_kategori }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-{{ $kategori->warna ?? 'primary' }} text-white">
                <h4 class="mb-0">
                    <i class="bi bi-{{ $kategori->icon ?? 'bookmark' }}"></i> {{ $kategori->nama_kategori }}
                </h4>
            </div>
            <div class="card-body">
                <p class="lead">{{ $kategori->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Kategori ID</h6>
                        <p class="h5">#{{ $kategori->id }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Total Buku</h6>
                        <p class="h5">
                            <span class="badge bg-warning text-dark" style="font-size: 1rem;">
                                {{ $kategori->bukus_count ?? $bukus->count() }} Buku
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-book"></i> Daftar Buku dalam Kategori</h5>
            </div>
            <div class="card-body">
                @if ($bukus->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Judul Buku</th>
                                    <th>Pengarang</th>
                                    <th class="text-center">Tahun</th>
                                    <th class="text-center">Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bukus as $index => $buku)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $buku->judul }}</strong></td>
                                        <td>{{ $buku->pengarang }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $buku->tahun_terbit }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $buku->stok > 0 ? 'bg-success' : 'bg-danger' }}">{{ $buku->stok }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('buku.show', $buku->id) }}" class="btn btn-sm btn-info text-white">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> Belum ada buku dalam kategori ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-gear"></i> Aksi</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit Kategori
                </a>
                <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash"></i> Hapus Kategori
                    </button>
                </form>
                <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
