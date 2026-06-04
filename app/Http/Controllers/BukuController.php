<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Buku;
use App\Rules\KodeBukuFormat;

class BukuController extends Controller
{
    public function index()
    {
        $bukus = Buku::latest()->get();

        $totalBuku = $bukus->count();
        $bukuTersedia = $bukus->where('stok', '>', 0)->count();
        $bukuHabis = $bukus->where('stok', 0)->count();

        $kategoris = Buku::select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $tahuns = Buku::select('tahun_terbit')
            ->distinct()
            ->orderBy('tahun_terbit', 'desc')
            ->pluck('tahun_terbit');

        return view('buku.index', compact(
            'bukus',
            'totalBuku',
            'bukuTersedia',
            'bukuHabis',
            'kategoris',
            'tahuns'
        ));
        return $this->renderIndex(Buku::query());
    }

    public function kategori($kategori)
    {
        $bukus = Buku::where('kategori', $kategori)
            ->latest()
            ->get();

        $totalBuku = $bukus->count();
        $bukuTersedia = $bukus->where('stok', '>', 0)->count();
        $bukuHabis = $bukus->where('stok', 0)->count();

        $kategoris = Buku::select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $tahuns = Buku::select('tahun_terbit')
            ->distinct()
            ->orderBy('tahun_terbit', 'desc')
            ->pluck('tahun_terbit');

        return view('buku.index', compact(
            'bukus',
            'totalBuku',
            'bukuTersedia',
            'bukuHabis',
            'kategoris',
            'tahuns'
        ));
        return $this->renderIndex(
            Buku::where('kategori', $kategori),
            [
                'kategori' => $kategori,
                'searchInput' => ['kategori' => $kategori],
            ]
        );
    }

    public function search(Request $request)
    {
        $query = Buku::query();
        $searchInput = $request->only(['keyword', 'kategori', 'tahun', 'stok']);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                ->orWhere('pengarang', 'like', "%{$keyword}%")
                ->orWhere('penerbit', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_terbit', $request->tahun);
        }

        if ($request->filled('stok') && $request->stok !== 'semua') {

            if ($request->stok === 'tersedia') {
                $query->where('stok', '>', 0);
            }

            if ($request->stok === 'habis') {
                $query->where('stok', 0);
            }
        }

        return $this->renderIndex($query, [
            'kategori' => $request->filled('kategori') ? $request->kategori : null,
            'searchInput' => $searchInput,
        ]);
    }
    public function create()
    {
        return view('buku.create');
    }

    public function store(Request $request)
    {
        //
        $validated = $request->validate(
            $this->validationRules($request),
            $this->validationMessages()
        );

        Buku::create($validated);

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    public function show(string $id)
    {
        $buku = Buku::findOrFail($id);

        return view('buku.show', compact('buku'));
    }

    public function edit(string $id)
    {
        $buku = Buku::findOrFail($id);

        return view('buku.edit', compact('buku'));
    }

    public function update(Request $request, string $id)
    {
        //
        $buku = Buku::findOrFail($id);

        $validated = $request->validate(
            $this->validationRules($request, $buku->id),
            $this->validationMessages()
        );

        $buku->update($validated);

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        //
        $buku = Buku::findOrFail($id);
        $buku->delete();

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil dihapus!');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'buku_ids' => ['required', 'array', 'min:1'],
            'buku_ids.*' => ['integer', 'exists:buku,id'],
        ], [
            'buku_ids.required' => 'Pilih minimal satu buku yang ingin dihapus.',
            'buku_ids.array' => 'Pilihan buku tidak valid.',
            'buku_ids.min' => 'Pilih minimal satu buku yang ingin dihapus.',
            'buku_ids.*.integer' => 'ID buku harus berupa angka.',
            'buku_ids.*.exists' => 'Buku yang dipilih tidak ditemukan.',
        ]);

        $ids = $validated['buku_ids'];
        Buku::whereIn('id', $ids)->delete();

