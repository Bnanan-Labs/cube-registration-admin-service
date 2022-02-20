<?php

namespace App\Observers;

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
}
