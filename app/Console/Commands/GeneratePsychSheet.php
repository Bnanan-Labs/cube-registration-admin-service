<?php

namespace App\Console\Commands;

use App\Jobs\ImportCompetitorRanks;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\Event;
use App\Models\Rank;
use App\Services\Wca\Wca;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class GeneratePsychSheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psych:generate {competition}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $competition = Competition::find($this->argument('competition'));

        foreach ($competition->competitors as $competitor) {
            $this->line("Importing competitor: {$competitor->wca_id}");
            ImportCompetitorRanks::dispatch($competitor);
        }
        return 0;
    }
}
