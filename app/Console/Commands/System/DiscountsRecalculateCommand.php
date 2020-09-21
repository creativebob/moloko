<?php

namespace App\Console\Commands\System;

use App\Discount;
use App\PricesGoods;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Notifications\System\Notifications;

class DiscountsRecalculateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discounts:recalculate
                            {companyId : Id компании}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Перерасчет актуальных скидок для прайсов товаров';

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
        set_time_limit(0);

        $companyId = $this->argument('companyId');
//        $now = Carbon::createFromFormat('Y-m-d H:i:s', '2020-09-09 00:00:00');
        $now = Carbon::createFromFormat('Y-m-d H:i:s', now()->format('Y-m-d H:i') . ':00');

        $discounts = Discount::with([
            'entity'
        ])
            ->where(function($q) use ($now) {
                $q->where('begined_at',  $now)
                ->orWhere('ended_at',  $now);
            })
            ->where([
                'company_id' => $companyId,
                'archive' => false
            ])
            ->get();
//        dd($discounts->count());

        $pricesGoodsIds = [];

        foreach ($discounts as $discount) {

            // Скидка актуальна
            if ($discount->begined_at == $now) {
                $discount->update([
                    'is_actual' => true
                ]);

                switch($discount->entity->alias) {
                    case ('prices_goods'):
                        $discount->load([
                            'prices_goods'
                        ]);
//                        $pricesGoodsIds += array_values($discount->prices_goods->pluck('id')->toArray());
                        $pricesGoodsIds = array_merge(array_values($pricesGoodsIds), array_values($discount->prices_goods->pluck('id')->toArray()));
                        break;

                    case ('catalogs_goods_items'):
                        $discount->load([
                            'catalogs_goods_items.prices_goods_actual'
                        ]);

                        foreach ($discount->catalogs_goods_items as $catalogsGoodsItem) {
                            $pricesGoodsIds = array_merge(array_values($pricesGoodsIds), array_values($catalogsGoodsItem->prices_goods_actual->pluck('id')->toArray()));
                        }
                        break;

                    case('estimates'):
                        $pricesGoods = PricesGoods::where([
                            'archive' => false,
                            'company_id' => $companyId
                        ])
                            ->get([
                                'id'
                            ]);
                        $pricesGoodsIds = array_merge(array_values($pricesGoodsIds), array_values($pricesGoods->pluck('id')->toArray()));
                        break;
                }
            }

            // Скидка не актуальна
            if ($discount->ended_at == $now) {
                $discount->update([
                    'is_actual' => false
                ]);

                switch($discount->entity->alias) {
                    case ('prices_goods'):
                        $discount->load([
                            'prices_goods_actual'
                        ]);
                        $pricesGoodsIds = array_merge(array_values($pricesGoodsIds), array_values($discount->prices_goods_actual->pluck('id')->toArray()));
                        break;

                    case ('catalogs_goods_items'):
                        $discount->load([
                            'catalogs_goods_items_prices_goods_actual'
                        ]);
                        $pricesGoodsIds = array_merge(array_values($pricesGoodsIds), array_values($discount->catalogs_goods_items_prices_goods_actual->pluck('id')->toArray()));
                        break;

                    case('estimates'):
                        $discount->load([
                            'estimates_prices_goods_actual'
                        ]);
                        $pricesGoodsIds = array_merge(array_values($pricesGoodsIds), array_values($discount->estimates_prices_goods_actual->pluck('id')->toArray()));
                        break;
                }
            }
        }

//        dd($pricesGoodsIds);
        $pricesGoodsIds = array_unique($pricesGoodsIds);
//        dd($pricesGoodsIds);

        $pricesGoods = PricesGoods::find($pricesGoodsIds);
        foreach ($pricesGoods as $priceGoods) {
            $priceGoods->update([
                'is_need_recalculate' => true
            ]);
        }

        if ($discounts->isNotEmpty() && count($pricesGoodsIds) > 0) {
            $discounts = Discount::find($discounts->pluck('id')->toArray());
            // Сообщение
            $message = "ИЗМЕНЕНИЯ НА СКИДКАХ\r\n\r\n";
            foreach ($discounts as $discount) {
                $message .= "{$discount->name} ";
                $message .= ($discount->mode == 1) ? "({$discount->percent}%)" : "({$discount->currency} руб.)";
                $message .= ' ';
                $message .= ($discount->is_actual == 1) ? ' - установлена' : ' - снята';
                $message .= "\r\n";
            }

            $message .= "\r\n";
            $message .= "Затронуто позиций: " . count($pricesGoodsIds) .  " шт.";

            // Отправляем мессагу подписанным
            Notifications::sendNotification(5, $message, $companyId);

        }
    }
}
