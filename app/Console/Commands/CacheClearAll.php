<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CacheClearAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cache-clear-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all caches: config, route, cache, view, and route caches';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('route:clear');

        $this->info('All caches have been cleared!');

        return 0;
    }
}
