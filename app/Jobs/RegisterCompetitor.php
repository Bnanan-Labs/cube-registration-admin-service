<?php

namespace App\Jobs;

use App\Models\Competition;
use App\Models\Competitor;
use App\Models\User;
use Illuminate\Bus\Queueable;
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
    public function __construct(public array $registration, public User $user, public string $registrationId, public string $ip)
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
        $competitor = Competitor::create([
            'id' => $this->registrationId,
            'first_name' => Arr::get($this->registration, 'first_name'),
            'last_name' => Arr::get($this->registration, 'last_name'),
            'is_interested_in_nations_cup' => Arr::get($this->registration, 'is_interested_in_nations_cup'),
            'guests' => Arr::get($this->registration, 'guests'),
            'gender' => $this->user->wca->gender,
            'is_delegate' => (bool) $this->user->wca->delegate_status,
            'wca_teams' => $this->user->wca->teams,
            'avatar' => $this->user->avatar,
            'wca_id' => $this->user->wca_id,
            'nationality' => $this->user->nationality,
            'email' => $this->user->email,
        ]);

        $competitor->events()->sync(Arr::get($this->registration, 'events'));
        $competitor->days()->sync(Arr::get($this->registration, 'days'));
    }
}
