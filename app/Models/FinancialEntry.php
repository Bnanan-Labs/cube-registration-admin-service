<?php

namespace App\Models;

use App\Services\Finances\Casts\MoneyBagCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class FinancialEntry extends Model
{
    use HasFactory;

    protected $casts = [
        'balance' => MoneyBagCast::class,
    ];

    /**
     * @return BelongsTo
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(FinancialBook::class, 'book_id');
    }
}
