<?php

namespace App\Models;

use App\Services\Finances\Casts\MoneyBagCast;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Day extends Model
{
    use HasFactory, Uuid;

    protected $casts = [
        'price' => MoneyBagCast::class,
        'date' => 'date'
    ];

    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}
