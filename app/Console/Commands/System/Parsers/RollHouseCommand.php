<?php

namespace App\Console\Commands\System\Parsers;

use App\Parsers\System\RollHouseParser;
use Illuminate\Console\Command;

class RollHouseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roll-house:parser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсинг базы РХ с 17.12.2019 (начало работы нашей системы)';

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
        $parser = new RollHouseParser();
        $parser->parser();
    }
}
