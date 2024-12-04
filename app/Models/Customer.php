<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama',
        'nomor_hp',
        'email',
    ];
    public function saldoCustomer(): HasMany
    {
        return $this->hasMany(SaldoCustomer::class);
    }
    public function totalSaldo(): HasMany
    {
        return $this->hasMany(TotalSaldo::class);
    }
}
