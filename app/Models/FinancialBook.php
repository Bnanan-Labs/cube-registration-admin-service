<?php

namespace App\Models;

use App\Enums\FinancialEntryType;
use App\Services\Finances\Casts\MoneyBagCast;
use App\Services\Finances\MoneyBag;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class FinancialBook extends Model
{
    use HasFactory, Uuid;

    protected $casts = [
        'balance' => MoneyBagCast::class,
        'paid' => MoneyBagCast::class,
        'total' => MoneyBagCast::class,
        'refunded' => MoneyBagCast::class,
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
        return $this->hasMany(FinancialEntry::class, 'financial_book_id')->orderBy('booked_at');
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

    /**
     * @return MoneyBag
     * @throws \Exception
     */
    public function getTotalAttribute(): MoneyBag
    {
        $balance = new MoneyBag();
        $typesToInclude = [
            FinancialEntryType::baseFee->value,
            FinancialEntryType::eventFee->value,
            FinancialEntryType::guestFee->value,
            FinancialEntryType::discount->value,
        ];

        foreach ($this->entries()->whereIn('type', $typesToInclude)->get() as $entry) {
            $balance->add($entry->balance);
        }

        return $balance;
    }

    /**
     * @return MoneyBag
     * @throws \Exception
     */
    public function getPaidAttribute(): MoneyBag
    {
        $balance = new MoneyBag();

        foreach ($this->entries()->where('type', FinancialEntryType::payment->value)->get() as $entry) {
            $balance->add($entry->balance);
        }

        return $balance;
    }

    /**
     * @return MoneyBag
     * @throws \Exception
     */
    public function getRefundedAttribute(): MoneyBag
    {
        $balance = new MoneyBag();

        foreach ($this->entries()->where('type', FinancialEntryType::refund->value)->get() as $entry) {
            $balance->add($entry->balance);
        }

        return $balance;
    }

    public function reset(): FinancialBook
    {
        $typesToInclude = [
            FinancialEntryType::baseFee->value,
            FinancialEntryType::eventFee->value,
            FinancialEntryType::guestFee->value,
            FinancialEntryType::discount->value,
        ];

        foreach ($this->entries()->whereIn('type', $typesToInclude)->get() as $entry) {
            $entry->delete();
        }

        $this->refresh();

        return $this;
    }
}
