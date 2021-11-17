<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Competitor;
use App\Models\Event;
use App\Models\Spectator;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Competition extends Model
{
    use HasFactory;

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
}
