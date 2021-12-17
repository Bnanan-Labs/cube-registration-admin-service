<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Spectator extends Model
{
    use HasFactory;

    protected $casts = [
        'registration_status' => RegistrationStatus::class,
        'payment_status' => PaymentStatus::class,
    ];

    /**
     * @return BelongsToMany
     */
    public function days(): BelongsToMany
    {
        return $this->belongsToMany(Day::class);
    }
}
