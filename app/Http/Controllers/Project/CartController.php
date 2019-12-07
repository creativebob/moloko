<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Project\Traits\Commonable;
use App\Http\Controllers\Traits\EstimateControllerTrait;
use App\Http\Controllers\Traits\LeadControllerTrait;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Lead;
use App\PricesGoods;
use App\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Telegram;

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
        if (Cookie::has('cart')) {
            $cart = json_decode(Cookie::get('cart'), true);

            // dd($cart);

            if (isset($cart['prices'])) {
                $prices_ids = array_keys($cart['prices']);
//            dd($prices_ids);

                $prices_goods = PricesGoods::with('goods_public.article.photo', 'currency')
                    ->find($prices_ids);

                // dd($prices_goods->first()->goods->article);

                foreach($cart['prices'] as $id => $price) {
                    $price_goods = $prices_goods->firstWhere('id', $id);
                    $price_goods->count = $price['count'];
                }
            } else {
                $prices_goods = [];
                $prices_goods = collect($prices_goods);
            }
//            dd($prices_goods);
        } else {
            $prices_goods = [];
            $prices_goods = collect($prices_goods);
        }

        $prices_goods = $prices_goods->toArray();
        $prices_goods = collect($prices_goods);


        // dd($prices_goods);

        $site = $this->site;

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

            $nickname = $request->name;
            $first_name = $request->first_name;
            $second_name = $request->second_name;

            if(($first_name == null)&&($second_name == null)){
                if($nickname == null){
                    $lead_name = null;
                } else {
                    $lead_name = $nickname;
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
                $user = check_user_by_phones($request->main_phone);


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
            $lead->location_id = create_location($request, $country_id = 1, $city_id = 1);

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
            // ------------------------------------------- Конец создаем лида ---------------------------------------------

            // Телефон
            $request->main_phone = $phone;
            $phones = add_phones($request, $lead);
            // $lead = update_location($request, $lead);

            // Если есть наполненная корзина, создаем смету на лиде
            if(isset($cart)){
                $estimate = $this->createEstimateFromCart($cart, $lead);
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
            $site = getSite();
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
                    $message .= $num . ' - ' . $item->product->article->name . ": " . $item->count .
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

            // Ролл Хаус
            if ($request->has('address')) {
                $message .= "Адрес: {$request->address}\r\n";
            };
            if ($request->has('comment')) {
                $message .= "Комментарий: {$request->comment}\r\n";
            };
            $pickup = $request->has('pickup') ? 'Самовывоз' : 'Доставка';
            $message .= "Доставка: {$pickup}\r\n";

            $card = $request->has('card') ? 'по карте' : 'наличный расчет';
            $message .= "Оплата: {$card}\r\n";

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
                        $response = Telegram::sendMessage([
                            'chat_id' => $destination->telegram,
                            'text' => $message
                        ]);
                    }
                }
            }

            // Чистим корзину у пользователя
            Cookie::queue(Cookie::forget('cart'));

            // Пишем в сессию пользователю данные нового лида

            // Создаем массив для хранения данных заказа
            $confirmation = [];

            // Сохраняем в него лида
            $confirmation['lead'] = $lead;
            // $confirmation['...'] = Что-нибудь еше, если будет необходимо...


            // Пишем в сессию
            session(['confirmation' => $confirmation]);



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
                    'count' => $cartGood['quantity']
                ];

                $sum += $cartGood['totalPrice'];
                $count += $cartGood['quantity'];
            }

            $cart['sum'] = $sum;
            $cart['count'] = $count;

            $result = Cookie::queue(Cookie::forever('cart', json_encode($cart)));
            return response()->json($result);
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

}
