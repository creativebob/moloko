<?php

namespace App\Console\Commands;

use App\Reports\System\ClientsIndicatorsReport;
use Illuminate\Console\Command;

class ClientsIndicatorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients-indicators:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Расчёт показателей общей клиентской базы за период';

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
        ClientsIndicatorsReport::getIndicators();
    }
}
