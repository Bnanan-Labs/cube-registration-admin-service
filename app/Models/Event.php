<?php

namespace App\Models;

use App\Services\Finances\Casts\MoneyBagCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Event extends Model
{
    use HasFactory;

    protected $casts = [
        'fee' => MoneyBagCast::class,
    ];

    /**
     * @return BelongsToMany
     */
    public function competitors(): BelongsToMany
    {
        return $this->belongsToMany(Competitor::class);
    }

    /**
     * @return BelongsToMany
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class);
    }
}
