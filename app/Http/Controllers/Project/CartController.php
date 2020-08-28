<?php

namespace App\Http\Controllers\Project;

use App\Campaign;
use App\Discount;
use App\Http\Controllers\Project\Traits\Commonable;
use App\Http\Controllers\Traits\EstimateControllerTrait;
use App\Http\Controllers\Traits\LeadControllerTrait;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Lead;
use App\Models\Project\Estimate;
use App\Models\Project\EstimatesGoodsItem;
use App\Models\Project\Promotion;
use App\PricesGoods;
use App\Source;
use App\Stock;
use App\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Telegram;
use Telegram\Bot\Exceptions\TelegramResponseException;

class CartController extends Controller
{

    use Commonable;
    use UserControllerTrait;
    use LeadControllerTrait;
    use EstimateControllerTrait;

    /**
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        if (Cookie::has('cart')) {
//            $cart = json_decode(Cookie::get('cart'), true);
//
//            // dd($cart);
//
//            if (isset($cart['prices'])) {
//                $prices_ids = array_keys($cart['prices']);
////            dd($prices_ids);
//
//                $prices_goods = PricesGoods::with('goods_public.article.photo', 'currency')
//                    ->find($prices_ids);
//
//                // dd($prices_goods->first()->goods->article);
//
//                foreach($cart['prices'] as $id => $price) {
//                    $price_goods = $prices_goods->firstWhere('id', $id);
//                    $price_goods->count = $price['count'];
//                }
//            } else {
//                $prices_goods = [];
//                $prices_goods = collect($prices_goods);
//            }
////            dd($prices_goods);
//        } else {
//            $prices_goods = [];
//            $prices_goods = collect($prices_goods);
//        }
//
//        $prices_goods = $prices_goods->toArray();
//        $prices_goods = collect($prices_goods);


        // dd($prices_goods);

        $site = $this->site;

        // Грузим продвижения с отображением на корзине
        $filial_id = $site->filial->id;

        $prom = json_decode(\Cookie::get('prom'), true) ?? request()->prom;

        $promotions = Promotion::with([
            'prices_goods' => function ($q) use ($filial_id) {
                $q->with([
                    'goods',
                    'currency',
                    'catalogs_item.directive_category:id,alias',
                    'catalogs_item.parent'
                ])
                    ->where('filial_id', $filial_id);
            }
        ])
            ->where([
                'site_id' => $site->id,
                'is_upsale' => true,
                'display' => true
            ])
            ->whereHas('filials', function($q) use ($filial_id) {
                $q->where('id', $filial_id);
            })
            ->where('begin_date', '<=', today())
            ->where('end_date', '>=', today())
            ->whereNull('prom')
            ->when($prom, function ($q) use ($site, $prom) {
                $q->orWhere(function($q) use ($site, $prom) {
                    $q->display()
                        ->company($site->company_id)
                        ->whereHas('filials', function($q) use ($site) {
                            $q->where('id', $site->filial->id);
                        })
                        ->where('is_slider', true)
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
            'display' => true,
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

        return view($site->alias.'.pages.cart.index', compact('site',  'page', 'prices_goods', 'promotions', 'discount'));
    }

    /**
     * Показать форму для создания нового ресурса.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Сохранение созданного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Если пользователь дал согласие на обработку персональных данных
        if ($request->personal_data) {

            // Содержится ли в куках данные корзины
            if(Cookie::get('cart') !== null){
                $cart = json_decode(Cookie::get('cart'), true);

//                if (count($cart['prices']) > 0) {
//                    // Проверка на различие цены
//                    $prices = $cart['prices'];
//                    $prices_ids = array_keys($cart['prices']);
//                    $prices_goods = PricesGoods::with('goods.article.photo', 'currency')
//                    ->find($prices_ids);
//
//                    $result = [];
//                    foreach ($prices_goods as $price_goods) {
//                        if ($price_goods->price != $prices[$price_goods->id]['price']) {
//                            $result[] = $price_goods;
//                        }
//                    }
//                    dd(count($result) > 0);
//                }
            }

            // Собираем для request недостающие данные или преобразовываем

            // Вкусняшка
            $lead_type = $request->has('lead_type') ? $request->lead_type : null;

            $school_number = $request->has('school_number') ? $request->school_number : null;
            $class_number = $request->has('class_number') ? $request->class_number : null;

            $kindergarten_number = $request->has('kindergarten_number') ? $request->kindergarten_number : null;
            $kindergarten_group = $request->has('kindergarten_group') ? $request->kindergarten_group : null;

            $company_name = $request->has('company_name') ? $request->company_name : null;

            if($lead_type == "Школа"){
                $description = $lead_type . ' №' . $school_number . ', класс ' . $class_number;
            }

            if($lead_type == "Детский сад"){
                $description = $lead_type . ' №' . $kindergarten_number . ', группа ' . $kindergarten_group;
            }

            if($lead_type == "Компания"){
                $description = $lead_type . ' ' . $company_name;
            }

            // Отдаем недостающий description
            if(isset($description)){
                $request->description = $description;
            }

            // TODO - 03.12.19 - Вынести в отдельные методы для сайта
            // ------------------------------------------- Создаем лида ---------------------------------------------
//            $lead = $this->createLeadFromSite($request);

            // Готовим необходимые данные ======================================================================
            // Получаем сайт
            $site = $this->site;
            $company = $site->company;

            // Если не пришел филиал, берем первый у компании
            $filial_id = $this->site->filial->id;



            $first_name = isset($request->first_name) ? $request->first_name : 'Клиент не указал имя';

            $nickname = $request->name;
            $second_name = $request->second_name;

            if(($first_name == null)&&($second_name == null)){
                if($nickname == null){
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
            if($company_name){
                $private_status = 1;
            } else {
                $private_status = 0;
            }

            $phone = cleanPhone($request->main_phone);

            // Содержится ли в куках данные корзины
            if(Cookie::get('cart') !== null){

                $count = 0; $badget = 0;

                $cart = json_decode(Cookie::get('cart'), true);
                $badget = $cart['sum'];
                $count = $cart['count'];
            }


            // Работаем с ПОЛЬЗОВАТЕЛЕМ для лида ================================================================

            // Если пользователь АВТОРИЗОВАН
            $user = Auth::User();
            if($user){

                // Формируем имя записи в лида
                if(empty($lead_name)){
                    $lead_name = $user->first_name . ' ' . $user->second_name;
                }

                $phone = $user->main_phone->phone;

                // Если пользователь НЕ авторизован
            } else {

                if(!isset($request->main_phone)){abort(403, 'Не указан номер телефона!');}

                // Получаем юзера если такой пользователь есть в базе по указанному номеру
                $user = checkPhoneUserForSite($request->main_phone, $site);


                // Если нет, то создадим нового
                if (empty($user)) {

                    // Если нет: создаем нового пользователя по номеру телефона
                    // используя трейт экспресс создание пользователя
                    $user = $this->createUserByPhone($request->main_phone, null, $site->company);

                    // sendSms('79041248598', 'Данные для входа: ' . $user->access_code);

                    $user->location_id = create_location($request, 1, $site->filial->location->city_id);

                    $user->first_name = $first_name;
                    $user->second_name = $second_name;
                    $user->nickname = $nickname;

                    $user->site_id = $site->id;

                    // Компания и филиал
                    $user->author_id = 1;
                    $user->company_id = $company->id;
                    $user->filial_id = $filial_id;
                    $user->save();

                    $phone = $user->main_phone->phone;

                    // Конец апдейта юзеара
                };
            }

            // Конец работы с ПОЛЬЗОВАТЕЛЕМ для лида


            // Создание ЛИДА ======================================================================
            $lead = new Lead;
            $lead->company_id = $company->id;
            $lead->filial_id = $filial_id;
            $lead->user_id = $user->id;
            $lead->email = $request->email ?? '';
            $lead->name = $lead_name;
            $lead->company_name = $company_name;
            $lead->private_status = $private_status;
            $lead->location_id = create_location($request, 1, $site->filial->location->city_id);
            $lead->need_delivery = $request->get('need_delivery', 0);

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

            $lead->description = $description;
            $lead->stage_id = $request->stage_id ?? 2; // Этап: "обращение"" по умолчанию
            $lead->badget = $badget ?? 0;
            $lead->lead_method_id = 2; // Способ обращения: "звонок"" по умолчанию
            $lead->draft = null;
            $lead->site_id = $site->id;

            $lead->author_id = 1;
//        $lead->editor_id = 1;

            // if($request->choice_tag){
            //     $choiceFromTag = getChoiceFromTag($request->choice_tag);
            //     $lead->choice_type = $choiceFromTag['type'];
            //     $lead->choice_id = $choiceFromTag['id'];
            // } else {
            //     dd('Хм, нет цели обращения');
            // }

            $lead->save();

            logs('leads_from_project')->info("============== Создан лид с сайта с id :[{$lead->id}], сайт:[{$this->site->domain->domain}]  ===============================");
            // ------------------------------------------- Конец создаем лида ---------------------------------------------

            // Телефон
            $request->main_phone = $phone;
            $phones = add_phones($request, $lead);
            // $lead = update_location($request, $lead);

            // Если есть наполненная корзина, создаем смету на лиде
            if(isset($cart)){
//                $estimate = $this->createEstimateFromCart($cart, $lead);

                // Находим или создаем заказ для лида
                $estimate = Estimate::create([
                    'lead_id' => $lead->id,
                    'filial_id' => $lead->filial_id,
                    'company_id' => $lead->company->id,
                    'date' => today(),
                    'number' => $lead->case_number,
                    'author_id' => $lead->author_id,
                    'is_main' => true
                ]);

//                $discounts = Discount::where('company_id', $company->id)
//                    ->whereHas('entity', function ($q) {
//                        $q->where('alias', 'estimates');
//                    })
//                    ->where('archive', false)
//                    ->where('begined_at', '<=', now())
//                    ->where(function ($q) {
//                        $q->where('ended_at', '>=', now())
//                            ->orWhereNull('ended_at');
//                    })
//                    ->get();
////              dd($discounts);
//
//                $discountsIds = $discounts->pluck('id');

                $lead->load('estimate');
                $estimate = $lead->estimate;

//                $estimate->discounts()->attach($discountsIds);

                logs('leads_from_project')->info("Создана смета с id: [{$estimate->id}]");

                $prices_goods_ids = array_keys($cart['prices']);
                $prices_goods = PricesGoods::with('goods.article')
                    ->find($prices_goods_ids);

                $stock_id = null;
                // Если включены настройки для складов, то проверяем сколько складов в системе, и если один, то берем его id
                $settings = $this->site->company->settings;
                if ($settings->isNotEmpty()) {
                    $stocks = Stock::where('filial_id', $lead->filial_id)
                        ->get([
                            'id',
                            'filial_id'
                        ]);

                    if ($stocks) {
                        if ($stocks->count() == 1) {
                            $stock_id = $stocks->first()->id;
                        }
                    }
                }

                $estimatesGoodsItemsInsert = [];
                foreach ($prices_goods as $price_goods) {
                    $count = $cart['prices'][$price_goods->id]['count'];
                    $data = [
                        'currency_id' => 1,
                        'goods_id' => $price_goods->goods->id,
                        'price_id' => $price_goods->id,
                        'author_id' => 1,
                        'company_id' => $estimate->company_id,
                        'price' => $price_goods->price,
                        'count' => $count,
                        'cost' => $price_goods->goods->article->cost_default * $count,
                        'amount' => $count * $price_goods->price,

                        'price_discount_id' => $price_goods->price_discount_id,
                        'price_discount' => $price_goods->price_discount,
                        'total_price_discount' => $price_goods->total_price_discount,

                        'catalogs_item_discount_id' => $price_goods->catalogs_item_discount_id,
                        'catalogs_item_discount' => $price_goods->catalogs_item_discount,
                        'total_catalogs_item_discount' => $price_goods->total_catalogs_item_discount,

                        'estimate_discount_id' => $price_goods->estimate_discount_id,
                        'estimate_discount' => $price_goods->estimate_discount,
                        'total_estimate_discount' => $price_goods->total_estimate_discount,

                        'total' => $count * $price_goods->total_estimate_discount,
                    ];

                    $data['margin_currency'] = $data['total'] - $data['cost'];
                    $data['margin_percent'] = ($data['margin_currency'] / $data['total'] * 100);

                    $estimatesGoodsItemsInsert[] = EstimatesGoodsItem::make($data);
                }

                $estimate->goods_items()->saveMany($estimatesGoodsItemsInsert);
                logs('leads_from_project')->info("Записаны товары сметы");

                $estimate->load([
                    'goods_items',
                    'discounts'
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

                $discountCurrency = 0;
                $discountPercent = 0;

//                if ($total > 0) {
//                    if ($estimate->discounts->isNotEmpty()) {
//                        $discount = $estimate->discounts->first();
//
//                        switch ($discount->mode) {
//                            case(1):
//                                $discountCurrency = $total / 100 * $discount->percent;
//                                $discountPercent = $discount->percent;
//                                break;
//                            case(2):
//                                $discountCurrency = $discount->currency;
//                                $percent = $total / 100;
//                                $discountPercent = $discount->currency / $percent;
//                                break;
//                        }
//
//                        $total -= $discountCurrency;
//                    }
//                }

                $estimate->amount = $lead->badget;
                $estimate->total = $total;
                $estimate->discount_currency = $discountCurrency;
                $estimate->discount_percent = $discountPercent;

                $estimate->cost = $estimate->goods_items->sum('cost');
                $estimate->margin_currency = $estimate->total - $estimate->cost;
                $estimate->margin_percent = ($estimate->margin_currency / $estimate->total * 100);

                $estimate->save();

                // TODO - 23.10.19 - Сделать адекватное сохранение в корзине
                $lead->badget = $total;
                $lead->order_amount_base = $total;
                $lead->save();

            }

            // Оповещение
            // Получаем сайт
            $site = $this->site;
            $company = $site->company;
            $phone = cleanPhone($request->main_phone);

            $count = 0;
            $badget = 0;

            // Содержится ли в куках данные корзины
            if(Cookie::get('cart') !== null){

                $cart = json_decode(Cookie::get('cart'), true);
                $badget = $cart['sum'];
                $count = $cart['count'];
            }

            // Формируем сообщение
            $message = "Заказ с сайта: №" . $lead->id . "\r\n";

            if ($site->domains->count() > 1) {
                $message .= "Город: " . $site->filial->location->city->name . "\r\n";
            }

            $message .= "Имя клиента: " . $lead->name . "\r\n";
            $message .= "Тел: " . decorPhone($phone) . "\r\n";
            if($lead->description){$message .= "Примечание: " . $lead->description . "\r\n";};

            if ($estimate->goods_items->isNotEmpty()) {
                $estimate->goods_items->load([
                    'product.article'
                ]);

                $message .= "\r\nСостав заказа:\r\n";
                $num = 1;
                foreach ($estimate->goods_items as $item) {
                    $message .= $num . ' - ' . $item->product->article->name . ": " . num_format($item->count, 0) .
                        ' ' . $item->product->article->unit->abbreviation .

                        " (" . num_format($item->amount, 0) . " руб.) \r\n";
                    $num++;
                }
                $message .= "\r\n";
            }

            $message .= "Кол-во товаров: " . num_format($count, 0) . "\r\n";
            $message .= "Сумма заказа: " . num_format($estimate->amount, 0) . ' руб.' . "\r\n";

            if($discountCurrency > 0){
                $message .= "Сумма со скидкой: " . num_format($estimate->total, 0) . ' руб.' . "\r\n";
                $message .= "Скидка: " . num_format($estimate->discount_currency, 0) . ' руб.' . "\r\n";
            }

            $message .= "\r\n";
            // Маржа
            $message .= "Маржинальность: " . num_format($estimate->margin_currency, 0) . " руб. (" . round($estimate->margin_percent, 2) . "%)\r\n";
            $message .= "\r\n";

            // Ролл Хаус
            if ($request->has('address')) {
                $message .= "Адрес: {$request->address}\r\n";
            };
            if ($request->has('comment')) {
                $message .= "Комментарий: {$request->comment}\r\n";
            };
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

            $destinations = User::whereHas('staff', function ($query) {
                $query->whereHas('position', function ($query) {
                    $query->whereHas('notifications', function ($query) {
                        $query->where('notification_id', 1);
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
        }
    }

    /**
     * Отображение указанного ресурса.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function update_cookies(Request $request)
    {
//        dd($request->cartGoods);
        $cart = [];
        $sum = 0;
        $count = 0;
        if ($request->has('cartGoods')) {
            if (count($request->cartGoods) > 0) {
                $result = Cookie::queue(Cookie::forget('cart'));
                foreach($request->cartGoods as $cartGood) {
    //                $cartGood = json_decode($cartGood, true);

                    $cart['prices'][$cartGood['id']] = [
                        'count' => $cartGood['quantity'],
                        'price' => $cartGood['price'],
                    ];

                    $sum += $cartGood['totalPrice'];
                    $count += $cartGood['quantity'];
                }

                $cart['sum'] = $sum;
                $cart['count'] = $count;

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
//        dd($cart);


//        if (array_key_exists($id, $cart['prices'])) {
//            $new_count = $cart['prices'][$id]['count'] + $count;
//            $cart['prices'][$id] = [
//                'count' => $new_count
//            ];
//        } else {
//            $cart['prices'][$id] = [
//                'count' => $count
//            ];
//            $cart['count'] = $count;
//            $cart['sum'] = $price_goods->price * $count;
//        }
//        $cart['count'] += $count;
//        $cart['sum'] += ($price_goods->price * $count);
////            dd($cart);
//    } else {
//        $cart['prices'][$id] = [
//        'count' => $count
//        }
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
