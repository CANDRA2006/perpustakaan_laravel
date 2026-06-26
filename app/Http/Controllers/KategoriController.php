<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    /**
     * Tampilkan daftar kategori (dengan jumlah buku per kategori).
     */
    public function index(Request $request)
    {
        $query = Kategori::withCount('bukus');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_kategori', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%");
            });
        }

        $kategoris = $query->orderBy('nama_kategori')->get();
        $keyword = $request->keyword;
        $totalBukuKeseluruhan = Buku::count();

        return view('kategori.index', compact('kategoris', 'keyword', 'totalBukuKeseluruhan'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:50', 'unique:kategori,nama_kategori'],
            'deskripsi' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'warna' => ['nullable', 'string', 'max:20'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Kategori dengan nama ini sudah ada.',
        ]);

        Kategori::create($validated);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function show(string $id)
    {
        $kategori = Kategori::withCount('bukus')->findOrFail($id);
        $bukus = $kategori->bukus()->latest()->get();

        return view('kategori.show', compact('kategori', 'bukus'));
    }

    public function edit(string $id)
    {
        $kategori = Kategori::findOrFail($id);

        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, string $id)
    {
        $kategori = Kategori::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:50', Rule::unique('kategori', 'nama_kategori')->ignore($kategori->id)],
            'deskripsi' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'warna' => ['nullable', 'string', 'max:20'],
        ]);

        $namaLama = $kategori->nama_kategori;
        $kategori->update($validated);

        // Jaga konsistensi label string kategori pada tabel buku jika nama berubah.
        if ($namaLama !== $kategori->nama_kategori) {
            Buku::where('kategori_id', $kategori->id)->update(['kategori' => $kategori->nama_kategori]);
        }

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diupdate!');
    }

    public function destroy(string $id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->bukus()->count() > 0) {
            return redirect()->route('kategori.index')
                ->with('error', "Kategori '{$kategori->nama_kategori}' tidak dapat dihapus karena masih memiliki " . $kategori->bukus()->count() . ' buku terkait.');
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', "Kategori '{$nama}' berhasil dihapus!");
    }
}
