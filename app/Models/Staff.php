<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Staff extends Model
{
    use HasFactory;

    protected $casts = [
        'registration_status' => RegistrationStatus::class,
    ];

    /**
     * @return HasMany
     */
    public function scrambling_qualifications(): HasMany
    {
        return $this->hasMany(Event::class, 'scrambling_qualification_id');
    }

    /**
     * @return HasMany
     */
    public function availability(): HasMany
    {
        return $this->hasMany(Day::class, 'availability_id');
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
    public function priority_events(): HasMany
    {
        return $this->hasMany(Event::class, 'priority_event_id');
    }

    /**
     * @return HasMany
     */
    public function staff_roles(): HasMany
    {
        return $this->hasMany(StaffRole::class);
    }

    /**
     * @return HasMany
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(self::class, 'approval_id');
    }

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
        return $this->hasMany(Team::class, 'teams_lead_id');
    }
}
