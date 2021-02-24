<?php

namespace App\Console\Commands\System\Parsers\Documents;

use App\Models\System\Documents\Estimate;
use App\Models\System\Parser\Payment;
use Illuminate\Console\Command;

class AggregateEstimatesPaymentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:aggregate-estimate-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Аггрегация платежей по смете в смету';

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

        $estimates = Estimate::with([
            'payments'
        ])
//            ->where([
//                'paid' => 0,
//                'debit' => 0
//            ])
            ->has('payments')
//            ->limit(10000)
                ->latest()
            ->get([
                'id',
                'total'
            ]);
//        dd($estimates);

        $this->line('Количество: ' . $estimates->count());

        $bar = $this->output->createProgressBar($estimates->count());
        $bar->start();

        foreach ($estimates as $estimate) {
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
            $bar->advance();
        }

        $bar->finish();
        $this->info('');

        $this->info(__('msg.ok'));
    }
}
