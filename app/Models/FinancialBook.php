<?php

namespace App\Models;

use App\Services\Finances\Casts\MoneyBagCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class FinancialBook extends Model
{
    use HasFactory;

    protected $casts = [
        'balance' => MoneyBagCast::class,
        'paid' => MoneyBagCast::class,
        'total' => MoneyBagCast::class,
    ];

    /**
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasMany
     */
    public function entries(): HasMany
    {
        return $this->hasMany(FinancialEntry::class, 'entry_id');
    }
}
