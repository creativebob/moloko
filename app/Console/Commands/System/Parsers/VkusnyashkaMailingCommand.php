<?php

namespace App\Console\Commands\System\Parsers;

use App\Notifications\System\Email;
use App\Parsers\System\VkusnyashkaParser;
use Illuminate\Console\Command;

class VkusnyashkaMailingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vkusnyashka:mailing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Рассылка по базе подписчиков';

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
        Email::sendMailingToSubscribers();
    }
}
