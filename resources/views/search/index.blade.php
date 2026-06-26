@extends('layouts.app')

@section('title', 'Pencarian Global')

@php
    if (!function_exists('highlightKeyword')) {
        function highlightKeyword($text, $keyword)
        {
            if (!$keyword) {
                return e($text);
            }

            return preg_replace(
                '/(' . preg_quote($keyword, '/') . ')/i',
                '<mark style="background-color:#fff3cd;color:#000;">$1</mark>',
                e($text)
            );
        }
    }
@endphp

@section('content')
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <h4 class="mb-3"><i class="bi bi-search"></i> Pencarian Global</h4>
        <form method="GET" action="{{ route('search') }}" class="row g-2">
            <div class="col-md-9">
                <input type="search" name="q" class="form-control form-control-lg"
                       value="{{ $keyword }}"
                       placeholder="Cari buku, anggota, atau transaksi...">
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </form>
    </div>
</div>

@if ($keyword !== '')
    <p class="text-muted">
        Ditemukan <strong>{{ $totalResults }}</strong> hasil untuk kata kunci
        <mark style="background-color:#fff3cd;color:#000;">{{ $keyword }}</mark>
        di 3 modul (Buku, Anggota, Transaksi).
    </p>

    <ul class="nav nav-tabs mb-3" id="searchTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-buku-btn" data-bs-toggle="tab" data-bs-target="#tab-buku" type="button">
                <i class="bi bi-book"></i> Buku <span class="badge bg-primary ms-1">{{ $bukus->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-anggota-btn" data-bs-toggle="tab" data-bs-target="#tab-anggota" type="button">
                <i class="bi bi-people"></i> Anggota <span class="badge bg-success ms-1">{{ $anggotas->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-transaksi-btn" data-bs-toggle="tab" data-bs-target="#tab-transaksi" type="button">
                <i class="bi bi-arrow-left-right"></i> Transaksi <span class="badge bg-info ms-1">{{ $transaksis->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Tab Buku --}}
        <div class="tab-pane fade show active" id="tab-buku" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @forelse ($bukus as $buku)
                        <div class="d-flex justify-content-between align-items-start py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <a href="{{ route('buku.show', $buku->id) }}" class="text-decoration-none">
                                    <strong>{!! highlightKeyword($buku->judul, $keyword) !!}</strong>
                                </a>
                                <div class="text-muted small">
                                    {!! highlightKeyword($buku->pengarang, $keyword) !!} &middot; {{ $buku->kategori }} &middot; {{ $buku->harga_format }}
                                </div>
                            </div>
                            <span class="badge {{ $buku->stok > 0 ? 'text-bg-success' : 'text-bg-danger' }}">
                                Stok: {{ $buku->stok }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">Tidak ada buku yang cocok.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Tab Anggota --}}
        <div class="tab-pane fade" id="tab-anggota" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @forelse ($anggotas as $anggota)
                        <div class="d-flex justify-content-between align-items-start py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <a href="{{ route('anggota.show', $anggota->id) }}" class="text-decoration-none">
                                    <strong>{!! highlightKeyword($anggota->nama, $keyword) !!}</strong>
                                </a>
                                <div class="text-muted small">
                                    {!! highlightKeyword($anggota->email, $keyword) !!} &middot; <code>{{ $anggota->kode_anggota }}</code>
                                </div>
                            </div>
                            <span class="badge {{ $anggota->status == 'Aktif' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $anggota->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">Tidak ada anggota yang cocok.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Tab Transaksi --}}
        <div class="tab-pane fade" id="tab-transaksi" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @forelse ($transaksis as $t)
                        <div class="d-flex justify-content-between align-items-start py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <a href="{{ route('transaksi.show', $t->id) }}" class="text-decoration-none">
                                    <strong>{!! highlightKeyword($t->kode_transaksi, $keyword) !!}</strong>
                                </a>
                                <div class="text-muted small">
                                    {{ $t->buku->judul }} &middot; {{ $t->anggota->nama }}
                                </div>
                            </div>
                            <span class="badge {{ $t->status === 'Dikembalikan' ? 'text-bg-success' : 'text-bg-primary' }}">
                                {{ $t->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">Tidak ada transaksi yang cocok.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Masukkan kata kunci untuk mencari di seluruh modul Buku, Anggota, dan Transaksi sekaligus.
    </div>
@endif
@endsection
