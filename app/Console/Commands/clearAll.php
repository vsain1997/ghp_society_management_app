<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class clearAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        // Run each artisan command
        $this->call('cache:clear');
        // $this->call('cache:forget');
        // $this->call('cache:prune-stale-tags');
        // $this->call('cache:table');
        $this->call('config:clear');
        // $this->call('event:clear');
        $this->call('optimize:clear');
        // $this->call('queue:clear');
        $this->call('route:clear');
        $this->call('view:clear');

        $this->info('caches,config,event,optimize,queue,route,view successfully cleared');

        return 0;
    }
}
