<?php

namespace App\Parsers\System;

use App\Models\System\Parser\Client;
use App\Models\System\Parser\ContractsClient;
use App\Models\System\Parser\Estimate;
use App\Http\Controllers\System\Traits\Clientable;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Models\System\Parser\Lead as ParseLead;
use App\Models\System\Parser\User as ParseUser;
use App\Models\System\Parser\EstimatesGoodsItem;

use App\Models\System\RollHouse\Check;
use App\Models\System\RollHouse\Client as ParseClient;
use App\Models\System\Parser\Payment;
use App\Models\System\Parser\Phone as ParsePhone;
use App\Models\System\Parser\PricesGoods;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Lead;
use App\User;
use App\Phone;
use Telegram;
use Telegram\Bot\Exceptions\TelegramResponseException;

class RollHouseParser
{

    use Clientable;
    use UserControllerTrait;

    /**
     * Полный перенос старой базы РХ в систему
     *
     * @param Request $request
     * @return string
     */
    public static function fullParser()
    {
        set_time_limit(0);

        $destinations = [
            293282078,
            228265675
        ];

        // Отправляем на каждый telegram
        foreach ($destinations as $destination) {
            $response = Telegram::sendMessage([
                'chat_id' => $destination,
                'text' => 'Парсинг начат'
            ]);
        }

//        ParseClient::where('is_parse', true)->update([
//            'is_parse' => false
//        ]);
//
//        Check::where('is_parse', true)->update([
//            'is_parse' => false
//        ]);

        define("ANGARSK", 3);
        define("USOLYE", 2);
        define("BRATSK", 8);

        $pricesGoods = PricesGoods::with([
            'goods.article'
        ])
            ->where('archive', false)
            ->get();

        $oldClients = ParseClient::whereIn('branch_id', [ANGARSK, USOLYE, BRATSK])
            ->with([
                'checks' => function ($q) {
                    $q->whereDate('created', '>', '2016-03-03')
                        ->whereDate('created', '<', '2019-12-17')
                        ->whereNull('employer_id')
                        ->where(function ($q) {
                            $q->where('reject', 2)
                                ->orWhereNull('reject');
                        })
                        ->where('progress', '!=', 1)
                        ->where('is_parse', false);
                }
            ])
            ->where('is_parse', false)
//            ->where('id', '>', 4000)
//                ->where('discont', '>', 0)
            ->limit(1000)
//            ->get();
            ->chunk(100, function($oldClients) use ($pricesGoods) {
                $request = request();
                define("COMPANY", 1);
                define("AUTHOR", 1);

                foreach($oldClients as $oldClient) {
                    // Смотрим телефон в нашей БД
                    $phone = Phone::where('phone', $oldClient->phone)
                        ->with([
                            'user_owner' => function ($q) {
                                $q->where('site_id', 2);
                            }
                        ])
                        ->whereHas('user_owner', function ($q) {
                            $q->where('site_id', 2);
                        })
                        ->first();
//            dd($phone);

                    if ($phone) {
//                dd($oldClient);
                        $curUser = $phone->user_owner->first();
                        $phone = $curUser->main_phone;
                        // Если найден, проверяем есть ли user
                        $user = ParseUser::find($curUser->id);

                        if ($user->name == '' || $user->name == ' ' || is_null($user->name)) {
                            $res = getNameUser($oldClient->name);
                            $user->first_name = $res['first_name'];
                            $user->second_name = $res['second_name'];
                            $user->patronymic = $res['patronymic'];
                            $user->gender = $res['gender'];

                            $user->name = $oldClient->name;

                            echo "Обновлены данные user [{$user->id}]\r\n";
                        }
                        $user->external = $oldClient->id;
                        $user->save();

                    } else {
                        // если телефона нет в нашей БД, заводим юзера

                        $city_id = 2;
                        $filial_id = 1;
                        switch ($oldClient->branch_id) {
                            case (ANGARSK):
                                $city_id = 2;
                                $filial_id = 1;
                                break;
                            case (USOLYE):
                                $city_id = 4;
                                $filial_id = 2;
                                break;
                            case (BRATSK):
                                $city_id = 3;
                                $filial_id = 3;
                                break;
                        }

                        $userNumber = ParseUser::withTrashed()
                            ->count();
                        $userNumber = $userNumber + 1;

                        $user = new ParseUser;
                        $user->login = "user_{$userNumber}";
                        $user->password = bcrypt(str_random(12));
                        $user->access_code = rand(1000, 9999);

                        $res = getNameUser($oldClient->name);
                        $user->first_name = $res['first_name'];
                        $user->second_name = $res['second_name'];
                        $user->patronymic = $res['patronymic'];
                        $user->gender = $res['gender'];

                        $user->name = $oldClient->name;

                        $user->external = $oldClient->id;

                        $user->access_block = 0;
                        $user->user_type = 0;

                        if ($oldClient->birthday){
                            $user->birthday_date = Carbon::parse($oldClient->birthday)->format('d.m.Y');
                        }

                        $request->address = $oldClient->address;
                        $user->location_id = create_location($request, 1, $city_id);

                        $user->site_id = 2;
//                $user->email = $oldClient->email;

                        $user->filial_id = $filial_id;
                        $user->created_at = $oldClient->created;

                        $user->company_id = COMPANY;
                        $user->author_id = AUTHOR;
                        $user->display = true;

                        $user->save([
                            'timestamps' => false
                        ]);

                        if ($user) {
                            // Пишем или находим номер телефона
                            $new_phone = Phone::firstOrCreate([
                                'phone' => cleanPhone($oldClient->phone)
                            ], [
                                'crop' => substr(cleanPhone($oldClient->phone), -4),
                            ]);

                            $curUser = User::find($user->id);
                            $curUser->phones()->attach($new_phone->id, ['main' => 1]);

                            $phone = $new_phone;
                        }
                    }

                    // Пишем смс оповещение
                    if (is_null($oldClient->sms_deny)) {
                        $user->notifications()->sync([3]);
                    }

                    $user->load('client');

                    if (isset($user->client)) {
                        $client = $user->client;

                        $client = Client::update([
                            'description' => $oldClient->desc,
                            'discount' => $oldClient->discont ?? 0,
                            'points' => $oldClient->rh ?? 0,
                        ]);

                        if ($oldClient->state == 1) {
                            $client->blacklists()->create([
                                'description' => $oldClient->desc,
                                'begin_date' => $oldClient->updated,

                                'company_id' => COMPANY,
                                'author_id' => AUTHOR,
                                'display' => true
                            ]);
                        }

                    } else {
                        // Если у пользователя есть заказы или есть скидка - заводим его как клиента
                        if (($oldClient->discont > 0 && (! is_null($oldClient->discont))) || $oldClient->checks->isNotEmpty()) {
                            // Сохраняем пользователя как клиента
                            $client = Client::create([
                                'clientable_id' => $user->id,
                                'clientable_type' => 'App\User',

                                'description' => $oldClient->desc,
                                'discount' => $oldClient->discont ?? 0,
                                'points' => $oldClient->rh ?? 0,

                                'company_id' => COMPANY,
                                'author_id' => AUTHOR,
                                'display' => true
                            ]);

                            $client->created_at = $oldClient->created;
                            $client->save([
                                'timestamps' => false
                            ]);

                            if ($oldClient->state == 1) {
                                $client->blacklists()->create([
                                    'description' => $oldClient->desc,
                                    'begin_date' => $oldClient->updated,

                                    'company_id' => COMPANY,
                                    'author_id' => AUTHOR,
                                    'display' => true
                                ]);
                            }
                        }
                    }

                    if ($oldClient->checks->isNotEmpty()) {
                        foreach($oldClient->checks as $check) {

                            $city_id = 2;
                            $filial_id = 1;
                            switch ($check->branch_id) {
                                case (ANGARSK):
                                    $city_id = 2;
                                    $filial_id = 1;
                                    break;
                                case (USOLYE):
                                    $city_id = 4;
                                    $filial_id = 2;
                                    break;
                                case (BRATSK):
                                    $city_id = 3;
                                    $filial_id = 3;
                                    break;
                            }

                            $lead = ParseLead::with([
                                'estimates'
                            ])
                                ->whereDate('created_at', $check->created)
                                ->where('stage_id', 13)
                                ->whereHas('estimates', function ($q) {
                                    $q->where('is_dismissed', true);
                                })
                                ->where('client_id', $client->id)
                                ->first();

                            if ($lead) {
//                                    dd($lead);
                                foreach($lead->estimates as $estimate) {
                                    $estimate->update([
                                        'is_main' => false
                                    ]);
                                }

                            } else {
                                $lead = new ParseLead;

                                // Добавляем локацию
                                $request->address = $check->address;
                                $lead->location_id = create_location($request, 1, $city_id);

                                $lead->filial_id = $filial_id;
                                $lead->name = ($user->name == '' || $user->name == ' ' || is_null($user->name)) ? null : $user->name;
                                $lead->company_name = NULL;

                                $lead->draft = null;
                                $lead->author_id = 1;

                                // TODO - 10.06.20 - Менеджер пока Серебро
                                $lead->manager_id = 4;

                                $lead->user_id = $user->id;
                                $lead->client_id = $client->id;
                                $lead->stage_id = ($check->progress == 2) ? 12 : 13;
                                $lead->lead_type_id = 1;

                                $lead->lead_method_id = (isset($check->table) && ($check->table != 99)) ? 3 : 1;

                                $lead->badget = $check->summa;
                                $lead->created_at = $check->created;

                                $needDelivery = 1;
                                if (isset($check->table)) {
                                    $needDelivery = 0;
                                }
                                $lead->need_delivery = $needDelivery;

                                $lead->company_id = COMPANY;
                                $lead->author_id = AUTHOR;
                                $lead->display = true;

                                $lead->is_create_parse = true;
//                                $lead->is_link_parse = true;
//                        dd($lead);
                                $lead->save([
                                    'timestamps' => false
                                ]);

                                $lead->case_number = $check->id;

                                $leadsCount = ParseLead::where([
                                    'company_id' => COMPANY,
                                    'filial_id' => $filial_id
                                ])
                                    ->where('lead_type_id', 1)
                                    ->whereDate('created_at', $check->created)
                                    ->count();
                                $lead->serial_number = $leadsCount + 1;

                                $lead->save([
                                    'timestamps' => false
                                ]);

                                if ($lead) {
                                    $curLead = Lead::find($lead->id);
                                    $curLead->phones()->attach($phone->id, ['main' => 1]);
                                }
                            }

                            if ($lead) {
                                $estimate = Estimate::create([
                                    'lead_id' => $lead->id,
                                    'client_id' => $lead->client_id,
                                    'filial_id' => $lead->filial_id,

                                    'discount' => 0,
                                    'discount_percent' => 0,

                                    'margin_currency' => 0,
                                    'margin_percent' => 0,

                                    'amount' => $check->summa,
                                    'total' => $check->summa,

                                    'number' => $lead->case_number,
                                    'date' => $check->created->format('d.m.Y'),

                                    'is_main' => 1,
                                    'is_dismissed' => ($check->progress == 2) ? 0 : 1,

                                    'registered_at' => $check->created,

                                    'saled_at' => $check->created,

                                    'created_at' => $check->created,
                                    'timestamps' => false,

                                    'is_create_parse' => true,

                                    'external' => $check->id,

                                    'certificate_amount' => $check->certs ?? 0,

                                    'company_id' => COMPANY,
                                    'author_id' => AUTHOR,
                                    'display' => true

                                ]);

                                if ($check->progress != 2) {
                                    echo "Сметы [{$estimate->id}] должна быть списана - {$estimate->is_dismissed}, в старой базе - {$check->dismissed}\r\n";
                                }

                                // Сохраняем состав сметы
                                $check->load('consists.price');

                                $estimatesGoodsItemsInsert = [];
                                $consistCount = 0;
                                foreach ($check->consists as $consist) {

                                    $prices = $pricesGoods->filter(function ($price) use ($consist, $lead){
                                        if ($price->filial_id == $lead->filial_id) {
                                            if ($price->goods->article->external == $consist->price_id) {
                                                return $price;
                                            }
                                        }
                                    });

                                    if ($prices->isNotEmpty()) {
                                        if ($prices->count() > 1) {
                                            echo "Несколько артикулов с external {$consist->price_id}\r\n";
                                        }
                                        $priceGoods = $prices->first();
                                        $count = $consist->count;
                                        $data = [
                                            'currency_id' => 1,
                                            'goods_id' => $priceGoods->goods->id,
                                            'price_id' => $priceGoods->id,
                                            'price' => $consist->summa ?? 0,
                                            'count' => $count ?? 0,
                                            'cost' => $priceGoods->goods->article->cost_default * $count,
                                            'amount' => $count * $consist->summa,
                                            'points' => $consist->rh ?? 0,

                                            'created_at' => $consist->created,
                                            'timestamps' => false,

                                            'company_id' => COMPANY,
                                            'author_id' => AUTHOR,
                                            'display' => true
                                        ];

                                        $data['discount_percent'] = is_null($consist->discont) ? 0 : $consist->discont;
                                        $data['discount_currency'] = ($data['amount'] / 100) * $data['discount_percent'];

                                        if ($data['points'] > 0) {
                                            $data['total'] = 0;
                                        } else {
                                            $data['total'] = $data['amount'] - $data['discount_currency'];
                                        }


                                        $data['margin_currency'] = $data['total'] - $data['cost'];
                                        if ($data['total'] > 0) {
                                            $data['margin_percent'] = ($data['margin_currency'] / $data['total']) * 100;
                                        } else {
                                            $data['margin_percent'] = 0;
                                        }

                                        $estimatesGoodsItemsInsert[] = EstimatesGoodsItem::make($data);
//                                dd($estimatesGoodsItemsInsert);
                                    }

                                    $consistCount++;
                                }

                                $estimate->goods_items()->saveMany($estimatesGoodsItemsInsert);
                                $estimate->load('goods_items');
                                if ($estimate->goods_items->count() != $check->consists->count()) {
                                    echo "У сметы [{$estimate->id}] не сходится состав, у нас {$estimate->goods_items->count()}, у него {$check->consists->count()}\r\n";
                                } else {
                                    echo "У сметы [{$estimate->id}] сходится состав\r\n";
                                }

                                // Обновляем смету
                                $estimate->load([
                                    'goods_items',
                                ]);

                                $cost = 0;
                                $amount = 0;
                                $total = 0;
                                $points = 0;
                                $discount_items_currency = 0;

                                if ($estimate->goods_items->isNotEmpty()) {
                                    $cost += $estimate->goods_items->sum('cost');
                                    $amount += $estimate->goods_items->sum('amount');
                                    $total += $estimate->goods_items->sum('total');
                                    $points += $estimate->goods_items->sum('points');
                                    $discount_items_currency += $estimate->goods_items->sum('discount_currency');
                                }

                                if ($amount > 0) {
                                    $discount = (($amount * $estimate->discount_percent) / 100);

                                    $margin_currency = $total - $cost;

                                    if ($total > 0) {
                                        $margin_percent = ($margin_currency / $total * 100);
                                    } else {
                                        $margin_percent = 0;
                                    }

                                    $data = [
                                        'cost' => $cost,
                                        'amount' => $amount,
                                        'discount' => $discount,
                                        'total' => $total,
                                        'points' => $points,
                                        'discount_items_currency' => $discount_items_currency,
                                        'margin_currency' => $margin_currency,
                                        'margin_percent' => $margin_percent,
                                        'timestamps' => false
                                    ];

                                } else {
                                    $data = [
                                        'cost' => 0,
                                        'amount' => 0,
                                        'discount' => 0,
                                        'total' => 0,
                                        'points' => 0,
                                        'discount_items_currency' => 0,
                                        'margin_currency' => 0,
                                        'margin_percent' => 0,
                                        'timestamps' => false
                                    ];
                                }

                                $estimate->update($data);

                                $estimate->save([
                                    'created_at' => $check->created,
                                    'timestamps' => false
                                ]);

                                if ($check->summa != $estimate->total) {
                                    echo "Не совпала сумма на смете {$estimate->id}: Наша - {$estimate->total}, Его - {$check->summa}\r\n";

                                    if ($check->summa > $estimate->total) {
                                        $losses = ($check->summa - $estimate->total);

                                        if ($estimate->points > 0) {
                                            $estimate->losses_from_points = $losses;
                                            $estimate->save([
                                                'timestamps' => false
                                            ]);
                                        } else {
                                            $estimate->surplus = $losses;
                                            $estimate->save([
                                                'timestamps' => false
                                            ]);
                                        }

                                    } else {
                                        echo "В смете [{$estimate->id}] наш total больше чем его summa\r\n";
                                    }
                                }

                                $diff = $check->summa - ($check->cash + $check->cashless);
                                if ($diff != 0) {
                                    echo "В смете [{$estimate->id}] разница между оплатой и суммой заказа = {$diff}\r\n";
                                }

                                if ($estimate->is_dismissed == 0) {
//                                    $this->setIndicators($estimate);
                                    $estimate->load('client');
                                    $client = $estimate->client;
                                    $data = [];

                                    $data['first_order_date'] = isset($client->first_order_date) ? Carbon::parse($client->first_order_date) : Carbon::parse($estimate->created_at);
                                    $data['last_order_date'] = Carbon::parse($estimate->created_at);
                                    $data['orders_count'] = $client->orders_count + 1;

                                    // TODO - 23.04.20 - Если разница меньше 1 месяца, то вписываем 1 месяц в секундах
                                    $diffInMonths = $data['first_order_date']->diffInMonths($data['last_order_date']);
                                    if ($diffInMonths == 0) {
                                        $diffInMonths = 1;
                                    }
                                    $data['lifetime'] = $diffInMonths;

                                    $data['purchase_frequency'] = $data['orders_count'] / $data['lifetime'];
                                    $data['ait'] = 1 / $data['purchase_frequency'];

                                    $total = Estimate::where([
                                        'client_id' => $client->id,
                                    ])
                                        ->whereNotNull('saled_at')
                                        ->sum('total');
                                    $data['customer_equity'] = $total + $estimate->total;

                                    $data['average_order_value'] = $data['customer_equity'] / $data['orders_count'];
                                    $data['customer_value'] = $data['average_order_value'] * $data['purchase_frequency'];

                                    // TODO - 22.04.20 - Lifetime перевести в месяца
                                    $data['ltv'] = $data['lifetime'] * $data['average_order_value'] * $data['purchase_frequency'];

                                    // TODO - 22.04.20 - Пока нет промоакций
                                    $data['use_promo_count'] = 0;
                                    $data['promo_rate'] = $data['use_promo_count'] / $data['orders_count'];

                                    $client->update($data);

                                    // Создаем договор
                                    $contracts_client = ContractsClient::create([
                                        'client_id' => $client->id,
                                        'date' => $check->created,
                                        'number' => $lead->case_number,
                                        'amount' => $estimate->total,
                                        'created_at' => $check->created,
                                        'timestamps' => false
                                    ]);
                                }

                                if ($check->progress == 2) {
                                    // Фиксируем платежи
                                    if ($check->cash) {
                                        if ($check->cash > 0) {
                                            $payment = Payment::create([
                                                'contract_id' => $contracts_client->id,
                                                'contract_type' => 'App\ContractsClient',
                                                'document_id' => $estimate->id,
                                                'document_type' => 'App\Models\System\Documents\Estimate',
                                                'payments_type_id' => 1,
                                                'amount' => $check->cash,
                                                'date' => $check->created->format('d.m.Y'),
                                                'currency_id' => 1,
                                                'created_at' => $check->created,
                                                'timestamps' => false,

                                                'company_id' => COMPANY,
                                                'author_id' => AUTHOR,
                                                'display' => true
                                            ]);
                                        }
                                    }

                                    if ($check->cashless) {
                                        if ($check->cashless > 0) {
                                            $payment = Payment::create([
                                                'contract_id' => $contracts_client->id,
                                                'contract_type' => 'App\ContractsClient',
                                                'document_id' => $estimate->id,
                                                'document_type' => 'App\Models\System\Documents\Estimate',
                                                'payments_type_id' => 2,
                                                'amount' => $check->cashless,
                                                'date' => Carbon::parse($check->created)->format('d.m.Y'),
                                                'currency_id' => 1,
                                                'created_at' => $check->created,
                                                'timestamps' => false,

                                                'company_id' => COMPANY,
                                                'author_id' => AUTHOR,
                                                'display' => true
                                            ]);
                                        }
                                    }
                                }
                            }

                            $check->is_parse = true;
                            $check->save();
                        }
                    } else {
                        echo "У юзера {$oldClient->id} нет заказов\r\n";
                    }

                    $oldClient->is_parse = true;
                    $oldClient->save();
                }
            });
//        dd($oldClients);



//        return 'Гатова';
        echo 'Гатова';

        $destinations = [
            293282078,
            228265675
        ];

        // Отправляем на каждый telegram
        foreach ($destinations as $destination) {
            $response = Telegram::sendMessage([
                'chat_id' => $destination,
                'text' => 'Парсинг кончат'
            ]);
        }
    }

