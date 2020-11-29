<?php

namespace App\Console\Commands\System\Parsers\Sales;

use Illuminate\Console\Command;

class ClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистка таблиц лидов и смет с сопуствующими';

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
        \DB::statement("SET foreign_key_checks=0");
        $names = [
            'leads',
            'clients',

            'estimates',
            'estimates_goods_items',
            'estimates_services_items',

            'payments',

            'reserves',
            'reserves_histories',
        ];
        foreach ($names as $name) {
            \DB::table($name)->truncate();
        }
        \DB::statement("SET foreign_key_checks=1");

        $this->info(__('msg.ok'));
    }
}
