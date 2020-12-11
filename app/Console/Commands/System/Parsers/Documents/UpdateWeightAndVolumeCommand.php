<?php

namespace App\Console\Commands\System\Parsers\Documents;

use App\Models\System\Parser\Payment;
use App\Off;
use App\Receipt;
use App\Reserve;
use Illuminate\Console\Command;

class UpdateWeightAndVolumeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:update-weight-and-volume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление веса и обьема в receipts / offs / reserves';

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
        $this->info(__($this->description));

        $receipts = Receipt::with([
            'cmv.article'
        ])
            ->where('weight_unit', 0)
            ->orWhere('volume_unit', 0)
            ->get();

        $this->line('Количество receipts: ' . $receipts->count());

        $bar = $this->output->createProgressBar($receipts->count());
        $bar->start();

        foreach ($receipts as $receipt) {
            $receipt->update([
                'weight_unit' => $receipt->cmv->article->weight,
                'volume_unit' => $receipt->cmv->article->volume,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->info('');

        $offs = Off::with([
            'cmv.article'
        ])
            ->where('weight_unit', 0)
            ->orWhere('volume_unit', 0)
            ->get();

        $this->line('Количество offs: ' . $offs->count());

        $bar = $this->output->createProgressBar($offs->count());
        $bar->start();

        foreach ($offs as $off) {
            $off->update([
                'weight_unit' => $off->cmv->weight,
                'volume_unit' => $off->cmv->volume,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->info('');

        $reserves = Reserve::with([
            'cmv.article'
        ])
            ->where('weight_unit', 0)
            ->orWhere('volume_unit', 0)
            ->get();

        $this->line('Количество reserves: ' . $reserves->count());

        $bar = $this->output->createProgressBar($reserves->count());
        $bar->start();

        foreach ($reserves as $reserve) {
            $reserve->update([
                'weight_unit' => $reserve->cmv->article->weight,
                'volume_unit' => $reserve->cmv->article->volume,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->info('');

        $this->info(__('msg.ok'));
    }
}
