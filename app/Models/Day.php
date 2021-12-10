<?php

namespace App\Models;

use App\Services\Finances\Casts\MoneyBagCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Day extends Model
{
    use HasFactory;

    protected $casts = [
        'price' => MoneyBagCast::class,
        'date' => 'date'
    ];

    protected $guarded = ['id'];

}
