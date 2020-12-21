<?php

namespace App\Console\Commands\System\Parsers\Documents;

use App\Models\System\Documents\Estimate;
use App\Models\System\Parser\Payment;
use Illuminate\Console\Command;

class GetEstimatesCatalogsGoodsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:get-estimates-catalogs-goods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизация смет с каталогами товаров';

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
            'goods_items' => function ($q) {
                $q->with([
                    'price_goods:id,catalogs_goods_id'
                ])
                    ->has('price_goods')
                ->select([
                    'id',
                    'estimate_id',
                    'price_id'
                ]);
            }
        ])
            ->doesntHave('catalogs_goods')
            ->select([
                'id'
            ])
            ->chunk(10000, function($estimates) {

                $this->line('Количество: ' . $estimates->count());

                $bar = $this->output->createProgressBar($estimates->count());
                $bar->start();


                foreach ($estimates as $estimate) {
                    if ($estimate->goods_items->isNotEmpty()) {
                        $groupedGoodsItems = $estimate->goods_items->groupBy('price_goods.catalogs_goods_id');
                        $catalogsFoodsIds = $groupedGoodsItems->keys();
                        $estimate->catalogs_goods()->sync($catalogsFoodsIds);
                    }
                    $bar->advance();
                }

                $bar->finish();
                $this->info('');

            });
//            ->get([
//                'id'
//            ]);



        $this->info(__('msg.ok'));
    }
}
