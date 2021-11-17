<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Day;
use App\Models\Event;
use App\Models\StaffRole;
use App\Models\Team;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Staff extends Model
{
    use HasFactory;

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
