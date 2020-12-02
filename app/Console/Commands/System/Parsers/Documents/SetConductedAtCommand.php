<?php

namespace App\Console\Commands\System\Parsers\Documents;

use App\Models\System\Parser\Estimate;
use Illuminate\Console\Command;

class SetConductedAtCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:set-registered-at';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проставление сметам даты оформления и продажи';

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
        $this->info(__('Начинаем отмену непроведенных нарядов'));


        $estimates = Estimate::whereNull('registered_at')
            ->where('is_registered', true)
            ->get();

        $this->line('Количество: ' . $estimates->count());

        $bar = $this->output->createProgressBar($estimates->count());
        $bar->start();

        foreach ($estimates as $estimate) {
            $estimate->update([
                'registered_at' => $estimate->created_at,
                'conducted_at' => $estimate->is_saled == 1 ? $estimate->created_at : null,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->info('');

        $this->info(__('msg.ok'));
    }
}
