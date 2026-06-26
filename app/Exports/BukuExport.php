<?php

namespace App\Exports;

use App\Models\Buku;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BukuExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Buku::select([
            'kode_buku', 'judul', 'kategori', 'pengarang', 'penerbit',
            'tahun_terbit', 'isbn', 'harga', 'stok', 'bahasa', 'deskripsi',
        ])->get();
    }

    public function headings(): array
    {
        return [
            'Kode Buku', 'Judul', 'Kategori', 'Pengarang', 'Penerbit',
            'Tahun Terbit', 'ISBN', 'Harga', 'Stok', 'Bahasa', 'Deskripsi',
        ];
    }
}
