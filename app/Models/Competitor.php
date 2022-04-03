<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;


class Competitor extends Model
{
    use HasFactory, Uuid;

    protected $guarded = [];

    protected $casts = [
        'is_delegate' => 'boolean',
        'is_interested_in_nations_cup' => 'boolean',
        'payment_status' => PaymentStatus::class,
        'registration_status' => RegistrationStatus::class,
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
     * @return BelongsTo
     */
    public function finances(): BelongsTo
    {
        return $this->belongsTo(FinancialBook::class, 'financial_book_id');
    }

    /**
     * @return BelongsTo
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * @param array $teams
     */
    public function setWcaTeamsAttribute(array $teams): void
    {
        $this->attributes['wca_teams'] = collect($teams)->join(',');
    }

    /**
     * @param ?string $value
     * @return Collection
     */
    public function getWcaTeamsAttribute(?string $value): Collection
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
     * @param string $value
     * @return Collection
     */
    public function getGuestsAttribute(string $value): Collection
    {
        if (!$value) {
            return collect([]);
        }

        return collect(explode(',', $value));
    }

    /**
     * @return int
     */
    public function getNumberOfGuestsAttribute(): int
    {
        return $this->guests->count();
    }
}
