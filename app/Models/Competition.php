<?php

namespace App\Models;

use App\Services\Finances\Casts\MoneyBagCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Competition extends Model
{
    use HasFactory;

    protected $casts = [
        'base_fee' => MoneyBagCast::class,
        'is_active' => 'boolean',
    ];

    /**
     * @return HasMany
     */
    public function competitors(): HasMany
    {
        return $this->hasMany(Competitor::class);
    }

    /**
     * @return HasMany
     */
    public function spectators(): HasMany
    {
        return $this->hasMany(Spectator::class);
    }

    /**
     * @return HasMany
     */
    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    /**
     * @return HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return HasMany
     */
    public function days(): HasMany
    {
        return $this->hasMany(Day::class);
    }

    /**
     * @return BelongsTo
     */
    public function finances(): BelongsTo
    {
        return $this->belongsTo(FinancialBook::class, 'financial_book_id');
    }
}