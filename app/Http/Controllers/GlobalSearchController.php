<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    /**
     * Pencarian global lintas modul: Buku, Anggota, dan Transaksi.
     */
    public function index(Request $request)
    {
        $keyword = trim((string) $request->get('q', ''));

        $bukus = collect();
        $anggotas = collect();
        $transaksis = collect();

        if ($keyword !== '') {
            $bukus = Buku::where('judul', 'like', "%{$keyword}%")
                ->orWhere('pengarang', 'like', "%{$keyword}%")
                ->orWhere('kode_buku', 'like', "%{$keyword}%")
                ->orWhere('penerbit', 'like', "%{$keyword}%")
                ->latest()
                ->take(25)
                ->get();

            $anggotas = Anggota::where('nama', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%")
                ->orWhere('kode_anggota', 'like', "%{$keyword}%")
                ->orWhere('telepon', 'like', "%{$keyword}%")
                ->latest()
                ->take(25)
                ->get();

            $transaksis = Transaksi::with(['buku', 'anggota'])
                ->where('kode_transaksi', 'like', "%{$keyword}%")
                ->orWhereHas('buku', function ($q) use ($keyword) {
                    $q->where('judul', 'like', "%{$keyword}%");
                })
                ->orWhereHas('anggota', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%");
                })
                ->latest()
                ->take(25)
                ->get();
        }

        $totalResults = $bukus->count() + $anggotas->count() + $transaksis->count();

        return view('search.index', compact(
            'keyword', 'bukus', 'anggotas', 'transaksis', 'totalResults'
        ));
    }
}
