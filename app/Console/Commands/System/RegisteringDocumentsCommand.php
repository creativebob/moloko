<?php

namespace App\Console\Commands\System;

use App\Http\Controllers\Traits\Offable;
use App\Http\Controllers\Traits\Receiptable;
use App\Models\System\Documents\Consignment;
use App\Models\System\Documents\Production;
use App\User;
use Illuminate\Console\Command;

class RegisteringDocumentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:registering';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Переприходование накладных и перепроизводство нарядов';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    use Offable;
    use Receiptable;

    /**
     * Execute the console command.
     */
    public function handle()
    {

        set_time_limit(0);

        \DB::statement("SET foreign_key_checks=0");
        $names = [
            'goods_stocks',
            'raws_stocks',
            'attachments_stocks',
            'containers_stocks',
            'tools_stocks',
            
            'costs',
            'costs_histories',
            
            'receipts',
            'offs',
        ];
        foreach ($names as $name) {
            \DB::table($name)->truncate();
        }
        \DB::statement("SET foreign_key_checks=1");
        
        \Auth::loginUsingId(4);

        $consignments = Consignment::with([
            'items' => function ($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'article',
                            'stocks',
                            'cost'
                        ]);
                    },
                    'entity',
                ]);
            },
        ])
            ->whereNotNull('receipted_at')
            ->get();

        foreach ($consignments as $consignment) {
            logs('documents')
                ->info("========================================== НАЧАЛО ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ ==============================================");

            foreach ($consignment->items as $item) {
                $this->receipt($item);
            }

            logs('documents')
                ->info("Оприходована накладная c id: {$consignment->id}");
            logs('documents')
                ->info('======================================== КОНЕЦ ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ ==============================================
				
				');
            echo "Накладная {$consignment->id} переприходована";
        }

        $productions = Production::with([
            'items' => function ($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'article' => function ($q) {
                                $q->with([
                                    'raws' => function ($q) {
                                        $q->with([
                                            'cost'
                                        ]);
                                    },
                                    'containers' => function ($q) {
                                        $q->with([
                                            'cost'
                                        ]);
                                    },
                                    'attachments' => function ($q) {
                                        $q->with([
                                            'cost'
                                        ]);
                                    },
                                ]);
                            }
                        ]);
                    },
                    'entity'
                ]);
            },
        ])
            ->whereNotNull('produced_at')
            ->get();

        foreach ($productions as $production) {
            logs('documents')
                ->info('========================================== НАЧАЛО НАРЯДА ПРОИЗВОДСТВА C ID: ' . $production->id . ' ==============================================');

            foreach ($production->items as $item) {

                logs('documents')
                    ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() . ' ' . $item->id . ' ===');

                // Без проверки остатка
                $res = $this->production($item);
                $cost = $res['cost'];
                $isWrong = $res['is_wrong'];
                $amount = $cost * $item->count;

                $item->update([
                    'cost' => $cost,
                    'amount' => $amount,
                ]);

                // Приходование
                $this->receipt($item, $isWrong);
            }

            logs('documents')
                ->info('Произведен наряд c id: ' . $production->id);
            logs('documents')
                ->info('========================================== КОНЕЦ ПРОИЗВОДСТВА НАРЯДА ==============================================
				
				');
            echo "Наряд {$production->id} перепроизведен";
        }

        echo "Гатова";
    }
}
