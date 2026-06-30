@extends('layouts.app')

@section('title', 'Kelola Kategori Buku')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-bookmark-fill"></i> Kelola Kategori Buku</h2>
    <a href="{{ route('kategori.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Kategori
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('kategori.index') }}" class="d-flex gap-2">
            <input type="search" name="keyword" class="form-control" value="{{ $keyword }}" placeholder="Cari nama atau deskripsi kategori...">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
            <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i> Reset</a>
        </form>
    </div>
</div>

<div class="row">
    @forelse ($kategoris as $kategori)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-{{ $kategori->warna ?? 'primary' }} text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-{{ $kategori->icon ?? 'bookmark' }}"></i> {{ $kategori->nama_kategori }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted">
                        {{ $kategori->deskripsi ?: 'Tidak ada deskripsi.' }}
                    </p>
                    <span class="badge bg-info text-dark" style="font-size: .9rem;">
                        <i class="bi bi-book"></i> {{ $kategori->bukus_count }} Buku
                    </span>
                </div>
                <div class="card-footer bg-light d-flex gap-2">
                    <a href="{{ route('kategori.show', $kategori->id) }}" class="btn btn-sm btn-info text-white flex-fill">
                        <i class="bi bi-eye"></i> Detail
                    </a>
                    <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-sm btn-warning flex-fill">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus kategori ini?')" class="flex-fill">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger w-100">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i> Tidak ada kategori yang ditemukan.
            </div>
        </div>
    @endforelse
</div>

<div class="row mt-2">
    <div class="col-12">
        <div class="card text-bg-body border">
            <div class="card-body">
                <h6>
                    <i class="bi bi-bar-chart"></i> Statistik
                </h6>
                <p class="mb-0">
                    <strong>Total Kategori:</strong> {{ $kategoris->count() }} |
                    <strong>Total Buku Keseluruhan:</strong> {{ $totalBukuKeseluruhan }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
