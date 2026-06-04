@php
    $kategoriOptions = ['Programming', 'Database', 'Web Design', 'Networking', 'Data Science'];
    $bahasaOptions = ['Indonesia', 'Inggris'];
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label for="kode_buku" class="form-label">Kode Buku</label>
        <input type="text" id="kode_buku" name="kode_buku" class="form-control @error('kode_buku') is-invalid @enderror" value="{{ old('kode_buku', $buku?->kode_buku) }}" placeholder="BK-PROG-001" required>
        @error('kode_buku') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-8">
        <label for="judul" class="form-label">Judul</label>
        <input type="text" id="judul" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $buku?->judul) }}" required>
        @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="kategori" class="form-label">Kategori</label>
        <select id="kategori" name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
            <option value="">Pilih Kategori</option>
            @foreach ($kategoriOptions as $option)
                <option value="{{ $option }}" {{ old('kategori', $buku?->kategori) === $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="bahasa" class="form-label">Bahasa</label>
        <select id="bahasa" name="bahasa" class="form-select @error('bahasa') is-invalid @enderror" required>
            <option value="">Pilih Bahasa</option>
            @foreach ($bahasaOptions as $option)
                <option value="{{ $option }}" {{ old('bahasa', $buku?->bahasa) === $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        @error('bahasa') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">Kategori Programming wajib menggunakan bahasa Inggris.</div>
    </div>

    <div class="col-md-4">
        <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
        <input type="number" id="tahun_terbit" name="tahun_terbit" class="form-control @error('tahun_terbit') is-invalid @enderror" value="{{ old('tahun_terbit', $buku?->tahun_terbit) }}" min="1900" max="{{ date('Y') }}" required>
        @error('tahun_terbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="pengarang" class="form-label">Pengarang</label>
        <input type="text" id="pengarang" name="pengarang" class="form-control @error('pengarang') is-invalid @enderror" value="{{ old('pengarang', $buku?->pengarang) }}" required>
        @error('pengarang') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="penerbit" class="form-label">Penerbit</label>
        <input type="text" id="penerbit" name="penerbit" class="form-control @error('penerbit') is-invalid @enderror" value="{{ old('penerbit', $buku?->penerbit) }}" required>
        @error('penerbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="isbn" class="form-label">ISBN</label>
        <input type="text" id="isbn" name="isbn" class="form-control @error('isbn') is-invalid @enderror" value="{{ old('isbn', $buku?->isbn) }}">
        @error('isbn') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="harga" class="form-label">Harga</label>
        <input type="number" id="harga" name="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga', $buku?->harga) }}" min="0" required>
        @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="stok" class="form-label">Stok</label>
        <input type="number" id="stok" name="stok" class="form-control @error('stok') is-invalid @enderror" value="{{ old('stok', $buku?->stok) }}" min="0" required>
        @error('stok') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">Jika tahun terbit kurang dari 2000, stok maksimal 5 buku.</div>
    </div>

    <div class="col-12">
        <label for="deskripsi" class="form-label">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4">{{ old('deskripsi', $buku?->deskripsi) }}</textarea>
        @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">Batal</a>
    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
</div>
