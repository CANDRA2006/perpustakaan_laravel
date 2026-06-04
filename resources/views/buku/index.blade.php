@extends('layouts.app')

@section('title', 'Daftar Buku')

@php
    $staticKategoriOptions = ['Programming', 'Database', 'Web Design', 'Networking', 'Data Science'];
@endphp

@section('content')

{{-- Header --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="mb-0">
        <i class="bi bi-book"></i> Daftar Buku
    </h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('buku.export') }}" class="btn btn-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
        <a href="{{ route('buku.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Buku
        </a>
    </div>
</div>

{{-- Statistik Cards --}}
<div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
    <div class="col">
        <div class="card border-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Buku</h6>
                        <h2 class="mb-0">{{ $totalBuku }}</h2>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-book-fill" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card border-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Buku Tersedia</h6>
                        <h2 class="mb-0">{{ $bukuTersedia }}</h2>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card border-danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Buku Habis</h6>
                        <h2 class="mb-0">{{ $bukuHabis }}</h2>
                    </div>
                    <div class="text-danger">
                        <i class="bi bi-x-circle-fill" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form Search & Filter --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="bi bi-search"></i> Cari & Filter Buku
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('buku.search') }}" method="GET">
            <div class="row g-3 align-items-end">

                {{-- Keyword --}}
                <div class="col-12 col-lg-4">
                    <label for="keyword" class="form-label small fw-semibold">
                        <i class="bi bi-search"></i> Kata Kunci
                    </label>
                    <input type="text"
                           id="keyword"
                           name="keyword"
                           class="form-control"
                           placeholder="Judul, pengarang, atau penerbit..."
                           value="{{ $searchInput['keyword'] ?? '' }}">
                </div>

                {{-- Filter Kategori --}}
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="kategori" class="form-label small fw-semibold">
                        <i class="bi bi-tag"></i> Kategori
                    </label>
                    <select id="kategori" name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach ($staticKategoriOptions as $kat)
                            <option value="{{ $kat }}"
                                {{ isset($searchInput['kategori']) && $searchInput['kategori'] == $kat ? 'selected' : '' }}>
                                {{ $kat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tahun --}}
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="tahun" class="form-label small fw-semibold">
                        <i class="bi bi-calendar"></i> Tahun Terbit
                    </label>
                    <select id="tahun" name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @isset($tahuns)
                            @foreach ($tahuns as $thn)
                                <option value="{{ $thn }}"
                                    {{ isset($searchInput['tahun']) && $searchInput['tahun'] == $thn ? 'selected' : '' }}>
                                    {{ $thn }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                {{-- Filter Ketersediaan --}}
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="stok" class="form-label small fw-semibold">
                        <i class="bi bi-boxes"></i> Ketersediaan
                    </label>
                    <select id="stok" name="stok" class="form-select">
                        <option value="semua" {{ isset($searchInput['stok']) && $searchInput['stok'] == 'semua' ? 'selected' : '' }}>Semua</option>
                        <option value="tersedia" {{ isset($searchInput['stok']) && $searchInput['stok'] == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="habis" {{ isset($searchInput['stok']) && $searchInput['stok'] == 'habis' ? 'selected' : '' }}>Habis</option>
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="col-12 col-sm-6 col-lg-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary" aria-label="Reset filter">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                </div>

            </div>
        </form>

        {{-- Info hasil pencarian --}}
        @if (isset($searchInput) && array_filter($searchInput))
            <div class="mt-3 alert alert-info py-2 mb-0">
                <i class="bi bi-info-circle"></i>
                Hasil pencarian ditemukan <strong>{{ $totalBuku }}</strong> buku.
                <a href="{{ route('buku.index') }}" class="ms-2">
                    <i class="bi bi-x"></i> Reset Filter
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Filter Kategori (Pills) --}}
<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title">
            <i class="bi bi-funnel"></i> Filter Kategori:
        </h6>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('buku.index') }}"
               class="btn btn-sm {{ !isset($kategori) ? 'btn-primary' : 'btn-outline-primary' }}">
                Semua
            </a>
            @foreach ($staticKategoriOptions as $kat)
                <a href="{{ route('buku.kategori', $kat) }}"
                   class="btn btn-sm {{ isset($kategori) && $kategori == $kat ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ $kat }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Bulk Delete Form + Daftar Buku --}}
