<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Jobs\CreateCompetitorBook;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'approved_at' => 'datetime',
    ];

    /**
     * @return BelongsToMany
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('best_single', 'best_single_formatted', 'competition_rank_single', 'national_rank_single', 'continental_rank_single', 'world_rank_single', 'best_average', 'best_average_formatted', 'competition_rank_average', 'national_rank_average', 'continental_rank_average', 'world_rank_average', 'synced_at');
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

    /**
     * @param Builder $query
     * @return void
     */
    public function scopeApproved(Builder $query): void
    {
        $query->whereNotNull('approved_at');
    }

    /**
     * @param Builder $query
     * @return void
     */
    public function scopeAccepted(Builder $query): void
    {
        $query->where('registration_status', '=', RegistrationStatus::accepted->value);
    }

    /**
     * @param Builder $query
     * @return void
     */
    public function scopeWaiting(Builder $query): void
    {
        $query->where('registration_status', '=', RegistrationStatus::waitingList->value)
              ->orderBy('approved_at');
    }

    /**
     * @return int
     */
    public function getQueueNumberInWaitingListAttribute(): int
    {
        if ($this->registration_status !== RegistrationStatus::waitingList) {
            return 0;
        }

        return Competitor::waiting()->where('approved_at', '<', $this->approved_at)->count() + 1;
    }

    /**
     * @return bool
     */
    public function getShouldWaiveFeeAttribute(): bool
    {
        $isStaff = (bool) $this->competition->staff()->where('wca_id', '=', $this->wca_id)->first();
        return $this->is_exempt_from_payment || $isStaff;
    }

    /**
     * @return Attribute
     */
    public function medals(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => collect(['gold', 'silver', 'bronze', 'total'])->combine(collect(explode(',', $value))),
            set: fn ($value) => collect($value)->values()->join(','),
        );
    }

    /**
     * @return Attribute
     */
    public function records(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => collect(['national', 'continental', 'world', 'total'])->combine(collect(explode(',', $value))),
            set: fn ($value) => collect($value)->values()->join(','),
        );
    }

    /**
     * @return void
     */
    public function cancel(): void
    {
        $this->update([
            'approved_at' => null,
            'guests'=> [],
            'registration_status' => RegistrationStatus::cancelled
        ]);
        $this->events()->detach();
        $this->refresh();

        CreateCompetitorBook::dispatch($this);
    }
}
