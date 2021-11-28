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
     * @return array
     */
    public function getWcaTeamsAttribute($value): Collection
    {
        if (!$value) {
            return collect([]);
        }
        return collect(explode(',', $value));
    }
}
