<?php

namespace App\Jobs;

use App\Models\Competitor;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class RegisterCompetitor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public array $registration, public User $user)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Competitor::create([
            'first_name' => Arr::get($this->registration, 'first_name'),
            'last_name' => Arr::get($this->registration, 'last_name'),
            'wca_id' => $this->user->wca_id,
            'nationality' => $this->user->nationality,
            'email' => $this->user->email,
            'financial_book_id' => 1,
            'competition_id' => 1,
        ]);
    }
}
