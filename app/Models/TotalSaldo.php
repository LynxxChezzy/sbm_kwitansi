<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TotalSaldo extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'saldo_customer_id',
        'total_nilai',
        'total_saldo',
    ];
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function saldoCustomer(): BelongsTo
    {
        return $this->belongsTo(SaldoCustomer::class);
    }
}
