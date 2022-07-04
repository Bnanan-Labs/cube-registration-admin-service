<?php

namespace App\Console\Commands;

use App\Models\Competitor;
use Illuminate\Console\Command;

class ProcessRefund extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refund:process';

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
        // dispatch_sync(new \App\Jobs\ProcessRefund(Competitor::find('...')));
        return 0;
    }
}
