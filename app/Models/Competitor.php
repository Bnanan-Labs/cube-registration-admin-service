<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;


class Competitor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_delegate' => 'boolean',
        'is_interested_in_nations_cup' => 'boolean',
    ];

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
    public function days(): BelongsToMany
    {
        return $this->belongsToMany(Day::class);
    }

    /**
     * @param array $teams
     */
    public function setWcaTeamsAttribute(array $teams): void
    {
        $this->attributes['wca_teams'] = collect($teams)->join(',');
    }

    /**
     * @param $value
     * @return Collection
     */
    public function getWcaTeamsAttribute($value): Collection
    {
        if (!$value) {
            return collect([]);
        }

        return collect(explode(',', $value));
    }

    /**
     * @param array $guests
     */
    public function setGuestsAttribute(array $guests): void
    {
        $this->attributes['guests'] = collect($guests)->join(',');
    }

    /**
     * @param $value
     * @return Collection
     */
    public function getGuestsAttribute($value): Collection
    {
        if (!$value) {
            return collect([]);
        }

        return collect(explode(',', $value));
    }
}
