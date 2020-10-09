<?php

namespace App\Http\Controllers\Project;

use App\Campaign;
use App\Client;
use App\Discount;
use App\Http\Controllers\Project\Traits\Commonable;
use App\Http\Controllers\Traits\EstimateControllerTrait;
use App\Http\Controllers\Traits\LeadControllerTrait;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Lead;
use App\Models\Project\Estimate;
use App\Models\Project\EstimatesGoodsItem;
use App\Models\Project\Promotion;
use App\Phone;
use App\PricesGoods;
use App\Source;
use App\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use Telegram;
use Telegram\Bot\Exceptions\TelegramResponseException;

class CartController extends Controller
{

    use Commonable;
    use UserControllerTrait;
    use LeadControllerTrait;
    use EstimateControllerTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $site = $this->site;

        // Грузим продвижения с отображением на корзине
        $filialId = $site->filial->id;

        $prom = json_decode(\Cookie::get('prom'), true) ?? request()->prom;

        $promotions = Promotion::with([
            'prices_goods' => function ($q) use ($filialId) {
                $q->with([
                    'goods',
                    'currency',
                    'catalogs_item.directive_category:id,alias',
                    'catalogs_item.parent'
                ])
                    ->where('filial_id', $filialId);
            },
            'goods',
        ])
            ->where([
                'site_id' => $site->id,
                'is_upsale' => true,
                'display' => true
            ])
            ->whereHas('filials', function ($q) use ($filialId) {
                $q->where('id', $filialId);
            })
            ->where('begin_date', '<=', today())
            ->where('end_date', '>=', today())
            ->whereNull('prom')
            ->when($prom, function ($q) use ($site, $prom) {
                $q->orWhere(function ($q) use ($site, $prom) {
                    $q->whereHas('filials', function ($q) use ($site) {
                        $q->where('id', $site->filial->id);
                    })
                        ->where([
                            'site_id' => $site->id,
                            'is_upsale' => true,
                            'display' => true
                        ])
                        ->where('begin_date', '<=', today())
                        ->where('end_date', '>=', today())
                        ->when(is_array($prom), function ($q) use ($prom) {
                            $q->whereIn('prom', $prom);
                        })
                        ->when(is_string($prom), function ($q) use ($prom) {
                            $q->where('prom', $prom);
                        });
                });
            })
            ->get();

        $discount = Discount::where([
            'company_id' => $site->company_id,
//            'display' => true,
            'archive' => false
        ])
            ->whereHas('entity', function ($q) {
                $q->where('alias', 'estimates');
            })
            ->where('begined_at', '<=', now())
            ->where(function ($q) {
                $q->where('ended_at', '>=', now())
                    ->orWhereNull('ended_at');
            })
            ->first();

        $page = $site->pages_public->firstWhere('alias', 'cart');

