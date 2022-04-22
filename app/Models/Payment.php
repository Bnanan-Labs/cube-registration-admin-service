<?php

namespace App\Models;

use App\Services\Finances\Casts\MoneyBagCast;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{
    use HasFactory, Uuid;

    protected $guarded = [];

    protected $casts = [
        'total' => MoneyBagCast::class,
    ];
}
