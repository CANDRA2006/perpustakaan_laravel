@extends('layouts.app')

@section('content')
<div class="container">

    <h1 class="mb-4">Dashboard Perpustakaan</h1>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="text-body-secondary">Total Buku</h5>
                    <h2 class="fw-bold">{{ $totalBuku }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="text-body-secondary">Buku Tersedia</h5>
                    <h2 class="fw-bold text-success">{{ $bukuTersedia }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="text-body-secondary">Buku Habis</h5>
                    <h2 class="fw-bold text-danger">{{ $bukuHabis }}</h2>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-4 g-4">

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">Total Anggota</div>
                <div class="card-body fs-4 fw-bold">
                    {{ $totalAnggota }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">Aktif</div>
                <div class="card-body fs-4 fw-bold text-success">
                    {{ $anggotaAktif }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">Nonaktif</div>
                <div class="card-body fs-4 fw-bold text-danger">
                    {{ $anggotaNonaktif }}
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-5 g-4">

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    5 Buku Terbaru
                </div>

                <ul class="list-group list-group-flush">
                    @foreach($bukuTerbaru as $buku)
                        <li class="list-group-item">
                            {{ $buku->judul }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    5 Anggota Terbaru
                </div>

                <ul class="list-group list-group-flush">
                    @foreach($anggotaTerbaru as $anggota)
                        <li class="list-group-item">
                            {{ $anggota->nama }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

    <div class="mt-5">
        <h4 class="mb-3">Quick Links</h4>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                Dashboard
            </a>

            <a href="{{ route('anggota.index') }}" class="btn btn-success">
                Anggota
            </a>

            <a href="{{ route('kategori.index') }}" class="btn btn-warning text-dark">
                Kategori
            </a>
        </div>
    </div>

</div>
@endsection
