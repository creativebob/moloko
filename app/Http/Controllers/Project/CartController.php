<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Traits\EstimateControllerTrait;
use App\Http\Controllers\Traits\LeadControllerTrait;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\PricesGoods;
use App\Site;
use App\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Telegram;

class CartController extends Controller
{

    // Настройки сконтроллера
    public function __construct()
    {
//        $this->middleware('auth');
        $domain = request()->getHost();

        $site = Site::where('domain', $domain)
            ->with([
                'pages_public',
                'filials'
            ])
            ->first();
//        dd($site);

        $this->site = $site;
    }

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


            // $cart_test = json_decode(Cookie::get('cart_test'), true);
            // dd($cart_test);

            $prices_ids = array_keys($cart['prices']);
//            dd($prices_ids);

            $prices_goods = PricesGoods::with('goods_public')
                ->find($prices_ids);

            foreach($cart['prices'] as $id => $price) {
                $price_goods = $prices_goods->firstWhere('id', $id);
                $price_goods->count = $price['count'];
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
        return view($site->alias.'.pages.cart.index', compact('site', 'page', 'prices_goods'));
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

            // Создаем лида
            $lead = $this->createLeadFromSite($request);

            // Если есть наполненная корзина, создаем смету на лиде
            if(isset($cart)){
                $estimate = $this->createEstimateFromCart($cart, $lead);
                $discount_percent = 10;

                // Пока статично вписываем скидку и размер суммы со скидкой
                $total = $lead->badget - ($lead->badget * 10 / 100);
                $discount = $lead->badget * 10 / 100;

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
            $message .= "Сумма со скидкой: " . num_format($estimate->total, 0) . ' руб.' . "\r\n";
            $message .= "Скидка: " . num_format($estimate->discount, 0) . ' руб.' . "\r\n";

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
                $cartGood = json_decode($cartGood, true);
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
