<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #444; padding: 6px; }
        th { background-color: #eee; }
        .text-end { text-align: right; }
        .summary { margin-top: 15px; }
    </style>
</head>
<body>
    <h2>Laporan Transaksi Perpustakaan</h2>
    <p style="text-align:center;">Dicetak pada: {{ now()->format('d F Y H:i') }}</p>

    <table>
        <thead>
            <tr><th>Kode</th><th>Buku</th><th>Anggota</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th><th class="text-end">Denda</th></tr>
        </thead>
        <tbody>
            @forelse ($transaksis as $t)
                <tr>
                    <td>{{ $t->kode_transaksi }}</td>
                    <td>{{ $t->buku->judul }}</td>
                    <td>{{ $t->anggota->nama }}</td>
                    <td>{{ $t->tanggal_pinjam->format('d/m/Y') }}</td>
                    <td>{{ $t->tanggal_kembali_aktual ? $t->tanggal_kembali_aktual->format('d/m/Y') : '-' }}</td>
                    <td>{{ $t->status }}</td>
                    <td class="text-end">Rp {{ number_format($t->denda, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Transaksi:</strong> {{ $totalTransaksi }}</p>
        <p><strong>Total Denda:</strong> Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
    </div>
</body>
</html>
