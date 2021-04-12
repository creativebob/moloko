<?php

namespace App\Console\Commands\System\Caches;

use App\Reports\System\ClientsIndicatorsReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ReCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caches:re-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Перекеширование проекта';

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
     */
    public function handle()
    {
        Artisan::call('cache:clear');
        Artisan::call('modelCache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        Artisan::call('optimize');
        Artisan::call('view:cache');
    }
}
