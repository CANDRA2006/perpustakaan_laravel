<?php

namespace App\Http\Controllers;

use App\Exports\BukuExport;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data buku dari database
        $bukus = Buku::latest()->get();

        // Statistik untuk card
        $totalBuku = Buku::count();
        $bukuTersedia = Buku::where('stok', '>', 0)->count();
        $bukuHabis = Buku::where('stok', 0)->count();

        $tahunList = Buku::select('tahun_terbit')->distinct()->orderByDesc('tahun_terbit')->pluck('tahun_terbit');
        $kategoriOptions = Kategori::orderBy('nama_kategori')->pluck('nama_kategori');

        // Return view dengan data
        return view('buku.index', compact(
            'bukus',
            'totalBuku',
            'bukuTersedia',
            'bukuHabis',
            'tahunList',
            'kategoriOptions'
        ));
    }

    /**
     * Search and advanced filter for buku.
     */
    public function search(Request $request)
    {
        $query = Buku::query();

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('judul', 'like', "%{$kw}%")
                    ->orWhere('pengarang', 'like', "%{$kw}%")
                    ->orWhere('penerbit', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_terbit', $request->tahun);
        }

        if ($request->filled('ketersediaan')) {
            if ($request->ketersediaan === 'tersedia') {
                $query->where('stok', '>', 0);
            } elseif ($request->ketersediaan === 'habis') {
                $query->where('stok', 0);
            }
        }

        // Advanced search: filter range harga
        if ($request->filled('harga_min')) {
            $query->where('harga', '>=', (float) $request->harga_min);
        }

        if ($request->filled('harga_max')) {
            $query->where('harga', '<=', (float) $request->harga_max);
        }

        $bukus = $query->latest()->get();

        $totalBuku = $bukus->count();
        $bukuTersedia = $bukus->where('stok', '>', 0)->count();
        $bukuHabis = $bukus->where('stok', 0)->count();

        $tahunList = Buku::select('tahun_terbit')->distinct()->orderByDesc('tahun_terbit')->pluck('tahun_terbit');
        $kategoriOptions = Kategori::orderBy('nama_kategori')->pluck('nama_kategori');

        $kategori = $request->kategori ?? null;
        $tahun = $request->tahun ?? null;
        $ketersediaan = $request->ketersediaan ?? null;
        $keyword = $request->keyword ?? null;
        $hargaMin = $request->harga_min ?? null;
        $hargaMax = $request->harga_max ?? null;

        return view('buku.index', compact(
            'bukus',
            'totalBuku',
            'bukuTersedia',
            'bukuHabis',
            'tahunList',
            'kategoriOptions',
            'kategori',
            'tahun',
            'ketersediaan',
            'keyword',
            'hargaMin',
            'hargaMax'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoriOptions = Kategori::orderBy('nama_kategori')->pluck('nama_kategori');

        return view('buku.create', compact('kategoriOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBukuRequest $request)
    {
        try {
            $validated = $request->validated();

            // Pastikan relasi belongsTo ke tabel kategori tetap konsisten.
            $validated['kategori_id'] = Kategori::firstOrCreate(
                ['nama_kategori' => $validated['kategori']]
            )->id;

            Buku::create($validated);

            return redirect()->route('buku.index')
                ->with('success', 'Buku berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan buku: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $buku = Buku::findOrFail($id);

        return view('buku.show', compact('buku'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $buku = Buku::findOrFail($id);
        $kategoriOptions = Kategori::orderBy('nama_kategori')->pluck('nama_kategori');

        return view('buku.edit', compact('buku', 'kategoriOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBukuRequest $request, string $id)
    {
        try {
            $buku = Buku::findOrFail($id);

            $validated = $request->validated();
            $validated['kategori_id'] = Kategori::firstOrCreate(
                ['nama_kategori' => $validated['kategori']]
            )->id;

            $buku->update($validated);

            return redirect()->route('buku.show', $buku->id)
                ->with('success', 'Buku berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate buku: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $buku = Buku::findOrFail($id);
            $judulBuku = $buku->judul;

            $buku->delete();

            return redirect()->route('buku.index')
                ->with('success', "Buku '{$judulBuku}' berhasil dihapus!");
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return redirect()->route('buku.index')
                    ->with('error', 'Data buku tidak ditemukan atau sudah dihapus.');
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus buku: ' . $e->getMessage());
        }
    }

    /**
     * Filter buku berdasarkan kategori.
     */
    public function filterKategori($kategori)
    {
        $bukus = Buku::where('kategori', $kategori)->latest()->get();

        $totalBuku = $bukus->count();
        $bukuTersedia = $bukus->where('stok', '>', 0)->count();
        $bukuHabis = $bukus->where('stok', 0)->count();

        return view('buku.index', compact(
            'bukus',
            'totalBuku',
            'bukuTersedia',
            'bukuHabis',
            'kategori'
        ));
    }

    /**
     * Delete multiple books at once.
     */
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('buku_ids', []);

            if (empty($ids)) {
                return redirect()->route('buku.index')
                    ->with('error', 'Pilih minimal satu buku untuk dihapus.');
            }

            $validIds = array_filter(array_map('intval', $ids), function ($id) {
                return $id > 0;
            });

            if (empty($validIds)) {
                return redirect()->route('buku.index')
                    ->with('error', 'ID buku tidak valid.');
            }

            $deleted = Buku::whereIn('id', $validIds)->delete();

            return redirect()->route('buku.index')
                ->with('success', $deleted . ' buku berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus buku: ' . $e->getMessage());
        }
    }

    /**
     * Export books data to an Excel (.xlsx) file.
     */
    public function export()
    {
        return Excel::download(new BukuExport, 'buku_' . date('Y-m-d_His') . '.xlsx');
    }
}
