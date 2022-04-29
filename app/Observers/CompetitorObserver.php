<?php

namespace App\Observers;

use App\Enums\RegistrationStatus;
use App\Jobs\CreateCompetitorBook;
use App\Mails\CompetitorAccepted;
use App\Mails\CompetitorRegistered;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\FinancialBook;
use Illuminate\Support\Facades\Mail;

class CompetitorObserver
{
    public function creating(Competitor $competitor): void
    {
        if (!$competitor->isDirty('financial_book_id')) {
            $competitor->finances()->associate(FinancialBook::create());
        }
        if (!$competitor->isDirty('competition_id')) {
            $competitor->competition()->associate(Competition::first());
        }
    }

    public function created(Competitor $competitor)
    {
        // Send confirmation email
        if (app()->environment('production')) {
            Mail::to([$competitor->email])
                ->send(new CompetitorRegistered($competitor));
        }
    }

    public function updating(Competitor $competitor): void
    {
        // Accept or put people on the waiting list
        if ($competitor->isDirty('approved_at') && $competitor['approved_at']) {
            $competition = $competitor->competition;
            if ($competition->numberOfApprovedCompetitors < $competition->competitor_limit) {
                $competitor->registration_status = RegistrationStatus::accepted;
            } else {
                $competitor->registration_status = RegistrationStatus::waitingList;
            }
        }

        // revoke accepted status if approval gets removed
        if ($competitor->isDirty('approved_at') && !$competitor['approved_at']) {
            if ($competitor->registration_status === RegistrationStatus::accepted || $competitor->registration_status === RegistrationStatus::waitingList) {
                $competitor->registration_status = RegistrationStatus::pending;
            }
        }
    }

    public function updated(Competitor $competitor): void
    {
        // If people are on the waiting list, lets approve them!
        if ($competitor->isDirty('registration_status') && $competitor->getOriginal('registration_status') === RegistrationStatus::accepted) {
            $competition = $competitor->competition;
            $delta = $competition->competitor_limit - $competition->numberOfAcceptedCompetitors;
            if ($delta > 0) {
                Competitor::waiting()
                    ->limit($delta)
                    ->get()
                    ->each(Fn (Competitor $competitor) =>
                        $competitor->update(['registration_status' => RegistrationStatus::accepted]));
            }
        }

        // Re-calculate payment if payment situation changes for competitor status
        if ($competitor->isDirty('is_exempt_from_payment')) {
            CreateCompetitorBook::dispatch($competitor);
        }

        // Send Registration has been accepted email
        if (app()->environment('production') && $competitor->isDirty('registration_status') && $competitor['registration_status'] === RegistrationStatus::accepted) {
            Mail::to([$competitor->email])
                ->send(new CompetitorAccepted($competitor));
        }
    }
}
