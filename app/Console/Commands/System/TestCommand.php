<?php

namespace App\Console\Commands\System\Parsers;

use App\Parsers\System\RollHouseParser;
use Illuminate\Console\Command;
use Telegram;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестовая команда';

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
        $response = Telegram::sendMessage([
            'chat_id' => 228265675,
            'text' => 'Крон бро, clients_indicators = ' . config('app.clients_indicators')
        ]);
    }
}
