<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiController extends Controller
{
    const LAMA_PINJAM_HARI = 7;

    public function index(Request $request)
    {
        $query = Transaksi::with(['buku', 'anggota'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transaksis = $query->get();

        return view('transaksi.index', compact('transaksis'));
    }

    public function create(Request $request)
    {
        $bukus = Buku::tersedia()->orderBy('judul')->get();
        $anggotas = Anggota::aktif()->orderBy('nama')->get();

        // Mendukung prefill buku terpilih
        $selectedBukuId = $request->integer('buku_id') ?: null;

        return view('transaksi.create', compact('bukus', 'anggotas', 'selectedBukuId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'buku_id' => 'required|exists:buku,id',
            'anggota_id' => 'required|exists:anggota,id',
            'tanggal_pinjam' => 'required|date',
            'lama_pinjam' => 'nullable|integer|min:1|max:30',
        ]);

        $buku = Buku::findOrFail($validated['buku_id']);

        if ($buku->stok < 1) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Stok buku tidak tersedia.');
        }

        DB::transaction(function () use ($validated, $buku) {
            $tanggalPinjam = Carbon::parse($validated['tanggal_pinjam']);
            $lamaPinjam = (int) ($validated['lama_pinjam'] ?? self::LAMA_PINJAM_HARI);

            Transaksi::create([
                'kode_transaksi' => $this->generateKodeTransaksi(),
                'buku_id' => $buku->id,
                'anggota_id' => $validated['anggota_id'],
                'tanggal_pinjam' => $tanggalPinjam,
                'tanggal_kembali_rencana' => $tanggalPinjam->copy()->addDays($lamaPinjam),
                'status' => 'Dipinjam',
                'denda' => 0,
            ]);

            $buku->decrement('stok');
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi peminjaman berhasil dibuat.');
    }

    public function show(string $id)
    {
        $transaksi = Transaksi::with(['buku', 'anggota'])->findOrFail($id);

        return view('transaksi.show', compact('transaksi'));
    }

    /**
     * Proses pengembalian buku: hitung denda jika terlambat & tambah stok buku.
     */
    public function kembalikan(Request $request, string $id)
    {
        $transaksi = Transaksi::with('buku')->findOrFail($id);

        if ($transaksi->status === 'Dikembalikan') {
            return redirect()->route('transaksi.show', $transaksi->id)
                ->with('error', 'Transaksi ini sudah dikembalikan sebelumnya.');
        }

        try {
            DB::transaction(function () use ($transaksi) {
                $tanggalKembaliAktual = Carbon::today();

                // Denda Rp 5.000/hari, hanya jika terlambat
                $hariTerlambat = 0;
                if ($tanggalKembaliAktual->gt($transaksi->tanggal_kembali_rencana)) {
                    $hariTerlambat = $transaksi->tanggal_kembali_rencana->diffInDays($tanggalKembaliAktual);
                }
                $denda = $hariTerlambat * Transaksi::DENDA_PER_HARI;

                $transaksi->update([
                    'tanggal_kembali_aktual' => $tanggalKembaliAktual,
                    'status' => 'Dikembalikan',
                    'denda' => $denda,
                ]);

                // Stok buku bertambah 1 saat dikembalikan
                $transaksi->buku()->increment('stok');
            });

            $transaksi->refresh();

            $message = $transaksi->denda > 0
                ? 'Buku berhasil dikembalikan. Denda terlambat: Rp ' . number_format($transaksi->denda, 0, ',', '.')
                : 'Buku berhasil dikembalikan tepat waktu.';

            return redirect()->route('transaksi.show', $transaksi->id)->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }

    /** Laporan transaksi dengan filter tanggal, status, dan anggota. */
    public function laporan(Request $request)
    {
        $transaksis = $this->filteredLaporanQuery($request)->latest()->get();

        $totalTransaksi = $transaksis->count();
        $totalDenda = $transaksis->sum('denda');
        $anggotaList = Anggota::orderBy('nama')->get();

        return view('transaksi.laporan', compact('transaksis', 'totalTransaksi', 'totalDenda', 'anggotaList'));
    }

    /**
     * Export laporan ke PDF.
     */
    public function laporanPdf(Request $request)
    {
        $transaksis = $this->filteredLaporanQuery($request)->latest()->get();

        $totalTransaksi = $transaksis->count();
        $totalDenda = $transaksis->sum('denda');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transaksi.laporan_pdf', compact(
            'transaksis', 'totalTransaksi', 'totalDenda'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-transaksi-' . now()->format('Y-m-d_His') . '.pdf');
    }

    private function filteredLaporanQuery(Request $request)
    {
        $query = Transaksi::with(['buku', 'anggota']);

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->sampai_tanggal);
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('anggota_id')) {
            $query->where('anggota_id', $request->anggota_id);
        }

        return $query;
    }

    private function generateKodeTransaksi(): string
    {
        $tanggal = now()->format('Ymd');
        $jumlahHariIni = Transaksi::whereDate('created_at', now())->count() + 1;

        return 'TRX-' . $tanggal . '-' . str_pad((string) $jumlahHariIni, 3, '0', STR_PAD_LEFT);
    }
}

