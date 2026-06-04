<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'bukus';

    protected $fillable = [
        'kode_buku',
        'judul',
        'kategori',
        'bahasa',
        'pengarang',
        'penerbit',
        'tahun_terbit',
        'isbn',
        'harga',
        'stok',
        'deskripsi',
        'kategori_id',
    ];

    protected $casts = [
        'harga'       => 'integer',
        'stok'        => 'integer',
        'tahun_terbit' => 'integer',
    ];

    // ACCESSORS

    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga ?? 0, 0, ',', '.');
    }

    /**
     * @return string
     */
    public function getStatusStokBadgeAttribute(): string
    {
        $stok = $this->stok ?? 0;

        if ($stok === 0) {
            return '<span class="badge bg-danger">Habis</span>';
        } elseif ($stok <= 5) {
            return '<span class="badge bg-warning text-dark">Menipis</span>';
        } elseif ($stok <= 15) {
            return '<span class="badge bg-info text-dark">Sedang</span>';
        } else {
            return '<span class="badge bg-success">Aman</span>';
        }
    }

    /**
     * Accessor: Label tahun buku.
     * @return string
     */
    public function getTahunLabelAttribute(): string
    {
        $tahun = $this->tahun_terbit ?? 0;
        if ($tahun < 2000) {
            return '<span class="badge bg-secondary">Klasik</span>';
        } elseif ($tahun <= 2010) {
            return '<span class="badge bg-primary">Modern</span>';
        } else {
            return '<span class="badge bg-success">Kontemporer</span>';
        }
    }
}
