<?php

namespace App\Http\Controllers\Project;

use App\Campaign;
use App\Http\Controllers\Project\Traits\Commonable;
use App\Http\Controllers\Traits\EstimateControllerTrait;
use App\Http\Controllers\Traits\LeadControllerTrait;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Lead;
use App\Models\Project\Estimate;
use App\Models\Project\EstimatesGoodsItem;
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
     * Display a listing of the resource.
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
        $site->load([
            'promotions' => function ($q) use ($filial_id) {
                $q->with([
                    'prices_goods' => function ($q) use ($filial_id) {
                        $q->with([
                            'goods_public' => function ($q) {
                                $q->with([
                                    'article.photo',
                                    'metrics.values'
                                ]);

                            },
                            'currency',
                            'catalogs_item.directive_category:id,alias',
                            'catalogs_item.parent'
                        ])
                        ->where('filial_id', $filial_id);
                    }
                ])
                    ->where([
                        'is_upsale' => true,
                        'display' => true
                    ])
                    ->where('begin_date', '<=', today())
                    ->where('end_date', '>=', today())
                ;
            }
        ]);
        $page = $site->pages_public->firstWhere('alias', 'cart');
        return view($site->alias.'.pages.cart.index', compact('site',  'page', 'prices_goods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
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

                    $user->location_id = create_location($request, $country_id = 1, $city_id = 1);

                    $user->first_name = $first_name;
                    $user->second_name = $second_name;
                    $user->nickname = $nickname;

                    $user->site_id = $site->id;

                    // Компания и филиал
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
            $lead->location_id = create_location($request, $country_id = 1, $site->filial->location->city_id);

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

                // TODO - 15.11.19 - Склад должен браться из настроек, пока берем первый по филиалу
                $stock_id = Stock::where('filial_id', $lead->filial_id)->value('id');

                // Находим или создаем заказ для лида
                $estimate = Estimate::create([
                    'lead_id' => $lead->id,
                    'filial_id' => $lead->filial_id,
                    'company_id' => $lead->company->id,
                    'stock_id' => $stock_id,
                    'date' => now()->format('Y-m-d'),
                    'number' => $lead->case_number,
                    'author_id' => $lead->author_id,

                ]);

                logs('leads_from_project')->info("Создана смета с id: [{$estimate->id}]");

                $prices_goods_ids = array_keys($cart['prices']);
                $prices_goods = PricesGoods::with('goods')
                    ->find($prices_goods_ids);

                $data = [];
                foreach ($prices_goods as $price_goods) {
                    $data[] = new EstimatesGoodsItem([
                        'goods_id' => $price_goods->goods->id,

                        'price_id' => $price_goods->id,
                        'stock_id' => $estimate->stock_id,

                        'company_id' => $lead->company->id,
                        'author_id' => 1,

                        'price' => $price_goods->price,
                        'count' => $cart['prices'][$price_goods->id]['count'],

                        'amount' => $cart['prices'][$price_goods->id]['count'] * $price_goods->price
                    ]);
                }

                $estimate->goods_items()->saveMany($data);
                logs('leads_from_project')->info("Записаны товары сметы");
                // TODO - 15.11.19 - Скидка должна браться из ценовой политики
                $discount_percent = 0;

                // Пока статично вписываем скидку и размер суммы со скидкой
                $total = $lead->badget - ($lead->badget * $discount_percent / 100);
                $discount = $lead->badget * $discount_percent / 100;

                $estimate->amount = $lead->badget;
                $estimate->total = $total;
                $estimate->discount = $discount;
                $estimate->discount_percent = $discount_percent;
                $estimate->save();

                // TODO - 23.10.19 - Сделать адекватное сохранение в корзине
                $lead->badget = $total;
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

            if($discount > 0){
                $message .= "Сумма со скидкой: " . num_format($estimate->total, 0) . ' руб.' . "\r\n";
                $message .= "Скидка: " . num_format($estimate->discount, 0) . ' руб.' . "\r\n";
            }

            $message .= "\r\n";

            // Ролл Хаус
            if ($request->has('address')) {
                $message .= "Адрес: {$request->address}\r\n";
            };
            if ($request->has('comment')) {
                $message .= "Комментарий: {$request->comment}\r\n";
            };
            if ($request->has('pickup')) {
                $pickup = $request->pickup == 1 ? 'самовывоз' : 'да';
                $message .= "Доставка: {$pickup}\r\n";
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
