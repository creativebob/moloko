<?php

namespace App\Console\Commands\System\Parsers\Documents;

use Illuminate\Console\Command;

class TablesClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:tables-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистка таблиц нарядов и накладных с сопуствующими';

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
        if ($this->confirm("{$this->description}?")) {
            \DB::statement("SET foreign_key_checks=0");
            $names = [
                'consignments',
                'consignments_items',

                'productions',
                'productions_items',

                'goods_stocks',
                'raws_stocks',
                'attachments_stocks',
                'containers_stocks',
                'tools_stocks',

                'costs',
                'costs_histories',

                'receipts',
                'offs',

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
}
