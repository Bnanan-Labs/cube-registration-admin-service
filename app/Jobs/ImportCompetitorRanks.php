<?php

namespace App\Jobs;

use App\Models\Competitor;
use App\Models\Event;
use App\Services\Wca\Wca;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class ImportCompetitorRanks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Competitor $competitor)
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
        $wca = new Wca();
        $competitor = $this->competitor;
        $wcaResponse = $wca->getPerson($competitor->wca_id);
        $competitor->update([
            'number_of_competitions' => Arr::get($wcaResponse, 'competition_count'),
            'medals' => Arr::get($wcaResponse, 'medals'),
            'records' => Arr::get($wcaResponse, 'records'),
        ]);
        $records = collect(Arr::get($wcaResponse, 'personal_records'));
        $competitor->events->each(function (Event $event) use ($competitor, $records) {
            if (!$ranks = Arr::get($records, $event->wca_event_id)) {
                return;
            }
            $competitor->events()->updateExistingPivot($event->id, [
                'best_single' => Arr::get($ranks, 'single.best'),
                'world_rank_single' => Arr::get($ranks, 'single.world_rank'),
                'continental_rank_single' => Arr::get($ranks, 'single.continent_rank'),
                'national_rank_single' => Arr::get($ranks, 'single.country_rank'),
                'competition_rank_single' => null,
                'best_average' => Arr::get($ranks, 'average.best'),
                'world_rank_average' => Arr::get($ranks, 'average.world_rank'),
                'continental_rank_average' => Arr::get($ranks, 'average.continent_rank'),
                'national_rank_average' => Arr::get($ranks, 'average.country_rank'),
                'competition_rank_average' => null,
                'synced_at' => now(),
            ]);
        });
    }
}
