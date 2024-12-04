<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaldoCustomer extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'tipe_transaksi_id',
        'nomor',
        'tanggal',
        'masa',
        'deskripsi',
        'nilai',
        'nilai_jatuh_tempo',
        'status_follow_up_id',
        'catatan',
    ];
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function tipeTransaksi(): BelongsTo
    {
        return $this->belongsTo(TipeTransaksi::class);
    }
    public function statusFollowUp(): BelongsTo
    {
        return $this->belongsTo(StatusFollowUp::class);
    }
    public function totalSaldo(): HasMany
    {
        return $this->hasMany(TotalSaldo::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($saldoCustomer) {
            // Mendapatkan tahun dari `tanggal` jika di-set, atau tahun saat ini
            $year = $saldoCustomer->tanggal
                ? \Carbon\Carbon::parse($saldoCustomer->tanggal)->format('Y')
                : now()->year;

            // Konversi ke format dua digit terakhir tahun
            $currentYear = substr($year, -2); // Contoh: "2022" -> "22"

            // Cari entri terakhir dalam tahun yang sama berdasarkan tanggal
            $lastSaldoCustomer = self::whereYear('tanggal', $year) // Filter berdasarkan tahun di kolom `tanggal`
                ->orderBy('created_at', 'desc') // Urutkan berdasarkan tanggal pembuatan terbaru
                ->orderBy('id', 'desc')        // Jika tanggal sama, urutkan berdasarkan ID
                ->first();

            // Ambil nomor terakhir, jika ada, atau mulai dari 0
            $lastNumber = 0;
            if ($lastSaldoCustomer && str_contains($lastSaldoCustomer->nomor, '.')) {
                $lastNumber = intval(explode('.', $lastSaldoCustomer->nomor)[1]);
            }

            // Format nomor baru menjadi 5 digit (contoh: "00001")
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);

            // Gabungkan tahun dan nomor baru menjadi format yang diinginkan
            $saldoCustomer->nomor = "{$currentYear}.{$newNumber}";
        });
    }
}
