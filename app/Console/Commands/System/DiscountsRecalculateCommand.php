<?php

namespace App\Console\Commands\System;

use App\Discount;
use App\PricesGoods;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Telegram;
use Telegram\Bot\Exceptions\TelegramResponseException;

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
//        dd($companyId);

//        $now = Carbon::createFromFormat('Y-m-d H:i:s', '2020-09-09 00:00:00');
        $now = Carbon::createFromFormat('Y-m-d H:i:s', now()->format('Y-m-d H:i') . ':00');
//        dd($now);




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

        $response = Telegram::sendMessage([
            'chat_id' => 228265675,
            'text' => "Попали в перерасчет скидок. Время {$now->format('d.m.Y H:i:s')}, Id: {$companyId}, Скидок: {$discounts->count()}"
        ]);

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

        if ($discounts->isNotEmpty()) {
            // Сообщение
            $message = "Изменены скидки:\r\n";
            foreach ($discounts as $discount) {
                $message .= $discount->name . ' ' . ($discount->mode == 1) ? "{$discount->percent}%" : "{$discount->cyrrency} руб." . ($discount->is_actual == 1) ? ' - установлена' : ' - снята' . "\r\n";
            }
            $message .= "Затронуто позиций: " . count($pricesGoodsIds) .  " шт.";

            $destinations = User::whereHas('staff', function ($query) {
                $query->whereHas('position', function ($query) {
                    $query->whereHas('notifications', function ($query) {
                        $query->where('notification_id', 5);
                    });
                });
            })
                ->whereNotNull('telegram')
                ->get([
                    'telegram'
                ]);

            if (isset($destinations)) {

                // Отправляем на каждый telegram
                foreach ($destinations as $destination) {

                    if (isset($destination->telegram)) {

                        try {
                            $response = Telegram::sendMessage([
                                'chat_id' => $destination->telegram,
                                'text' => $message
                            ]);
                        } catch (TelegramResponseException $exception) {
                            // Юзера нет в боте, не отправляем ему мессагу
                        }
                    }
                }
            }
        }
    }
}