        return view($site->alias . '.pages.cart.index', compact('site', 'page', 'prices_goods', 'promotions', 'discount'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Если пользователь дал согласие на обработку персональных данных
        if ($request->personal_data) {
            // Собираем для request недостающие данные или преобразовываем

            // Вкусняшка
            $lead_type = $request->has('lead_type') ? $request->lead_type : null;

            $school_number = $request->has('school_number') ? $request->school_number : null;
            $class_number = $request->has('class_number') ? $request->class_number : null;

            $kindergarten_number = $request->has('kindergarten_number') ? $request->kindergarten_number : null;
            $kindergarten_group = $request->has('kindergarten_group') ? $request->kindergarten_group : null;

            $company_name = $request->has('company_name') ? $request->company_name : null;

            switch ($lead_type) {
                case "Школа":
                    $description = $lead_type . ' №' . $school_number . ', класс ' . $class_number;
                    break;

                case "Детский сад":
                    $description = $lead_type . ' №' . $kindergarten_number . ', группа ' . $kindergarten_group;
                    break;

                case "Компания":
                    $description = $lead_type . ' ' . $company_name;
                    break;
            }

            // Отдаем недостающий description
            if (isset($description)) {
                $request->description = $description;
            }

            // TODO - 03.12.19 - Вынести в отдельные методы для сайта
            // ------------------------------------------- Создаем лида ---------------------------------------------
//            $lead = $this->createLeadFromSite($request);

            // Готовим необходимые данные ======================================================================
            // Получаем сайт
            $site = $this->site;
            $company = $site->company;
            $filialId = $this->site->filial->id;

            $first_name = isset($request->first_name) ? $request->first_name : 'Клиент не указал имя';

            $nickname = $request->name;
            $second_name = $request->second_name;

            if (($first_name == null) && ($second_name == null)) {
                if ($nickname == null) {
                    $lead_name = null;
                } else {
                    $lead_name = $first_name;
                }
            } else {
                $lead_name = $first_name . ' ' . $second_name;
            }

            $company_name = $request->company_name;
            $description = $request->description;

            // ------------------------------------------------------------------------------

            // Если пришло имя компании, то укажем, что это компания
            if ($company_name) {
                $private_status = 1;
            } else {
                $private_status = 0;
            }

            $cleanPhone = cleanPhone($request->main_phone);

            // Содержится ли в куках данные корзины
            if (Cookie::get('cart') !== null) {
                $cart = json_decode(Cookie::get('cart'), true);
            }

            // Работаем с ПОЛЬЗОВАТЕЛЕМ для лида ================================================================

            // Если пользователь АВТОРИЗОВАН
            $user = auth()->user();
            if ($user) {

                // Формируем имя записи в лида
                if (empty($lead_name)) {
                    $lead_name = $user->first_name . ' ' . $user->second_name;
                }
                $phone = $user->main_phone->phone;

                // Если пользователь НЕ авторизован
            } else {
                if (!isset($request->main_phone)) {
                    abort(403, 'Не указан номер телефона!');
                }

                $user = User::where('company_id', $site->company_id)
                    ->where(function ($q) use ($site) {
                        $q->where('site_id', $site->id)
                            ->orWhereNull('site_id');
                    })
                    ->whereHas('main_phones', function ($q) use ($cleanPhone) {
                        $q->where('phone', $cleanPhone);
                    })
                    ->first();
//                    dd($user);

//                    $result = $phone->user_owner->where('site_id', $site->id)->where('company_id', $site->company_id);

                if ($user) {
                    Log::info('Нашли телефон в связке с текущим сайтом');
                    Log::info($user->name ?? 'Имя не указано');

                } else {
                    Log::info('А вот в связке с текущим сайтом - не нашли');
                }

                // Если нет, то создадим нового
                if (empty($user)) {

                    // Если нет: создаем нового пользователя по номеру телефона
                    // Подготовка: -------------------------------------------------------------------------------------


                    $usersCount = User::withoutTrashed()
                        ->count();
                    $user_number = $usersCount + 1;

                    $user = new User;
                    $user->login = 'user_' . $user_number;
                    $user->password = bcrypt(str_random(12));
                    $user->access_code = rand(1000, 9999);

                    if ($request != null) {

                        $user->first_name = $request->first_name;
                        $user->second_name = $request->second_name;
                        $user->patronymic = $request->patronymic;
                    }

                    $user->access_block = 0;
                    $user->user_type = 0;

                    // Компания и филиал ----------------------------------------------------------
                    $user->company_id = $site->id;
                    $user->filial_id = $site->filial->id;

                    $user->name = $user->first_name . ' ' . $user->second_name;

                    $user->author_id = 1;

                    $user->saveQuietly();

                    if ($user) {

                        // Если номера нет, пишем или ищем новый и создаем связь
                        $new_phone = Phone::firstOrCreate(
                            ['phone' => $cleanPhone
                            ], [
                            'crop' => substr($cleanPhone, -4),
                        ]);

                        $user->phones()->attach($new_phone->id, [
                            'phone_entity_type' => 'App\USer',
                            'main' => 1
                        ]);

                    } else {
                        abort(403, 'Ошибка при создании пользователя по номеру телефона!');
                    }

                    // sendSms('79041248598', 'Данные для входа: ' . $user->access_code);

                    $user->location_id = create_location($request, 1, $site->filial->location->city_id);

                    $user->first_name = $first_name;
                    $user->second_name = $second_name;
                    $user->nickname = $nickname;

                    $user->site_id = $site->id;

                    // Компания и филиал
                    $user->author_id = 1;
                    $user->company_id = $company->id;
                    $user->filial_id = $filialId;
                    $user->saveQuietly();

                    $phone = $user->main_phone->phone;

                    // Конец апдейта юзеара
                };
            }
            // Конец работы с ПОЛЬЗОВАТЕЛЕМ для лида

            // Ищем клиента
            $user->load([
                'organizations'
            ]);
//            dd($user);

            $client = null;
            $organization = null;

            if ($user->organizations->isNotEmpty()) {
                $organizationsIds = $user->organizations->pluck('id');

                $client = Client::where([
                    'company_id' => $site->company_id,
                    'clientable_type' => 'App\Company'
                ])
                    ->whereIn('clientable_id', $organizationsIds)
                    ->first();

                if ($client) {
                    $organization = $client->clientable;
                } else {
                    $organization = $user->organizations->first();
                }

            } else {
                $client = $client = Client::where([
                    'company_id' => $site->company_id,
                    'clientable_type' => 'App\User',
                    'clientable_id' => $user->id
                ])
                    ->first();
            }

            // Создание ЛИДА ======================================================================
            $lead = new Lead;
            $lead->company_id = $company->id;
            $lead->filial_id = $filialId;
            $lead->user_id = $user->id;

            $lead->client_id = optional($client)->id;

            $lead->email = $request->email ?? '';
            $lead->name = $lead_name;
            $lead->company_name = $company_name;
            $lead->private_status = $private_status;
            $lead->location_id = create_location($request, 1, $site->filial->location->city_id);
            $lead->need_delivery = $request->get('need_delivery', 0);
            $lead->description = $description;
            $lead->stage_id = $request->stage_id ?? 2;
            $lead->badget = 0;
            $lead->lead_method_id = 2;
            $lead->draft = false;
            $lead->site_id = $site->id;

            $lead->author_id = 1;

            // if($request->choice_tag){
            //     $choiceFromTag = getChoiceFromTag($request->choice_tag);
            //     $lead->choice_type = $choiceFromTag['type'];
            //     $lead->choice_id = $choiceFromTag['id'];
            // } else {
            //     dd('Хм, нет цели обращения');
            // }

            // Ловим utm метки
            $utm_source = null;
            if ($request->cookie('utm_source') != null) {
                $utm_source = "Площадка: " . $request->cookie('utm_source');
                $lead->source_id = Source::where('utm', $request->cookie('utm_source'))->value('id');
            }

            $utm_term = null;
            if ($request->cookie('utm_term') != null) {
                $utm_term = "Клиент искал: " . $request->cookie('utm_term');
                $lead->utm_term = $request->cookie('utm_term');
            }

            if ($request->cookie('utm_content') != null) {
                $lead->utm_content = $request->cookie('utm_content');
            }

            if ($request->cookie('utm_campaign') != null) {
                $lead->campaign_id = Campaign::where('external', $request->cookie('utm_campaign'))->value('id');
            }

            $lead->saveQuietly();


            logs('leads_from_project')->info("============== Создан лид с сайта с id :[{$lead->id}], сайт:[{$site->id}]  ===============================");
            // ------------------------------------------- Конец создаем лида ---------------------------------------------

            // Если номера нет, пишем или ищем новый и создаем связь
            $new_phone = Phone::firstOrCreate(
                ['phone' => $cleanPhone
                ], [
                'crop' => substr($cleanPhone, -4),
            ]);

            $lead->phones()->attach($new_phone->id, [
                'phone_entity_type' => 'App\Lead',
                'main' => 1,
            ]);

            // $lead = update_location($request, $lead);

            // Создаем заказ для лида
            $estimate = Estimate::create([
                'lead_id' => $lead->id,
                'filial_id' => $lead->filial_id,
                'client_id' => $lead->client_id,
                'company_id' => $lead->company_id,
                'date' => today(),
                'number' => $lead->id,
                'author_id' => 1,
                'is_main' => true
            ]);
            logs('leads_from_project')->info("Создана смета с id: [{$estimate->id}]");

            // Если есть наполненная корзина, создаем смету на лиде
            if (isset($cart)) {

                $lead->load('estimate');
                $estimate = $lead->estimate;

                $prices_goods_ids = array_keys($cart['prices']);
                $prices_goods = PricesGoods::with('goods.article')
                    ->find($prices_goods_ids);

                $stockId = null;
                // Если включены настройки для складов, то проверяем сколько складов в системе, и если один, то берем его id
//                $settings = $site->company->settings;
//                if ($settings->isNotEmpty()) {
//                    $stocks = Stock::where('filial_id', $lead->filial_id)
//                        ->get([
//                            'id',
//                            'filial_id'
//                        ]);
//
//                    if ($stocks) {
//                        if ($stocks->count() == 1) {
//                            $stock_id = $stocks->first()->id;
//                        }
//                    }
//                }

                // Вписываем пункты сметы
                $estimatesGoodsItemsInsert = [];
                foreach ($prices_goods as $priceGoods) {

                    $count = $cart['prices'][$priceGoods->id]['count'];
                    $data = [
                        'estimate_id' => $estimate->id,
                        'price_id' => $priceGoods->id,

                        'goods_id' => $priceGoods->product->id,
                        'currency_id' => $priceGoods->currency_id,
                        'sale_mode' => 1,

                        'stock_id' => $stockId,

                        'cost_unit' => $priceGoods->product->article->cost_default,
                        'price' => $priceGoods->price,
                        'points' => $priceGoods->points,
                        'count' => $count,

                        'price_discount_id' => $priceGoods->price_discount_id,
                        'price_discount_unit' => $priceGoods->price_discount,

                        'catalogs_item_discount_id' => $priceGoods->catalogs_item_discount_id,
                        'catalogs_item_discount_unit' => $priceGoods->catalogs_item_discount,

                        'estimate_discount_id' => $priceGoods->estimate_discount_id,
                        'estimate_discount_unit' => $priceGoods->estimate_discount,

                        'client_discount_percent' => $client ? $client->discount : 0,

                        'manual_discount_currency' => 0,

                        'author_id' => 1,
                        'company_id' => $estimate->company_id,
                    ];

                    $estimatesGoodsItemsInsert[] = EstimatesGoodsItem::make($data);
                }

                $estimate->goods_items()->saveMany($estimatesGoodsItemsInsert);
                logs('leads_from_project')->info("Записаны товары сметы");

            }

            // Аггрегируем значения сметы
            $estimate->load([
                'goods_items',
                'services_items',
            ]);

            $cost = 0;
            $amount = 0;
            $points = 0;

            $priceDiscount = 0;
            $catalogsItemDiscount = 0;
            $estimateDiscount = 0;
            $clientDiscount = 0;
            $manualDiscount = 0;

            $total = 0;
            $totalPoints = 0;
            $totalBonuses = 0;

            $count = 0;

            if ($estimate->goods_items->isNotEmpty()) {
                $cost += $estimate->goods_items->sum('cost');
                $amount += $estimate->goods_items->sum('amount');
                $points += $estimate->goods_items->sum('points');

                $priceDiscount += $estimate->goods_items->sum('price_discount');
                $catalogsItemDiscount += $estimate->goods_items->sum('catalogs_item_discount');
                $estimateDiscount += $estimate->goods_items->sum('estimate_discount');
                $clientDiscount += $estimate->goods_items->sum('client_discount_currency');
                $manualDiscount += $estimate->goods_items->sum('manual_discount_currency');

                $total += $estimate->goods_items->sum('total');
                $totalPoints += $estimate->goods_items->sum('total_points');
                $totalBonuses += $estimate->goods_items->sum('total_bonuses');

                $count += $estimate->goods_items->sum('count');
            }

//        if ($estimate->services_items->isNotEmpty()) {
//            $cost += $estimate->services_items->sum('cost');
//            $amount += $estimate->services_items->sum('amount');
//            $total += $estimate->services_items->sum('total');
//        }

            // Скидки
            $discountCurrency = 0;
            $discountPercent = 0;
            if ($total > 0) {
                $discountCurrency = $amount - $total;
                $discountPercent = $discountCurrency * 100 / $amount;
            }

            // Маржа
            $marginCurrency = $total - $cost;
            if ($total > 0) {
                $marginPercent = ($marginCurrency / $total * 100);
            } else {
                $marginPercent = $marginCurrency * 100;
            }

            $estimate->cost = $cost;
            $estimate->amount = $amount;
            $estimate->points = $points;

            $estimate->price_discount = $priceDiscount;
            $estimate->catalogs_item_discount = $catalogsItemDiscount;
            $estimate->estimate_discount = $estimateDiscount;
            $estimate->client_discount = $clientDiscount;
            $estimate->manual_discount = $manualDiscount;

            $estimate->discount_currency = $discountCurrency;
            $estimate->discount_percent = $discountPercent;

            $estimate->total = $total;
            $estimate->total_points = $totalPoints;
            $estimate->total_bonuses = $totalBonuses;

            $estimate->margin_currency = $marginCurrency;
            $estimate->margin_percent = $marginPercent;

            $estimate->save();

            // Обновляем бюджет лида
            // TODO - 23.10.19 - Сделать адекватное сохранение в корзине
            $lead->badget = $total;
            $lead->order_amount_base = $total;
            $lead->save();

            // Оповещение
            // Получаем сайт
            $phone = cleanPhone($request->main_phone);

            // Формируем сообщение
            $message = "Заказ с сайта: №{$lead->id}\r\n";

            if ($site->domains->count() > 1) {
                $message .= "Город: {$site->filial->location->city->name}\r\n";
            }

            $message .= "Имя клиента: {$lead->name}\r\n";
            $message .= "Тел: " . decorPhone($phone) . "\r\n";
            if ($lead->description) {
                $message .= "Примечание: {$lead->description}\r\n";
            };

            if ($estimate->goods_items->isNotEmpty()) {
                $estimate->goods_items->load([
                    'goods.article'
                ]);

                $message .= "\r\nСостав заказа:\r\n";
                $num = 1;
                foreach ($estimate->goods_items as $item) {
                    $message .= $num . ' - ' . $item->goods->article->name . ": " . num_format($item->count, 0) .
                        ' ' . $item->goods->article->unit->abbreviation . " (" . num_format($item->total, 0) . " руб.) \r\n";
                    $num++;
                }
                $message .= "\r\n";
            }

            $message .= "Кол-во товаров: " . num_format($count, 0) . "\r\n";
            $message .= "Сумма заказа: " . num_format($estimate->amount, 0) . ' руб.' . "\r\n";

            if ($estimate->discount_currency > 0) {
                $message .= "Сумма со скидкой: " . num_format($estimate->total, 0) . ' руб.' . "\r\n";
                $message .= "Скидка: " . num_format($estimate->discount_currency, 0) . ' руб.' . "\r\n";
            }
            $message .= "\r\n";

            // Маржа
            $message .= ($estimate->margin_currency < 0) ? "Убыток: " : "Маржинальность: ";
            $message .= num_format($estimate->margin_currency, 0) . " руб. (" . round($estimate->margin_percent, 2) . "%)\r\n";
            $message .= "\r\n";

            // Ролл Хаус
            if ($request->has('address')) {
                $message .= "Адрес: {$request->address}\r\n";
            }

            if ($request->has('comment')) {
                $message .= "Комментарий: {$request->comment}\r\n";
            }

            if ($request->has('need_delivery')) {
                $need_delivery = $request->need_delivery == 1 ? 'да' : 'самовывоз';
                $message .= "Доставка: {$need_delivery}\r\n";
            }

            if ($request->has('card')) {
                $card = $request->card == 1 ? 'по карте' : 'наличный расчет';
                $message .= "Оплата: {$card}\r\n";
            }

            $message .= "\r\n";
            if ($utm_source) {
                $message .= $utm_source . "\r\n";
            }
            if ($utm_term) {
                $message .= $utm_term . "\r\n";
            }

            $lead->notes()->create([
                'company_id' => $company->id,
                'body' => $message,
                'author_id' => 1,
            ]);

            $destinations = \App\User::whereHas('staff', function ($query) {
                $query->whereHas('position', function ($query) {
                    $query->whereHas('notifications', function ($query) {
                        $query->where('notification_id', 1);
                    });
                });
            })
                ->where('company_id', $site->company_id)
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

            // Чистим корзину у пользователя
            Cookie::queue(Cookie::forget('cart'));
            logs('leads_from_project')->info("Очищены куки");

            // Пишем в сессию пользователю данные нового лида

            // Создаем массив для хранения данных заказа
            $confirmation = [];

            // Сохраняем в него лида
            $confirmation['lead'] = $lead;
            // $confirmation['...'] = Что-нибудь еше, если будет необходимо...

            // Пишем в сессию
            session(['confirmation' => $confirmation]);

            logs('leads_from_project')->info("============== Создан лид с сайта ===============================

            ");

            return redirect()->route('project.confirmation');
        } else {
            abort(403, 'Ну вы и Хакер!');
        }
    }

    public function update_cookies(Request $request)
    {
//        dd($request->goodsItems);
        $cart = [];
        $sum = 0;
        $count = 0;
        if ($request->has('goodsItems')) {
            if (count($request->goodsItems) > 0) {
                $result = Cookie::queue(Cookie::forget('cart'));
                foreach ($request->goodsItems as $goodsItem) {

                    $cart['prices'][$goodsItem['id']] = [
                        'count' => $goodsItem['quantity'],
                        'price' => $goodsItem['price'],
                    ];
                }

                $result = Cookie::queue(Cookie::forever('cart', json_encode($cart)));
                return response()->json([
                    'success' => true,
                    'result' => $result
                ]);
            } else {
                $result = Cookie::queue(Cookie::forget('cart'));
                return response()->json($result);
            }
        } else {
            $result = Cookie::queue(Cookie::forget('cart'));
            return response()->json($result);
        }
    }

    public function check_prices(Request $request)
    {
        $result['success'] = true;

        if (Cookie::get('cart') !== null) {

            $cart = json_decode(Cookie::get('cart'), true);

            if (count($cart['prices']) > 0) {
                // Проверка на различие цены
                $prices = $cart['prices'];
                $prices_ids = array_keys($cart['prices']);
                $prices_goods = PricesGoods::with('goods.article.photo', 'currency')
                    ->find($prices_ids);

                foreach ($prices_goods as $price_goods) {
                    if ($price_goods->price != $prices[$price_goods->id]['price']) {
                        $result['changes'][] = $price_goods;
                    }
                }
                if (isset($result['changes'])) {
                    $result['success'] = false;
                }
            }
        }
        return response()->json($result);
    }
}
