<?php

namespace App\Parsers\System;

use App\ContractsClient;
use App\Discount;
use App\Http\Controllers\System\Traits\Clientable;
use App\Lead;
use App\MailingList;
use App\MailingListItem;
use App\Payment;
use App\Subscriber;
use Telegram\Bot\Exceptions\TelegramResponseException;

class VkusnyashkaParser
{

    use Clientable;

    /**
     * Парсер смет из базы ВК
     */
    public static function parser()
    {
        set_time_limit(0);

        $start = now();

        define("COMPANY", 1);
        define("AUTHOR", 1);

        $discount = Discount::whereDate('ended_at', '2019-11-15')
            ->first();

        $leads = Lead::with([
            'estimate.goods_items.goods'
        ])
        ->where('is_link_parse', false)
        ->whereNotNull('user_id')
        ->chunk(100, function($leads) use ($discount) {
            foreach ($leads as $lead) {
                $estimate = $lead->estimate;

                if ($estimate->registered_at) {

                    logs('documents')->info("========================================== НАЧАЛО РЕГИСТРАЦИИ СМЕТЫ, ID: {$estimate->id} =============================================== ");

                    if ($estimate->goods_items->isNotEmpty()) {
                        foreach($estimate->goods_items as $estimatesGoodsItem) {
                            $data = [
                                'sale_mode' => 1,

                                'price_discount_id' => null,
                                'price_discount' => 0,
                                'total_price_discount' => $estimatesGoodsItem->count * $estimatesGoodsItem->price,

                                'catalogs_item_discount_id' => null,
                                'catalogs_item_discount' => 0,
                                'total_catalogs_item_discount' => $estimatesGoodsItem->count * $estimatesGoodsItem->price,

                                'estimate_discount_id' => null,
                                'estimate_discount' => 0,
                                'total_estimate_discount' => $estimatesGoodsItem->count * $estimatesGoodsItem->price,

                                'currency_id' => 1,
                                'amount' => $estimatesGoodsItem->count * $estimatesGoodsItem->price,

                                'discount_percent' => 0,
                                'discount_currency' => 0,
                            ];

                            if ($estimate->discount_percent == 10) {

                                $amount = 0;
                                switch ($discount->mode) {
                                    case(1):
                                        $amount = $data['amount'] / 100 * $discount->percent;
                                        $data['discount_percent'] = $discount->percent;
                                        $data['discount_currency'] = $amount;
                                        break;
                                    case(2):
                                        $amount = $discount->currency;
                                        $data['discount_percent'] = $amount / ($data['amount'] / 100);
                                        $data['discount_currency'] = $amount;
                                        break;
                                }

                                $data['estimate_discount_id'] = $discount->id;
                                $data['estimate_discount'] = $amount;
                                $data['total_estimate_discount'] = $data['amount'] - $amount;
                            }

                            $estimatesGoodsItem->update($data);
                        }

                    }

                    $cost = 0;
                    $amount = 0;
                    $total = 0;
                    $points = 0;
                    $discountItemsCurrency = 0;
                    $totalPoints = 0;
                    $totalBonuses = 0;

                    $estimate->load('goods_items');
                    if ($estimate->goods_items->isNotEmpty()) {
                        $cost += $estimate->goods_items->sum('cost');
                        $amount += $estimate->goods_items->sum('amount');
                        $total += $estimate->goods_items->sum('total');
                        $points += $estimate->goods_items->sum('points');
                        $discountItemsCurrency += $estimate->goods_items->sum('discount_currency');
                        $totalPoints += $estimate->goods_items->sum('total_points');
                        $totalBonuses += $estimate->goods_items->sum('total_bonuses');
                    }

                    $discountCurrency = 0;
                    $discountPercent = 0;

                    if ($amount > 0) {
                        $discountCurrency = $amount - $total;
                        $percent = $amount / 100;
                        $discountPercent = $discountCurrency / $percent;
                    }

                    $marginCurrency = $total - $cost;
                    $marginPercent = 0;
                    if ($marginCurrency > 0) {
                        $marginPercent = ($marginCurrency / $total * 100);
                    }

                    $data = [
                        'cost' => $cost,
                        'amount' => $amount,
                        'discount_currency' => $discountCurrency,
                        'discount_percent' => $discountPercent,
                        'total' => $total,
                        'margin_currency' => $marginCurrency,
                        'margin_percent' => $marginPercent,
                        'points' => $points,
                        'discount_items_currency' => $discountItemsCurrency,
                        'total_points' => $totalPoints,
                        'total_bonuses' => $totalBonuses,
                    ];

                    $estimate->update($data);

                    // Пишем склады при оформлении
                    $settings = getSettings();
                    if ($settings) {

                        $estimate->load([
                            'goods_items'
                        ]);

                        if ($estimate->goods_items->isNotEmpty()) {

                        }
                    }

                    // Ищем или создаем клиента
                    $client = $this->getClientUser($lead->user_id);

                    if (is_null($client->source_id)) {
                        $client->update([
                            'source_id' => $lead->source_id
                        ]);
                    }

                    $lead->update([
                        'client_id' =>  $client->id
                    ]);

                    $contractsClient = ContractsClient::create([
                        'client_id' => $client->id,
                        'amount' => $estimate->total,
                    ]);

                    $estimate->update([
                        'client_id' => $client->id,
                        'registered_at' => $estimate->updated_at,
                    ]);

                    logs('documents')->info("========================================== КОНЕЦ РЕГИСТРАЦИИ СМЕТЫ, ID: {$estimate->id} =============================================== ");

                    if ($lead->payment > 0) {
                        $paymentData = [
                            'amount' => $lead->payment,
                            'payments_type_id' => 1,
                            'currency_id' => 1,
                            'date' => $lead->created_at->format('d.m.Y'),

                            'contract_id' => $contractsClient->id,
                            'contract_type' => 'App\ContractsClient',

                            'document_id' => $estimate->id,
                            'document_type' => 'App\Models\System\Documents\Estimate'
                        ];

                        $payment = Payment::create($paymentData);
                    }

                    if (!$estimate->saled_at) {

                        // Обновляем показатели клиента
                        $this->setIndicators($estimate);

                        $estimate->update([
                            'saled_at' => $lead->created_at,
                        ]);

                    }

                }
            }
        });


        $msg = "Парсинг базы Вкусняшки\r\n";
        $msg .= "Начало: " . $start->format('d.m.Y H:i:s') . "\r\n";
        $msg .= "Окончание: " . now()->format('d.m.Y H:i:s');

        $destinations = [
            293282078,
            228265675
        ];
        // Отправляем на каждый telegram
        foreach ($destinations as $destination) {
            try {
                $response = Telegram::sendMessage([
                    'chat_id' => $destination,
                    'text' => $msg
                ]);
            } catch (TelegramResponseException $exception) {
                // Юзера нет в боте, не отправляем ему мессагу
            }
        }

    }
    
    /**
     * Добавление новых подписчиков в список рассылки
     *
     * @return string
     */
    public static function addSubscribersToMailing()
    {
        
        set_time_limit(0);
    
        \Auth::loginUsingId(4);
        
        $mailingList = MailingList::with('items')
            ->where('company_id', auth()->user()->company_id)
            ->first();
//        dd($mailingList);
        
        $subscribersIds = $mailingList->items->where('entity_type', 'App\Subscriber')->pluck('id');
//        dd($subscribersIds);
        
        $subscribers = Subscriber::whereNotIn('id', $subscribersIds)
            ->get();
        $newSubscriberIds = $subscribers->pluck('id');
//        dd($newSubscriberIds);
        
        $countSubscribers = count($newSubscriberIds);
        
        if ($countSubscribers > 0) {
            $data = [];
            foreach ($newSubscriberIds as $id) {
                $data[] = MailingListItem::make([
                    'mailing_list_id' => $mailingList->id,
                    'entity_id' => $id,
                    'entity_type' => 'App\Subscriber'
                ]);
            }
    
            $mailingList->items()->saveMany($data);
            echo "Добавлены новые подписчики: [{$countSubscribers}]";
        } else {
            echo "Новые подписчики не найдены";
        }
        
    }
}
