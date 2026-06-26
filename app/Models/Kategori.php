<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'icon',
        'warna',
    ];

    /**
     * Relasi hasMany ke Buku (kebalikan dari Buku::kategoriModel()).
     */
    public function bukus()
    {
        return $this->hasMany(Buku::class, 'kategori_id');
    }
}
