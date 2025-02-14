<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusFollowUp extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama',
    ];
    public function saldoCustomer(): HasMany
    {
        return $this->hasMany(SaldoCustomer::class);
    }
}
