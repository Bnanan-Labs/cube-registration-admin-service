<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FinancialEntry;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Relations\HasMany;


class FinancialBook extends Model
{
    use HasFactory;

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
