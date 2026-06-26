<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBuku = Buku::count();
        $bukuTersedia = Buku::where('stok', '>', 0)->count();
        $bukuHabis = Buku::where('stok', 0)->count();

        $totalAnggota = Anggota::count();
        $anggotaAktif = Anggota::where('status', 'Aktif')->count();
        $anggotaNonaktif = Anggota::where('status', 'Nonaktif')->count();

        $bukuTerbaru = Buku::latest()->take(5)->get();
        $anggotaTerbaru = Anggota::latest()->take(5)->get();

        $transaksiTerlambat = Transaksi::with(['buku', 'anggota'])->terlambat()->latest()->get();
        $totalTerlambat = $transaksiTerlambat->count();

        /*
         |---------------------------------------------------------------
         | Chart 1 (Line): Trend peminjaman 6 bulan terakhir
         |---------------------------------------------------------------
         */
        $trendLabels = [];
        $trendData = [];

        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);

            $jumlah = Transaksi::whereYear('tanggal_pinjam', $bulan->year)
                ->whereMonth('tanggal_pinjam', $bulan->month)
                ->count();

            $trendLabels[] = $bulan->translatedFormat('M Y');
            $trendData[] = $jumlah;
        }

        /*
         |---------------------------------------------------------------
         | Chart 2 (Pie): Distribusi buku per kategori
         |---------------------------------------------------------------
         */
        $distribusiKategori = Buku::select('kategori')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        /*
         |---------------------------------------------------------------
         | Chart 3 (Bar): Top 10 buku terpopuler berdasarkan jumlah transaksi
         |---------------------------------------------------------------
         */
        $topBuku = Buku::select('buku.judul')
            ->selectRaw('COUNT(transaksi.id) as total_pinjam')
            ->leftJoin('transaksi', 'transaksi.buku_id', '=', 'buku.id')
            ->groupBy('buku.id', 'buku.judul')
            ->orderByDesc('total_pinjam')
            ->take(10)
            ->get();

        /*
         |---------------------------------------------------------------
         | Chart 4 (Donut): Status transaksi
         |---------------------------------------------------------------
         */
        $statusDipinjam = Transaksi::where('status', 'Dipinjam')->count();
        $statusDikembalikan = Transaksi::where('status', 'Dikembalikan')->count();

        return view('dashboard', compact(
            'totalBuku',
            'bukuTersedia',
            'bukuHabis',
            'totalAnggota',
            'anggotaAktif',
            'anggotaNonaktif',
            'bukuTerbaru',
            'anggotaTerbaru',
            'transaksiTerlambat',
            'totalTerlambat',
            'trendLabels',
            'trendData',
            'distribusiKategori',
            'topBuku',
            'statusDipinjam',
            'statusDikembalikan'
        ));
    }
}
