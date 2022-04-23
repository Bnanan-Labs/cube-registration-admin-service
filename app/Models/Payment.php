<?php

namespace App\Models;

use App\Enums\FinancialEntryType;
use App\Services\Finances\Casts\MoneyBagCast;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Payment extends Model
{
    use HasFactory, Uuid;

    protected $guarded = [];

    protected $casts = [
        'total' => MoneyBagCast::class,
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(FinancialBook::class, 'financial_book_id');
    }

    public function markAsPaid()
    {
        $this->captured_at = now();
        $this->book->entries()->create([
            'type' => FinancialEntryType::payment,
            'balance' => $this->total,
            'title' => 'Stripe Payment',
            'booked_at' => now(),
        ]);
        $this->booked_at = now();
        $this->save();
    }

    public function cancel()
    {
        $this->delete();
    }
}
