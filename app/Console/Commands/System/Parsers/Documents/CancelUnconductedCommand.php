<?php

namespace App\Console\Commands\System\Parsers\Documents;

use App\Http\Controllers\System\Traits\Cancelable;
use App\Models\System\Documents\Production;
use Illuminate\Console\Command;

class CancelUnconductedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:cancel-unconducted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отмена документов, которые начаты и не закончены';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    use Cancelable;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info(__('Начинаем отмену непроведенных нарядов'));

        \Auth::loginUsingId(4);
        $this->line('Авторизован пользователь: ' . auth()->user()->name);

        $unconductedProductions = Production::with([
            'items' => function ($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'article',
                            'cost'
                        ]);
                    },
                    'entity',
                    'receipts' => function ($q) {
                        $q->with([
                            'cmv' => function ($q) {
                                $q->with([
                                    'cost',
                                    'stocks',
                                    'article'
                                ]);
                            },
                            'storage'
                        ]);
                    },
                    'offs' => function ($q) {
                        $q->with([
                            'cmv' => function ($q) {
                                $q->with([
                                    'cost',
                                    'stocks',
                                    'article'
                                ]);
                            },
                            'storage'
                        ]);
                    },
                    'document'
                ]);
            },
        ])
            ->where(function ($q) {
                $q->whereNull('conducted_at')
                    ->has('receipts');
            })
            ->orWhere(function ($q) {
                $q->whereNull('conducted_at')
                    ->has('offs');
            })
            ->get();
        $this->line('Количество нарядов: ' . $unconductedProductions->count());

        $bar = $this->output->createProgressBar($unconductedProductions->count());
        $bar->start();

        foreach ($unconductedProductions as $production) {

            logs('documents')
                ->info('========================================== ОТМЕНА НАРЯДА ПРОИЗВОДСТВА ==============================================');

            foreach ($production->items as $item) {
                logs('documents')
                    ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() . ' ' . $item->id . ' ===
                        ');

                if ($item->offs->isNotEmpty()) {
                    $this->cancelOffs($item);
                }

                if ($item->receipts->isNotEmpty()) {
                    foreach ($item->receipts as $receipt) {
                        $item->receipt = $receipt;
                        $this->cancelReceipt($item);
                    }
                }
            }

            $production->update([
                'conducted_at' => null
            ]);

            logs('documents')
                ->info('Отменен наряд c id: ' . $production->id);
            logs('documents')
                ->info('========================================== КОНЕЦ ОТМЕНЫ НАРЯДА ПРОИЗВОДСТВА ==============================================

				');


            $bar->advance();
        }

        $bar->finish();
        $this->info('');

        $this->info(__('msg.ok'));
    }
}
