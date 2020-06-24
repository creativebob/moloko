<?php

namespace App\Console\Commands\System\Parsers;

use App\Parsers\System\RollHouseParser;
use Illuminate\Console\Command;

class RollHouseFullParserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roll-house:full-parser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Полный парсинг базы РХ на момент запуска';

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
        RollHouseParser::fullParser();
    }
}
