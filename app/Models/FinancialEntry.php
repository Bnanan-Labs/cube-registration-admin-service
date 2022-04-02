<?php

namespace App\Models;

use App\Enums\FinancialEntryType;
use App\Services\Finances\Casts\MoneyBagCast;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class FinancialEntry extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['id'];

    protected $casts = [
        'balance' => MoneyBagCast::class,
        'type' => FinancialEntryType::class,
    ];

    /**
     * @return BelongsTo
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(FinancialBook::class, 'book_id');
    }
}