        return redirect()->route('buku.index')
            ->with('success', count($ids) . ' buku berhasil dihapus!');
    }

    public function export()
    {
        $bukus = Buku::orderBy('judul')->get();
        $filename = 'buku_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($bukus) {
            $file = fopen('php://output', 'w');

            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, [
                'Kode Buku',
                'Judul',
                'Kategori',
                'Pengarang',
                'Penerbit',
                'Tahun',
                'ISBN',
                'Harga',
                'Stok',
            ]);

            foreach ($bukus as $buku) {
                fputcsv($file, [
                    $buku->kode_buku,
                    $buku->judul,
                    $buku->kategori,
                    $buku->pengarang,
                    $buku->penerbit,
                    $buku->tahun_terbit,
                    $buku->isbn,
                    $buku->harga,
                    $buku->stok,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function renderIndex($query, array $data = [])
    {
        $bukus = $query->latest()->get();

        $kategoris = Buku::select('kategori')
            ->whereNotNull('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $tahuns = Buku::select('tahun_terbit')
            ->whereNotNull('tahun_terbit')
            ->distinct()
            ->orderBy('tahun_terbit', 'desc')
            ->pluck('tahun_terbit');

        return view('buku.index', array_merge([
            'bukus' => $bukus,
            'totalBuku' => $bukus->count(),
            'bukuTersedia' => $bukus->where('stok', '>', 0)->count(),
            'bukuHabis' => $bukus->where('stok', 0)->count(),
            'kategoris' => $kategoris,
            'tahuns' => $tahuns,
            'searchInput' => [],
        ], array_filter($data, fn ($value) => $value !== null)));
    }

    private function validationRules(Request $request, ?int $bukuId = null): array
    {
        $maxCurrentYear = date('Y');
        $bahasaRules = ['required', 'string', 'max:50'];
        $stokRules = ['required', 'integer', 'min:0'];

        if ($request->input('kategori') === 'Programming') {
            $bahasaRules[] = 'in:Inggris';
        }

        if ($request->filled('tahun_terbit') && (int) $request->input('tahun_terbit') < 2000) {
            $stokRules[] = 'max:5';
        }

        return [
            'kode_buku' => [
                'required',
                'string',
                new KodeBukuFormat(),
                Rule::unique('buku', 'kode_buku')->ignore($bukuId),
            ],
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'bahasa' => $bahasaRules,
            'pengarang' => ['required', 'string', 'max:255'],
            'penerbit' => ['required', 'string', 'max:255'],
            'tahun_terbit' => ['required', 'integer', 'digits:4', 'min:1900', 'max:' . $maxCurrentYear],
            'isbn' => ['nullable', 'string', 'max:30'],
            'harga' => ['required', 'integer', 'min:0'],
            'stok' => $stokRules,
            'deskripsi' => ['nullable', 'string'],
            'kategori_id' => ['nullable', 'integer', 'exists:kategori,id'],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'kode_buku.required' => 'Kode buku wajib diisi.',
            'kode_buku.unique' => 'Kode buku sudah digunakan.',
            'judul.required' => 'Judul buku wajib diisi.',
            'judul.max' => 'Judul buku maksimal :max karakter.',
            'kategori.required' => 'Kategori buku wajib dipilih.',
            'kategori.max' => 'Kategori buku maksimal :max karakter.',
            'bahasa.required' => 'Bahasa buku wajib diisi.',
            'bahasa.in' => 'Jika kategori Programming, bahasa harus Inggris.',
            'bahasa.max' => 'Bahasa buku maksimal :max karakter.',
            'pengarang.required' => 'Nama pengarang wajib diisi.',
            'pengarang.max' => 'Nama pengarang maksimal :max karakter.',
            'penerbit.required' => 'Nama penerbit wajib diisi.',
            'penerbit.max' => 'Nama penerbit maksimal :max karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka.',
            'tahun_terbit.digits' => 'Tahun terbit harus terdiri dari :digits digit.',
            'tahun_terbit.min' => 'Tahun terbit tidak boleh kurang dari :min.',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh melebihi :max.',
            'isbn.max' => 'ISBN maksimal :max karakter.',
            'harga.required' => 'Harga buku wajib diisi.',
            'harga.integer' => 'Harga buku harus berupa angka.',
            'harga.min' => 'Harga buku tidak boleh kurang dari :min.',
            'stok.required' => 'Stok buku wajib diisi.',
            'stok.integer' => 'Stok buku harus berupa angka.',
            'stok.min' => 'Stok buku tidak boleh kurang dari :min.',
            'stok.max' => 'Jika tahun terbit kurang dari 2000, stok maksimal 5 buku.',
            'deskripsi.string' => 'Deskripsi buku harus berupa teks.',
            'kategori_id.integer' => 'ID kategori harus berupa angka.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak ditemukan.',
        ];
    }
}
