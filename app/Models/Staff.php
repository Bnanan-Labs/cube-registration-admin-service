<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use App\Enums\ShirtSize;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Staff extends Model
{
    use HasFactory, Uuid;

    protected $casts = [
        'registration_status' => RegistrationStatus::class,
        't_shirt_size' => ShirtSize::class,
    ];

    /**
     * @return BelongsToMany
     */
    public function scrambling_qualifications(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->wherePivot('can_scramble', true);
    }

    /**
     * @return BelongsToMany
     */
    public function availability(): BelongsToMany
    {
        return $this->BelongsToMany(Day::class);
    }

    /**
     * @return BelongsToMany
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class);
    }

    /**
     * @return BelongsToMany
     */
    public function priority_events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->wherePivot('is_priority', true);
    }

    /**
     * @return BelongsToMany
     */
    public function staff_roles(): BelongsToMany
    {
        return $this->belongsToMany(StaffRole::class, 'staff_role_staff');
    }

    /**
     * @todo
     *
     * @return HasMany
     */
//    public function approvals(): HasMany
//    {
//        return $this->hasMany(self::class);
//    }

    /**
     * @return BelongsTo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return HasMany
     */
    public function teams_lead(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
