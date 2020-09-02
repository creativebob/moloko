<?php

namespace App\Console\Commands\System\Parsers;

use App\Parsers\System\VkusnyashkaParser;
use Illuminate\Console\Command;

class VkusnyashkaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vkusnyashka:parser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсинг базы Вкусняшки (аггрегирование смет, создание клиентов)';

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
        VkusnyashkaParser::parser();
    }
}
