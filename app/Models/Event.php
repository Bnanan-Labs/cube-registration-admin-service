<?php

namespace App\Models;

use App\Services\Finances\Casts\MoneyBagCast;
use App\Services\Wca\Wca;
use App\Services\Wca\Enums\Event as EventEnum;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Event extends Model
{
    use HasFactory, Uuid;

    protected $casts = [
        'fee' => MoneyBagCast::class,
    ];

    /**
     * @return BelongsToMany
     */
    public function competitors(): BelongsToMany
    {
        return $this->belongsToMany(Competitor::class)
            ->withPivot('best_single', 'competition_rank_single', 'national_rank_single', 'continental_rank_single', 'world_rank_single', 'best_average', 'competition_rank_average', 'national_rank_average', 'continental_rank_average', 'world_rank_average', 'synced_at');
    }

    /**
     * @return BelongsToMany
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class);
    }

    /**
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return (new Wca())->event(EventEnum::from($this->wca_event_id))->fullName;
    }

    /**
     * @return string
     */
    public function getShortNameAttribute(): string
    {
        return (new Wca())->event(EventEnum::from($this->wca_event_id))->shortName;
    }

    /**
     * @return string
     */
    public function getResultFormatAttribute(): string
    {
        return (new Wca())->event(EventEnum::from($this->wca_event_id))->resultFormat->value;
    }
}
