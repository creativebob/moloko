<?php

namespace App\Console\Commands\System\Parsers;

use App\Parsers\System\VkusnyashkaParser;
use Illuminate\Console\Command;

class VkusnyashkaSubscribersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vkusnyashka:subscribers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавление новых подписчиков в рассылку';

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
        VkusnyashkaParser::addSubscribersToMailing();
    }
}
