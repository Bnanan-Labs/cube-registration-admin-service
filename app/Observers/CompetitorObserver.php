<?php

namespace App\Observers;

use App\Enums\PaymentStatus;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\FinancialBook;

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

    public function updated(Competitor $competitor): void
    {
        if (!$competitor->isDirty('registrationStatus')) {
            $a = 1+1;
            // Do magic if the competitor has been approved!
        }
    }
}