<form action="{{ route('buku.bulk-delete') }}" method="POST" id="bulk-delete-form">
    @csrf

    @if ($bukus->count() > 0)
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="select-all">
                    <label class="form-check-label fw-semibold" for="select-all">
                        Pilih Semua Buku
                    </label>
                </div>
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Yakin ingin menghapus semua buku yang dipilih?')">
                    <i class="bi bi-trash"></i> Hapus Buku Terpilih
                </button>
            </div>
        </div>
    @endif

    @forelse ($bukus as $buku)
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3 align-items-center">

                    {{-- Kolom: Checkbox + Ikon + Badge --}}
                    <div class="col-12 col-md-2 text-center">
                        <div class="form-check d-inline-block mb-2">
                            <input class="form-check-input" type="checkbox"
                                   name="buku_ids[]" value="{{ $buku->id }}"
                                   id="buku-{{ $buku->id }}">
                            <label class="form-check-label small" for="buku-{{ $buku->id }}">Pilih</label>
                        </div>
                        <br>
                        <i class="bi bi-book text-primary" style="font-size: 4rem;"></i>
                        <div class="mt-2">
                            <span class="badge bg-{{ $buku->kategori == 'Programming' ? 'primary' : ($buku->kategori == 'Database' ? 'success' : ($buku->kategori == 'Web Design' ? 'info' : ($buku->kategori == 'Networking' ? 'warning' : 'danger'))) }}">
                                {{ $buku->kategori }}
                            </span>
                        </div>
                    </div>

                    {{-- Kolom: Info Buku --}}
                    <div class="col-12 col-md-7">
                        <h5 class="card-title">
                            <a href="{{ route('buku.show', $buku->id) }}" class="text-decoration-none">
                                {{ $buku->judul }}
                            </a>
                        </h5>
                        <p class="card-text text-muted mb-2">
                            <i class="bi bi-person"></i> {{ $buku->pengarang }} |
                            <i class="bi bi-building"></i> {{ $buku->penerbit }} |
                            <i class="bi bi-calendar"></i> {{ $buku->tahun_terbit }}
                        </p>
                        @if ($buku->isbn)
                            <p class="card-text small text-muted mb-1">
                                <i class="bi bi-upc"></i> ISBN: {{ $buku->isbn }}
                            </p>
                        @endif
                        @if ($buku->deskripsi)
                            <p class="card-text">
                                {{ Str::limit($buku->deskripsi, 150) }}
                            </p>
                        @endif
                    </div>

                    {{-- Kolom: Harga, Stok, Aksi --}}
                    <div class="col-12 col-md-3 text-md-end">
                        <h4 class="text-primary mb-2">
                            {{ $buku->harga_format }}
                        </h4>

                        <div class="mb-3">
                            @if ($buku->stok > 0)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Tersedia
                                </span>
                                <div class="text-muted small mt-1">
                                    Stok: {{ $buku->stok }} buku
                                </div>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle"></i> Habis
                                </span>
                            @endif
                        </div>

                        <div class="btn-group-vertical d-grid gap-2">
                            <a href="{{ route('buku.show', $buku->id) }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            <a href="{{ route('buku.edit', $buku->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Tidak ada data buku
            @if (isset($kategori))
                dengan kategori <strong>{{ $kategori }}</strong>
            @endif
        </div>
    @endforelse

    @if ($bukus->count() > 0)
        <div class="text-center mt-4">
            <p class="text-muted">
                Menampilkan {{ $bukus->count() }} buku
                @if (isset($kategori))
                    dari kategori <strong>{{ $kategori }}</strong>
                @endif
            </p>
        </div>
    @endif

</form>

@endsection

@section('scripts')
<script>
    document.getElementById('select-all')?.addEventListener('change', function () {
        document.querySelectorAll('input[name="buku_ids[]"]').forEach(cb => {
            cb.checked = this.checked;
        });
    });
</script>
@endsection
