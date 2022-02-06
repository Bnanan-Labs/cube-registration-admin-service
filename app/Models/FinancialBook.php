<?php

namespace App\Models;

use App\Services\Finances\Casts\MoneyBagCast;
use App\Services\Finances\MoneyBag;
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
        return $this->hasMany(FinancialEntry::class, 'financial_book_id')->orderByDesc('created_at');
    }

    /**
     * @return MoneyBag
     * @throws \Exception
     */
    public function getBalanceAttribute(): MoneyBag
    {
        $balance = new MoneyBag();
        foreach ($this->entries as $entry) {
            $balance->add($entry->balance);
        }

        return $balance;
    }
}
