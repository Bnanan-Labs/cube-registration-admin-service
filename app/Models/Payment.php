<?php

namespace App\Models;

use App\Enums\FinancialEntryType;
use App\Services\Finances\Casts\MoneyBagCast;
use App\Services\Finances\MoneyBag;
use App\Traits\Uuid;
use GraphQL\Error\Error;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stripe\Refund;
use Stripe\Stripe;


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

    /**
     * @param int $amount
     * @return void
     * @throws Error
     */
    public function refund(int $amount)
    {
        if ($amount > $this->total->amount) {
            throw new Error('Cannot refund more than payment value');
        }
        if (!$amount) {
            throw new Error('Cannot refund 0');
        }

        $competitor = Competitor::where('financial_book_id', '=', $this->financial_book_id)->first();
        Stripe::setApiKey($competitor->competition->stripe_api_key);
        $intent = Refund::create([
            'payment_intent' => $this->intent_id,
            'amount' => $amount,
        ]);

        $this->book->entries()->create([
            'type' => FinancialEntryType::refund,
            'balance' => new MoneyBag(-$amount),
            'title' => 'Stripe Refund',
            'booked_at' => now(),
        ]);
        $this->refunded_at = now();
        $this->refunded_amount = $amount;
        $this->refund_id = $intent->id;
        $this->save();
    }

    public function cancel()
    {
        $this->delete();
    }

    /**
     * @param Builder $query
     * @return void
     */
    public function scopePaid(Builder $query): void
    {
        $query->whereNotNull('captured_at');
    }
}
