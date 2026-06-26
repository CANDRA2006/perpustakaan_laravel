@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning">
                <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Kategori: {{ $kategori->nama_kategori }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('kategori.update', $kategori->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" id="nama_kategori"
                               class="form-control @error('nama_kategori') is-invalid @enderror"
                               value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
                        @error('nama_kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if ($kategori->bukus()->count() > 0)
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Mengganti nama akan otomatis memperbarui label kategori pada {{ $kategori->bukus()->count() }} buku terkait.
                            </small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3"
                                  class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
                        @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="icon" class="form-label">Icon (Bootstrap Icons)</label>
                            <input type="text" name="icon" id="icon"
                                   class="form-control @error('icon') is-invalid @enderror"
                                   value="{{ old('icon', $kategori->icon) }}">
                            @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="warna" class="form-label">Warna Badge</label>
                            <select name="warna" id="warna" class="form-select @error('warna') is-invalid @enderror">
                                @foreach (['primary', 'success', 'info', 'warning', 'danger', 'secondary', 'dark'] as $warna)
                                    <option value="{{ $warna }}" {{ old('warna', $kategori->warna) == $warna ? 'selected' : '' }}>{{ ucfirst($warna) }}</option>
                                @endforeach
                            </select>
                            @error('warna') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('kategori.show', $kategori->id) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save"></i> Update Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
