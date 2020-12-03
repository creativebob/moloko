<?php

namespace App\Console\Commands\System\Parsers\Documents;

use App\Models\System\Parser\Payment;
use Illuminate\Console\Command;

class UpdatePaymentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:update-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление платежей';

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
        $this->info(__('Начинаем обновление платежей'));

        $payments = Payment::whereNull('registered_at')
            ->get();

        $this->line('Количество: ' . $payments->count());

        $bar = $this->output->createProgressBar($payments->count());
        $bar->start();

        foreach ($payments as $payment) {
            $payment->update([
                'registered_at' => $payment->created_at,

                'payments_sign_id' => 1,
                'payments_method_id' => 4,

                'cash' => $payment->payments_type_id == 1 ? $payment->amount : 0,
                'cash_taken' => $payment->payments_type_id == 1 ? $payment->amount : 0,

                'electronically' => $payment->payments_type_id == 1 ? $payment->amount : 0,

                'total' => $payment->amount,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->info('');

        $this->info(__('msg.ok'));
    }
}
