<?php

namespace App\Console\Commands\System\Parsers\Documents;

use App\Http\Controllers\Traits\Estimatable;
use App\Models\System\Documents\Estimate;
use App\Models\System\Parser\Payment;
use Illuminate\Console\Command;

class AggregateEstimatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:aggregate-estimates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Аггрегация смет';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    use Estimatable;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(0);

        $this->info(__($this->description));

        $estimates = Estimate::with([
            'payments'
        ])
            ->where('is_need_parse', true)
            ->limit(10000)
            ->get();
//        dd($estimates);

        $this->line('Количество: ' . $estimates->count());

        $bar = $this->output->createProgressBar($estimates->count());
        $bar->start();

        foreach ($estimates as $estimate) {
            $this->aggregateEstimate($estimate);
            if ($estimate->payments->isNotEmpty()) {
                $type = null;
                $cash = $estimate->payments->sum('cash');
                $electronically = $estimate->payments->sum('electronically');

                if ($cash > 0 && $electronically > 0) {
                    $type = 'mixed';
                }

                if ($cash > 0 && $electronically == 0) {
                    $type = 'cash';
                }

                if ($cash == 0 && $electronically > 0) {
                    $type = 'electronically';
                }

                $paid = $cash + $electronically;

                $estimate->update([
                    'paid' => $paid,
                    'debit' => $estimate->total - $paid,
                    'payment_type' => $type
                ]);
            }
            $estimate->update([
               'is_need_parse' => false,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->info('');

        $this->info(__('msg.ok'));
    }
}
