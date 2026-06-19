<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    const DENDA_PER_HARI = 5000;

    protected $fillable = [
        'kode_transaksi',
        'buku_id',
        'anggota_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'status',
        'denda',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual' => 'date',
        'denda' => 'integer',
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    /** Apakah transaksi ini terlambat (masih dipinjam & lewat tanggal rencana). */
    public function getIsTerlambatAttribute(): bool
    {
        if ($this->status === 'Dikembalikan') {
            return false;
        }

        return Carbon::today()->gt($this->tanggal_kembali_rencana);
    }

    /** Jumlah hari terlambat (dihitung sampai hari ini, atau tanggal kembali aktual jika sudah dikembalikan). */
    public function getHariTerlambatAttribute(): int
    {
        $pembanding = $this->status === 'Dikembalikan'
            ? $this->tanggal_kembali_aktual
            : Carbon::today();

        if (! $pembanding || $pembanding->lte($this->tanggal_kembali_rencana)) {
            return 0;
        }

        return (int) $this->tanggal_kembali_rencana->diffInDays($pembanding);
    }

    /** Estimasi denda berjalan (untuk yang masih dipinjam & terlambat). */
    public function getDendaEstimasiAttribute(): int
    {
        return $this->hari_terlambat * self::DENDA_PER_HARI;
    }

    public function scopeDipinjam($query)
    {
        return $query->where('status', 'Dipinjam');
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'Dipinjam')
            ->whereDate('tanggal_kembali_rencana', '<', Carbon::today());
    }
}
