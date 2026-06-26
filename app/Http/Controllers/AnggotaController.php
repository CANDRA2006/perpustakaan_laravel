<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Http\Requests\StoreAnggotaRequest;
use App\Http\Requests\UpdateAnggotaRequest;
use App\Exports\AnggotaExport;
use Maatwebsite\Excel\Facades\Excel;

class AnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $anggotas = Anggota::latest()->get();

        // Statistik
        $totalAnggota = Anggota::count();
        $anggotaAktif = Anggota::where('status', 'Aktif')->count();
        $anggotaNonaktif = Anggota::where('status', 'Nonaktif')->count();

        return view('anggota.index', compact(
            'anggotas',
            'totalAnggota',
            'anggotaAktif',
            'anggotaNonaktif'
        ));
    }

    public function search(Request $request)
    {
        $query = Anggota::query();

        if ($request->keyword) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->keyword . '%')
                  ->orWhere('email', 'like', '%' . $request->keyword . '%')
                  ->orWhere('telepon', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->jenis_kelamin) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->pekerjaan) {
            $query->where('pekerjaan', $request->pekerjaan);
        }

        // Advanced search: filter range umur (dihitung dari tanggal_lahir)
        if ($request->filled('umur_min')) {
            $batasTanggal = now()->subYears((int) $request->umur_min)->format('Y-m-d');
            $query->whereDate('tanggal_lahir', '<=', $batasTanggal);
        }

        if ($request->filled('umur_max')) {
            $batasTanggal = now()->subYears((int) $request->umur_max + 1)->addDay()->format('Y-m-d');
            $query->whereDate('tanggal_lahir', '>=', $batasTanggal);
        }

        $anggotas = $query->latest()->get();

        // Statistics
        $totalAnggota = $anggotas->count();
        $anggotaAktif = $anggotas->where('status', 'Aktif')->count();
        $anggotaNonaktif = $anggotas->where('status', 'Nonaktif')->count();

        return view('anggota.index', compact(
            'anggotas',
            'totalAnggota',
            'anggotaAktif',
            'anggotaNonaktif'
        ));
    }

    public function export()
    {
        return Excel::download(new AnggotaExport, 'anggota_' . date('Y-m-d_His') . '.xlsx');
    }

    /**
     * Display the specified resource, termasuk riwayat peminjaman.
     */
    public function show(string $id)
    {
        $anggota = Anggota::findOrFail($id);

        $riwayatQuery = $anggota->transaksis()->with('buku')->latest();

        if (request()->filled('status_riwayat') && request('status_riwayat') !== 'Semua') {
            $riwayatQuery->where('status', request('status_riwayat'));
        }

        $riwayatPeminjaman = $riwayatQuery->get();

        $totalPinjam = $anggota->transaksis()->count();
        $totalDenda = $anggota->transaksis()->sum('denda');
        $sedangDipinjam = $anggota->transaksis()->where('status', 'Dipinjam')->count();
        $statusRiwayat = request('status_riwayat', 'Semua');

        return view('anggota.show', compact(
            'anggota',
            'riwayatPeminjaman',
            'totalPinjam',
            'totalDenda',
            'sedangDipinjam',
            'statusRiwayat'
        ));
    }

    private function generateKodeAnggota()
    {
        $tahun = date('Y');
        $lastAnggota = Anggota::whereYear('created_at', $tahun)
                              ->orderBy('kode_anggota', 'desc')
                              ->first();

        if ($lastAnggota) {
            $lastNumber = intval(substr($lastAnggota->kode_anggota, -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'AGT-' . $tahun . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function create()
    {
        $kodeAnggota = $this->generateKodeAnggota();
        return view('anggota.create', compact('kodeAnggota'));
    }

    public function store(StoreAnggotaRequest $request)
    {
        try {
            Anggota::create($request->validated());

            return redirect()->route('anggota.index')
                ->with('success', 'Anggota berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan anggota: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $anggota = Anggota::findOrFail($id);
        return view('anggota.edit', compact('anggota'));
    }

    public function update(UpdateAnggotaRequest $request, string $id)
    {
        try {
            $anggota = Anggota::findOrFail($id);

            $anggota->update($request->validated());

            return redirect()->route('anggota.show', $anggota->id)
                ->with('success', 'Data anggota berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate anggota: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $anggota = Anggota::findOrFail($id);
            $namaAnggota = $anggota->nama;

            $anggota->delete();

            return redirect()->route('anggota.index')
                ->with('success', "Anggota '{$namaAnggota}' berhasil dihapus!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus anggota: ' . $e->getMessage());
        }
    }
}
