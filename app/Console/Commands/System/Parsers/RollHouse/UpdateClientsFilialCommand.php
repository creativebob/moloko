<?php

namespace App\Console\Commands\System\Parsers\RollHouse;

use App\Models\System\Parser\Client;
use App\Parsers\System\RollHouseParser;
use Illuminate\Console\Command;

class UpdateClientsFilialCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roll-house:update-clients-filial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление филиала клиентов';

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
        $clients = Client::with([
            'clientable'
        ])
        ->get();

        $this->line('Количество: ' . $clients->count());

        $bar = $this->output->createProgressBar($clients->count());
        $bar->start();

        foreach($clients as $client) {
            $client->update([
                'filial_id' => $client->clientable->filial_id
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->info('');

        $this->info(__('msg.ok'));
    }
}