    /**
     * Парсер смет из базы РХ
     */
    public static function parser()
    {
        set_time_limit(0);

        $start = now();

        define("ANGARSK", 3);
        define("USOLYE", 2);
        define("BRATSK", 8);
        define("COMPANY", 1);
        define("AUTHOR", 1);
        $authUser = auth()->user();
        $request = request();

        $pricesGoods = PricesGoods::with([
            'goods.article'
        ])
            ->where('archive', false)
            ->get();

        $checks = Check::with([
                'client',
                'consists',
            ])
            ->whereDate('created', '>=', '2019-12-17')
            ->whereNull('employer_id')
            ->where(function ($q) {
                $q->where('reject', 2)
                    ->orWhereNull('reject');
            })
            ->where('progress', '!=', 1)
            ->where('is_parse', false)
            ->limit(3000)
            ->get();

        if ($checks->isNotEmpty()) {

            $msg = "Найдены новые обращения, парсим.\r\nId лидов:\r\n";

            foreach($checks as $check) {
                $oldClient = $check->client;
                // Смотрим телефон в нашей БД
                $phone = Phone::where('phone', $oldClient->phone)
                    ->with([
                        'user_owner' => function ($q) {
                            $q->where('site_id', 2);
                        }
                    ])
                    ->whereHas('user_owner', function ($q) {
                        $q->where('site_id', 2);
                    })
                    ->first();
//            dd($phone);
                $res = isset($phone) ? 'да' : 'нет';
                echo "Найден телефон [{$res}]\r\n";

                if ($phone) {
//                dd($oldClient);

                    $curUser = $phone->user_owner->first();
                    $phone = $curUser->main_phone;
                    // Если найден, проверяем есть ли user
                    $user = ParseUser::find($curUser->id);

                    echo "По телефону найден user [{$user->id}]\r\n";

                    if ($user) {
                        if ($user->name == '' || $user->name == ' ' || is_null($user->name)) {
                            $res = getNameUser($oldClient->name);
                            $user->first_name = $res['first_name'];
                            $user->second_name = $res['second_name'];
                            $user->patronymic = $res['patronymic'];
                            $user->gender = $res['gender'];

                            $user->name = $oldClient->name;

                            $user->external = $oldClient->id;

                            $user->save();

                            echo "Обновлены данные user [{$user->id}]\r\n";
                        }

//                    dd($user);
//                    $user->save();
                    }



                } else {
                    // если телефона нет в нашей БД, заводим юзера

                    $city_id = 2;
                    $filial_id = 1;
                    switch ($oldClient->branch_id) {
                        case (ANGARSK):
                            $city_id = 2;
                            $filial_id = 1;
                            break;
                        case (USOLYE):
                            $city_id = 4;
                            $filial_id = 2;
                            break;
                        case (BRATSK):
                            $city_id = 3;
                            $filial_id = 3;
                            break;
                    }

                    $userNumber = ParseUser::withTrashed()
                        ->count();
                    $userNumber = $userNumber + 1;

                    $user = new ParseUser;
                    $user->login = "user_{$userNumber}";
                    $user->password = bcrypt(str_random(12));
                    $user->access_code = rand(1000, 9999);

                    $res = getNameUser($oldClient->name);
                    $user->first_name = $res['first_name'];
                    $user->second_name = $res['second_name'];
                    $user->patronymic = $res['patronymic'];
                    $user->gender = $res['gender'];

                    $user->name = $oldClient->name;

                    $user->external = $oldClient->id;

                    $user->access_block = 0;
                    $user->user_type = 0;

                    if ($oldClient->birthday) {
                        $user->birthday_date = Carbon::parse($oldClient->birthday)->format('d.m.Y');
                    }

                    $request->address = $oldClient->address;
                    $user->location_id = create_location($request, 1, $city_id);

                    $user->site_id = 2;
//                $user->email = $oldClient->email;

                    $user->filial_id = $filial_id;
                    $user->created_at = $oldClient->created;

                    $user->company_id = COMPANY;
                    $user->author_id = AUTHOR;
                    $user->display = true;

                    $user->save([
                        'timestamps' => false
                    ]);

                    if ($user) {
                        // Пишем или находим номер телефона
                        $new_phone = Phone::firstOrCreate([
                            'phone' => cleanPhone($oldClient->phone)
                        ], [
                            'crop' => substr(cleanPhone($oldClient->phone), -4),
                        ]);

                        $curUser = User::find($user->id);
                        $curUser->phones()->attach($new_phone->id, ['main' => 1]);

                        $phone = $new_phone;

                    }
                    echo "Создан user [{$user->id}]\r\n";
                }

                // Пишем смс оповещение
                if (is_null($oldClient->sms_deny)) {
                    $user->notifications()->sync([3]);
                }

                $curUser = User::find($user->id);
                $curUser->load('client');

                $curClient = $curUser->client;

                $createClient = false;
                if (isset($curClient)) {
                    $client = Client::find($curClient->id);

                    $client->update([
                        'description' => $oldClient->desc,
                        'discount' => $oldClient->discont ?? 0,
                        'points' => $oldClient->rh ?? 0,
                    ]);

                    echo "Найден клиент [{$client->id}]\r\n";

                } else {
                    // Сохраняем пользователя как клиента, т.к. у него есть заказы в старой базе
                    $client = Client::create([
                        'clientable_id' => $user->id,
                        'clientable_type' => 'App\User',

                        'description' => $oldClient->desc,
                        'discount' => $oldClient->discont ?? 0,
                        'points' => $oldClient->rh ?? 0,

                        'company_id' => COMPANY,
                        'author_id' => AUTHOR,
                        'display' => true
                    ]);

                    $client->created_at = $oldClient->created;
                    $client->save([
                        'timestamps' => false
                    ]);

                    echo "Создан клиент [{$client->id}]\r\n";

                    $createClient = true;

                }

                if ($oldClient->state == 1) {
                    $client->blacklists()->create([
                        'description' => $oldClient->desc,
                        'begin_date' => $oldClient->updated,

                        'company_id' => COMPANY,
                        'author_id' => AUTHOR,
                        'display' => true
                    ]);
                }

                $estimate = Estimate::where('external', $check->id)
                    ->first();
                if (! $estimate) {
                    $city_id = 2;
                    $filial_id = 1;
                    switch ($check->branch_id) {
                        case (ANGARSK):
                            $city_id = 2;
                            $filial_id = 1;
                            break;
                        case (USOLYE):
                            $city_id = 4;
                            $filial_id = 2;
                            break;
                        case (BRATSK):
                            $city_id = 3;
                            $filial_id = 3;
                            break;
                    }

                    // стол (не сайт)
                    if (isset($check->table) && $check->table != 99) {

                        // Если не отмененный заказ
                        $lead = new ParseLead;

                        // Добавляем локацию
                        $request->address = $check->address;
                        $lead->location_id = create_location($request, 1, $city_id);

                        $lead->filial_id = $filial_id;
                        $lead->name = ($user->name == '' || $user->name == ' ' || is_null($user->name)) ? null : $user->name;
                        $lead->company_name = NULL;

                        $lead->draft = null;
                        $lead->author_id = 1;

                        // TODO - 10.06.20 - Менеджер пока Серебро
                        $lead->manager_id = 4;

                        $lead->client_id = $client->id;
                        $lead->stage_id = ($check->progress == 2) ? 12 : 13;
                        $lead->lead_type_id = 1;

                        $lead->lead_method_id = 3;

                        $lead->badget = $check->summa;
                        $lead->created_at = $check->created;

                        $lead->company_id = COMPANY;
                        $lead->author_id = AUTHOR;
                        $lead->display = true;

                        $lead->is_create_parse = true;
//                                $lead->is_link_parse = true;
//                        dd($lead);
                        $lead->save([
                            'timestamps' => false
                        ]);

                        $lead->case_number = $check->id;

                        $leadsCount = ParseLead::where([
                            'company_id' => COMPANY,
                            'filial_id' => $filial_id
                        ])
                            ->where('lead_type_id', 1)
                            ->whereDate('created_at', $check->created)
                            ->count();
                        $lead->serial_number = $leadsCount + 1;

                        $lead->save([
                            'timestamps' => false
                        ]);

                        if ($lead) {
                            $curLead = Lead::find($lead->id);
                            $curLead->phones()->attach($phone->id, ['main' => 1]);
                        }

                    } else {
                        // ЗДЕСЬ СРАЩИВАЕМ

                        $leads = Lead::whereDate('created_at', $check->created)
                            ->whereHas('main_phones', function ($q) use ($check) {
                                $q->where('phone', cleanPhone($check->client->phone));
                            })
                            ->where('is_link_parse', false)
                            ->where('is_create_parse', false)
                            ->orderBy('created_at')
                            ->get();

                        if ($leads->count() == 1) {
                            // Понимаем что лид с сайта, т.к. он 1, то точно соответствует, их не 2, сращиваем
                            $lead = $leads->first();
                            $lead->load('estimate.goods_items');

                            // TODO - 10.06.20 - Менеджер пока Серебро
                            $lead->manager_id = 4;
                            $lead->case_number = $check->id;

                            $leadsCount = ParseLead::where([
                                'company_id' => COMPANY,
                                'filial_id' => $filial_id
                            ])
                                ->where('lead_type_id', 1)
                                ->whereDate('created_at', $lead->created_at)
                                ->count();
                            $lead->serial_number = $leadsCount + 1;

                            $lead->client_id = $client->id;

                            if ($lead->name == 'Клиент не указал имя' || $lead->name == 'Клиент не указал имя ') {
                                $lead->name = ($user->name == '' || $user->name == ' ' || is_null($user->name)) ? null : $user->name;
                            }

                            $needDelivery = 1;
                            if ($check->table == 99 || (is_null($check->table) && is_null($check->address))) {
                                $needDelivery = 0;
                            }
                            $lead->need_delivery = $needDelivery;

                            $lead->badget = $check->summa;

                            $lead->stage_id = ($check->progress == 2) ? 12 : 13;
                            $lead->order_amount_base = $lead->estimate->total;

                            $lead->save([
                                'timestamps' => false
                            ]);

                            echo "Сращиваем лида {$lead->case_number}, id: [{$lead->id}]\r\n";

                            $lead->estimate->goods_items()->forceDelete();
                            $lead->estimate()->forceDelete();

                        } else if ($leads->count() == 0) {
                            echo "При сращивании лидов не нашлось, создаем звонок\r\n";
                            $lead = new ParseLead;

                            // Добавляем локацию
                            $request->address = $check->address;
                            $lead->location_id = create_location($request, 1, $city_id);

                            $lead->filial_id = $filial_id;
                            $lead->name = ($user->name == '' || $user->name == ' ' || is_null($user->name)) ? null : $user->name;
                            $lead->company_name = null;

                            $lead->draft = null;
                            $lead->author_id = 1;

                            // TODO - 10.06.20 - Менеджер пока Серебро
                            $lead->manager_id = 4;

                            $lead->user_id = $user->id;
                            $lead->client_id = $client->id;
                            $lead->stage_id = ($check->progress == 2) ? 12 : 13;
                            $lead->lead_type_id = 1;

                            $lead->lead_method_id = 1;

                            $lead->badget = $check->summa;
                            $lead->created_at = $check->created;

                            $needDelivery = 1;
                            if ($check->table == 99 || (is_null($check->table) && is_null($check->address))) {
                                $needDelivery = 0;
                            }
                            $lead->need_delivery = $needDelivery;

                            $lead->company_id = COMPANY;
                            $lead->author_id = AUTHOR;
                            $lead->display = true;

                            $lead->is_create_parse = true;
//                                $lead->is_link_parse = true;
//                        dd($lead);
                            $lead->save([
                                'timestamps' => false
                            ]);

                            $lead->case_number = $check->id;

                            $leadsCount = ParseLead::where([
                                'company_id' => COMPANY,
                                'filial_id' => $filial_id
                            ])
                                ->where('lead_type_id', 1)
                                ->whereDate('created_at', $lead->created_at)
                                ->count();
                            $lead->serial_number = $leadsCount + 1;

                            $lead->save([
                                'timestamps' => false
                            ]);

                            if ($lead) {
                                $curLead = Lead::find($lead->id);
                                $curLead->phones()->attach($phone->id, ['main' => 1]);
                            }
                        } else {
                            echo "При сращивании лидов нашлось: {$leads->count()}, их id: {$leads->implode('id', ', ')}\r\n";

                            $lead = $leads->first();
                            $lead->load('estimate.goods_items');

                            // TODO - 10.06.20 - Менеджер пока Серебро
                            $lead->manager_id = 4;
                            $lead->case_number = $check->id;

                            $leadsCount = ParseLead::where([
                                'company_id' => COMPANY,
                                'filial_id' => $filial_id
                            ])
                                ->where('lead_type_id', 1)
                                ->whereDate('created_at', $lead->created_at)
                                ->count();
                            $lead->serial_number = $leadsCount + 1;

                            $lead->client_id = $client->id;

                            $needDelivery = 1;
                            if ($check->table == 99 || (is_null($check->table) && is_null($check->address))) {
                                $needDelivery = 0;
                            }
                            $lead->need_delivery = $needDelivery;

                            if ($lead->name == 'Клиент не указал имя' || $lead->name == 'Клиент не указал имя ') {
                                $lead->name = ($user->name == '' || $user->name == ' ' || is_null($user->name)) ? null : $user->name;
                            }

                            $lead->stage_id = ($check->progress == 2) ? 12 : 13;
                            $lead->order_amount_base = $lead->estimate->total;

                            $lead->badget = $check->summa;

                            $lead->save([
                                'timestamps' => false
                            ]);

                            $lead->estimate->goods_items()->forceDelete();
                            $lead->estimate()->forceDelete();
                        }
                    }
                } else {
                    $estimate->load('lead');
                    $lead = $estimate->lead;
                    echo "Id лида со сметой, имеющей external: [{$lead->id}]\r\n";
                }
                $msg .= "[{$lead->id}] - " . ($lead->name ?? 'Имя не указано') . "\r\n";

                // Если создали клиента, то вписываем ему source_id, если он есть у лида (если сращивали)
                if ($createClient) {
                    $client->update([
                       'source_id' => $lead->source_id
                    ]);
                }

                if ($lead) {
                    $estimate = Estimate::create([
                        'lead_id' => $lead->id,
                        'client_id' => $lead->client_id,
                        'filial_id' => $lead->filial_id,

                        'discount' => 0,
                        'discount_percent' => 0,

                        'margin_currency' => 0,
                        'margin_percent' => 0,

                        'amount' => $check->summa,
                        'total' => $check->summa,

                        'number' => $lead->case_number,
                        'date' => $check->created->format('d.m.Y'),


                        'is_main' => 1,
                        'is_dismissed' => ($check->progress == 2) ? 0 : 1,

                        'registered_at' => $check->created,
                        
                        'saled_at' => $check->created,

                        'created_at' => $check->created,
                        'timestamps' => false,

                        'external' => $check->id,

                        'certificate_amount' => $check->certs ?? 0,

                        'is_create_parse' => true,

                        'company_id' => COMPANY,
                        'author_id' => AUTHOR,
                        'display' => true

                    ]);

                    if ($lead->is_create_parse == 0) {
                        $lead->is_link_parse = true;
                        $lead->save([
                            'timestamps' => false
                        ]);
                    }

                    if ($check->progress != 2) {
                        echo "Сметы [{$estimate->id}] должна быть списана - {$estimate->is_dismissed}, в старой базе - {$check->dismissed}\r\n";
                    }

                    // Сохраняем состав сметы
                    $check->load('consists.price');

                    $estimatesGoodsItemsInsert = [];
                    $consistCount = 0;
                    foreach ($check->consists as $consist) {

                        $prices = $pricesGoods->filter(function ($price) use ($consist, $lead){
                            if ($price->filial_id == $lead->filial_id) {
                                if ($price->goods->article->external == $consist->price_id) {
                                    return $price;
                                }
                            }
                        });

                        if ($prices->isNotEmpty()) {
                            if ($prices->count() > 1) {
                                echo "Несколько артикулов с external {$consist->price_id}\r\n";
                            }
                            $priceGoods = $prices->first();
                            $count = $consist->count;
                            $data = [
                                'currency_id' => 1,
                                'goods_id' => $priceGoods->goods->id,
                                'price_id' => $priceGoods->id,
                                'price' => $consist->summa ?? 0,
                                'count' => $count ?? 0,
                                'cost' => $priceGoods->goods->article->cost_default * $count,
                                'amount' => $count * $consist->summa,
                                'points' => $priceGoods->points,

                                'created_at' => $consist->created,
                                'timestamps' => false,

                                'total_points' => $consist->rh ?? 0,

                                'company_id' => COMPANY,
                                'author_id' => AUTHOR,
                                'display' => true
                            ];

                            $data['discount_percent'] = is_null($consist->discont) ? 0 : $consist->discont;
                            $data['discount_currency'] = ($data['amount'] / 100) * $data['discount_percent'];

                            if ($data['points'] > 0) {
                                $data['total'] = 0;
                            } else {
                                $data['total'] = $data['amount'] - $data['discount_currency'];
                            }


                            $data['margin_currency'] = $data['total'] - $data['cost'];
                            if ($data['total'] > 0) {
                                $data['margin_percent'] = ($data['margin_currency'] / $data['total']) * 100;
                            } else {
                                $data['margin_percent'] = 0;
                            }

                            $estimatesGoodsItemsInsert[] = EstimatesGoodsItem::make($data);
//                                dd($estimatesGoodsItemsInsert);
                        }

                        $consistCount++;
                    }

                    $estimate->goods_items()->saveMany($estimatesGoodsItemsInsert);
                    $estimate->load('goods_items');
                    if ($estimate->goods_items->count() != $check->consists->count()) {
                        echo "У сметы [{$estimate->id}] не сходится состав, у нас {$estimate->goods_items->count()}, у него {$check->consists->count()}\r\n";
                    } else {
                        echo "У сметы [{$estimate->id}] сходится состав\r\n";
                    }



                    // Обновляем смету
                    $estimate->load([
                        'goods_items',
                    ]);

                    $cost = 0;
                    $amount = 0;
                    $total = 0;
                    $points = 0;
                    $discountItemsCurrency = 0;
                    $totalPoints = 0;
                    $totalBonuses = 0;

                    if ($estimate->goods_items->isNotEmpty()) {
                        $cost += $estimate->goods_items->sum('cost');
                        $amount += $estimate->goods_items->sum('amount');
                        $total += $estimate->goods_items->sum('total');
                        $points += $estimate->goods_items->sum('points');
                        $discountItemsCurrency += $estimate->goods_items->sum('discount_currency');
                        $totalPoints += $estimate->goods_items->sum('total_points');
                        $totalBonuses += $estimate->goods_items->sum('total_bonuses');
                    }

                    $marginCurrency = 0;
                    $marginPercent = 0;
                    $discount = 0;

                    if ($amount > 0) {
                        $discount = (($amount * $estimate->discount_percent) / 100);
                        $marginCurrency = $total - $cost;
                        $marginPercent = ($marginCurrency / $total * 100);
                    }

                    $data = [
                        'cost' => $cost,
                        'amount' => $amount,
                        'discount' => $discount,
                        'total' => $total,
                        'margin_currency' => $marginCurrency,
                        'margin_percent' => $marginPercent,
                        'points' => $points,
                        'discount_items_currency' => $discountItemsCurrency,
                        'total_points' => $totalPoints,
                        'total_bonuses' => $totalBonuses,
                        'timestamps' => false
                    ];

                    $estimate->update($data);

                    $estimate->save([
                        'created_at' => $check->created,
                        'timestamps' => false
                    ]);

                    if ($check->summa != $estimate->total) {
                        echo "Не совпала сумма на смете {$estimate->id}: Наша - {$estimate->total}, Его - {$check->summa}\r\n";

                        if ($check->summa > $estimate->total) {
                            $losses = ($check->summa - $estimate->total);

                            if ($estimate->points > 0) {
                                $estimate->losses_from_points = $losses;
                                $estimate->save([
                                    'timestamps' => false
                                ]);
                            } else {
                                $estimate->surplus = $losses;
                                $estimate->save([
                                    'timestamps' => false
                                ]);
                            }

                        } else {
                            echo "В смете [{$estimate->id}] наш total больше чем его summa\r\n";
                        }
                    }

                    $diff = $check->summa - ($check->cash + $check->cashless);
                    if ($diff != 0) {
                        echo "В смете [{$estimate->id}] разница между оплатой и суммой заказа = {$diff}\r\n";
                    }

                    if ($estimate->is_dismissed == 0) {
//                                    $this->setIndicators($estimate);
                        $estimate->load('client');
                        $client = $estimate->client;
                        $data = [];

                        $data['first_order_date'] = isset($client->first_order_date) ? Carbon::parse($client->first_order_date) : Carbon::parse($estimate->created_at);
                        $data['last_order_date'] = Carbon::parse($estimate->created_at);
                        $data['orders_count'] = $client->orders_count + 1;

                        // TODO - 23.04.20 - Если разница меньше 1 месяца, то вписываем 1 месяц в секундах
                        $diffInMonths = $data['first_order_date']->diffInMonths($data['last_order_date']);
                        if ($diffInMonths == 0) {
                            $diffInMonths = 1;
                        }
                        $data['lifetime'] = $diffInMonths;

                        $data['purchase_frequency'] = $data['orders_count'] / $data['lifetime'];
                        $data['ait'] = 1 / $data['purchase_frequency'];

                        $total = Estimate::where([
                            'client_id' => $client->id,
                        ])
                            ->whereNotNull('saled_at')
                            ->sum('total');
                        $data['customer_equity'] = $total + $estimate->total;

                        $data['average_order_value'] = $data['customer_equity'] / $data['orders_count'];
                        $data['customer_value'] = $data['average_order_value'] * $data['purchase_frequency'];

                        // TODO - 22.04.20 - Lifetime перевести в месяца
                        $data['ltv'] = $data['lifetime'] * $data['average_order_value'] * $data['purchase_frequency'];

                        // TODO - 22.04.20 - Пока нет промоакций
                        $data['use_promo_count'] = 0;
                        $data['promo_rate'] = $data['use_promo_count'] / $data['orders_count'];

                        $client->update($data);

                        // Создаем договор
                        $contracts_client = ContractsClient::create([
                            'client_id' => $client->id,
                            'date' => $check->created,
                            'number' => $lead->case_number,
                            'amount' => $estimate->total,
                            'created_at' => $check->created,
                            'timestamps' => false
                        ]);
                    }

                    if ($check->progress == 2) {
                        // Фиксируем платежи
                        if ($check->cash) {
                            if ($check->cash > 0) {
                                $payment = Payment::create([
                                    'contract_id' => $contracts_client->id,
                                    'contract_type' => 'App\ContractsClient',
                                    'document_id' => $estimate->id,
                                    'document_type' => 'App\Models\System\Documents\Estimate',
                                    'payments_type_id' => 1,
                                    'amount' => $check->cash,
                                    'date' => $check->created->format('d.m.Y'),
                                    'currency_id' => 1,
                                    'created_at' => $check->created,
                                    'timestamps' => false,

                                    'company_id' => COMPANY,
                                    'author_id' => AUTHOR,
                                    'display' => true
                                ]);
                            }
                        }

                        if ($check->cashless) {
                            if ($check->cashless > 0) {
                                $payment = Payment::create([
                                    'contract_id' => $contracts_client->id,
                                    'contract_type' => 'App\ContractsClient',
                                    'document_id' => $estimate->id,
                                    'document_type' => 'App\Models\System\Documents\Estimate',
                                    'payments_type_id' => 2,
                                    'amount' => $check->cashless,
                                    'date' => Carbon::parse($check->created)->format('d.m.Y'),
                                    'currency_id' => 1,
                                    'created_at' => $check->created,
                                    'timestamps' => false,

                                    'company_id' => COMPANY,
                                    'author_id' => AUTHOR,
                                    'display' => true
                                ]);
                            }
                        }
                    }
                }

                $check->is_parse = true;
                $check->save();

            }

//            Lead::where([
//                'is_create_parse' => false,
//                'is_link_parse' => false
//            ])
//                ->update([
//                    'draft' => true
//                ]);

            $msg .= "\r\n";
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
    }
}
