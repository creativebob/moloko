<?php

namespace App\Http\Controllers\Project;

use App\CatalogsGoods;
use Carbon\Carbon;
use Telegram;
use App\CatalogsGoodsItem;
use App\Estimate;
use App\EstimatesGoodsItem;
use App\Lead;
use App\PricesGoods;
use App\Site;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\Project\UserUpdateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;

use Illuminate\Support\Facades\Auth;

// Подрубаем трейт записи и обновления
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Http\Controllers\Traits\LeadControllerTrait;
use App\Http\Controllers\Traits\EstimateControllerTrait;

class AppController extends Controller
{

    // Настройки контроллера
    public function __construct(Request $request)
    {
        $request = request();
        $domain = $request->getHost();
        $site = Site::where('domain', $domain)
            ->with([
                'pages_public',
                'filials'
            ])
            ->first();
        $this->site = $site;
    }

    use UserControllerTrait;
    use LeadControllerTrait;
    use EstimateControllerTrait;

    public function start(Request $request)
    {
        if (is_null($this->site)) {
            return view('project.pages.main.main');
        } else {

            return redirect('catalogs-goods/tovary-dlya-sayta/tekstil');
            $site = $this->site;
            $page = $site->pages_public
                ->where('alias', 'main')
                ->first();

            return view($site->alias.'.pages.main.index', compact('site','page'));
        }
    }


    // Метод динамического формирования страницы
    public function dynamic_pages(Request $request, $page_alias)
    {

        if (is_null($this->site)) {

            return view('project.pages.main.main');

        } else {

            $site = $this->site;

            // Ищим в базе страницу с алиасом
            $page = $site->pages_public
                ->where('alias', $page_alias)
                ->first();

            // Если не существует страницы с таким алиасом - отдаем 404
            if(!isset($page)){

                abort(404, "Страница не найдена");
            }

            // Формируем путь до view которая предположительно должна существовать
            $path_view = $site->alias . '/pages/' . $page_alias . '/index';

            // Проверяем существование файла view по сформирванному пути
            if(view()->exists($path_view)){

                // Нашли - отправляем пользователя туда
                return view($site->alias.'.pages.' . $page_alias . '.index', compact('site','page'));  

            } else {

                // Не нашли. Но нет повода для печали, отправляем на общий шаблон
                return view($site->alias.'.pages.common.index', compact('site','page'));
            }
        }
    }


    public function catalogs_goods(Request $request, $url)
    {
//        dd(__METHOD__, $url);
        $arr = explode('/', $url);

        $catalog_slug = $arr[0];

        if (count($arr) > 1) {
            $sliced = array_slice($arr, 1);
            $slug = '';
            foreach($sliced as $lol) {
                $slug .= $lol . '/';
            }
            $catalog_item_slug = substr($slug, 0, -1);
        } else {
            $catalog_item_slug = null;
        }
        $site = $this->site;
        $page = $site->pages_public->where('alias', 'catalogs-goods')->first();

        // Получаем полный прайс со всеми доступными разделами
        $catalog_goods = CatalogsGoods::with('items_public')
            ->whereHas('sites', function ($q) use ($site) {
                $q->where('id', $site->id);
            })
            ->where('slug', $catalog_slug)
            ->where(['display' => true])
            ->first();

        if($catalog_item_slug){

            // Получаем разделы прайса ограниченный slug'ом
            $catalog_goods_items = $catalog_goods->items_public->where('slug', $catalog_item_slug);
            $page->title = $catalog_goods_items->first()->title;

        } else {

            // Получаем все доступные разделы прайса
            $catalog_goods_items = $catalog_goods->items_public;
            $page->title = 'Все товары';
        }

        // Получаем id всех доступных на сайте разделов прайса,
        // чтобы далее не заниматься повторным перебором при получении товаров
        $catalog_goods_items_ids = $catalog_goods_items->pluck('id');

        $prices_goods = PricesGoods::with([
            'goods_public' => function ($q) {
                $q->with([
                    'article' => function ($q) {
                        $q->with([
                            'raws' => function ($q) {
                                $q->with([
                                    'article.unit',
                                    'metrics'
                                ]);
                            },
                            'attachments' => function ($q) {
                                $q->with([
                                    'article.unit',
                                ]);
                            },
                            'containers' => function ($q) {
                                $q->with([
                                    'article.unit',
                                ]);
                            },
                        ]);
                    }
                ]);
            }
        ])
            ->whereIn('catalogs_goods_item_id', $catalog_goods_items_ids)
            ->has('goods_public')
            ->where([
                'display' => true,
                'archive' => false
            ])
            ->filter($request)
            ->orderBy('sort', 'asc')
            ->paginate(16);

        // Перебор и дописывание агрегаций
        // Нужен способ проще!
        foreach($prices_goods as $price_goods){
            $price_goods->sweets = $price_goods->goods_public->article->raws->filter(function ($value, $key) {
                if(isset($value->metrics->where('name', 'Тип сырья')->first()->pivot->value)){
                    return $value->metrics->where('name', 'Тип сырья')->first()->pivot->value == 1;
                }
            });

            $price_goods->addition = $price_goods->goods_public->article->raws->filter(function ($value, $key) {
                if(isset($value->metrics->where('name', 'Тип сырья')->first()->pivot->value)){
                    return $value->metrics->where('name', 'Тип сырья')->first()->pivot->value == 2;
                }
            });
        }

        return view($site->alias.'.pages.catalogs_goods.index', compact('site','page', 'request', 'catalog_goods_items', 'prices_goods', 'catalog_goods'));
    }


    public function catalogs_services(Request $request, $catalog_slug, $catalog_item_slug)
    {
        $site = $this->site;

        // Вытаскивает через сайт каталог и его пункт с прайсами (не архивными), товаром и артикулом
        $site->load(['catalogs_services' => function ($q) use ($catalog_slug, $catalog_item_slug) {
            $q->with([
                'items' => function($q) use ($catalog_item_slug) {
                    $q->with([
                        'prices_services' => function ($q) {
                            $q->with([
                                'service' => function ($q) {
                                    $q->with(['process' => function ($q) {
                                        $q->where([
                                            'draft' => false
                                        ]);
                                    }])
                                        ->where([
                                            'display' => true,
                                            'archive' => false
                                        ]);
                                }
                            ])
                                ->where([
                                    'display' => true,
                                    'archive' => false
                                ]);
                        }
                    ])
                        ->where([
                            'slug' => $catalog_item_slug,
                            'display' => true,
                        ]);
                }
            ])
                ->where([
                    'slug' => $catalog_slug,
                    'display' => true,
                ]);
        }]);
        dd($site->catalogs_services->first()->items->first());
    }

    public function prices_goods(Request $request, $id)
    {
        $site = $this->site;
        $page = $site->pages_public->where('alias', 'prices-goods')->first();

        $price_goods = PricesGoods::with([
            'goods_public.article.raws'
        ])
            ->where([
                'id' => $id,
                'display' => true
            ])
            ->first();

        // dd($price_goods->goods_public->article->containers);

        $page->title = $price_goods->goods_public->article->name;

        return view($site->alias.'.pages.prices_goods.index', compact('site','page', 'price_goods'));
    }

    public function goods_composition(Request $request, $id)
    {

        $site = $this->site;
        $price_goods = PricesGoods::with([
            'goods_public'
        ])
            ->where([
                'id' => $id,
                'display' => true
            ])
            ->first();

        return view($site->alias.'.pages.prices_goods.goods_composition', compact('site', 'price_goods'));
    }

    public function cart(Request $request)
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

    // Личный кабинет пользователя
    public function cabinet(Request $request)
    {

        $estimates = null;

        $site = $this->site;
        $page = $site->pages_public->firstWhere('alias', 'cabinet');

        return view($site->alias.'.pages.cabinet.index', compact('site', 'page', 'estimates'));
    }


    public function add_cart(Request $request)
    {
        $id = $request->id;

        $price_goods = PricesGoods::findOrFail($id);
//		dd($price_goods);

        $count = $request->count;

        if (Cookie::has('cart')) {
            $cart = json_decode(Cookie::get('cart'), true);
//            dd($cookie);
            if (array_key_exists($id, $cart['prices'])) {
                $new_count = $cart['prices'][$id]['count'] + $count;
                $cart['prices'][$id] = [
                    'count' => $new_count
                ];
            } else {
                $cart['prices'][$id] = [
                    'count' => $count
                ];
            }
            $cart['count'] += $count;
            $cart['sum'] += ($price_goods->price * $count);
//            dd($cart);
        } else {
            $cart['prices'][$id] = [
                'count' => $count
            ];
            $cart['count'] = $count;
            $cart['sum'] = $price_goods->price * $count;
        }
       // dd($cart);

        Cookie::queue(Cookie::forever('cart', json_encode($cart)));

        $site = $this->site;
        return view($site->alias.'.layouts.headers.includes.cart', compact('cart'));
    }

    // Авторизация пользоваеля сайта через телефон и код СМС
    public function site_user_login(Request $request)
    {
        $access_code = $request->access_code;
        $main_phone = $request->main_phone;

        // Получаем пользователя, который чужой
        $user = check_user_by_phones($main_phone);

        if($user != null){

            if(($user->user_type == false) && ($user->access_code == $access_code) && ($user->access_block == false)){

                Auth::loginUsingId($user->id);
                return redirect('/cabinet');

            } else {

                abort(403, 'Не верный код');
            }

        } else {

            dd('Мы не в курсе кто вы такие!');
        }
    }

    // Запрос на получение СМС кода на указанный телефон
    public function get_sms_code(Request $request)
    {
        $phone = cleanPhone($request->phone);

        $site = $this->site;
        $company = $site->company;

        // Смотрим, есть ли пользователь с таким номером телефона в базе
        $user = check_user_by_phones($phone);

        // Если пользователь не найден - то создаем
        if($user == null){
            $user = $this->createUserByPhone($phone, null, $company);
        }

        // Генерируем код доступа и записываем для пользователя
        $access_code = rand(1000, 9999);
        $user->access_code = $access_code;
        $user->save();

        if(session('time_get_access_code')){

            $second_blocking = 180 - session('time_get_access_code')->diffInSeconds(now());

            if($second_blocking < 1){

                // Пишем в сессию время отправки СМС
                session(['time_get_access_code' => now()]);
                $msg = 'Код для входа: ' . $access_code;
                sendSms($phone, $msg);
            };

        } else {

                // Пишем в сессию время отправки СМС
                session(['time_get_access_code' => now()]);
                $msg = 'Код для входа: ' . $access_code;
                sendSms($phone, $msg);

        }

        return 'ок';
    }

    public function logout_siteuser(Request $request)
    {
        Auth::logout();
        return redirect()->route('project.start');
    }

    // Сохранение данных пользователя
    public function update_profile(UserUpdateRequest $request)
    {

        //Получаем авторизованного пользователя
        $user = $request->user();

        $user->first_name = $request->first_name;
        $user->second_name = $request->second_name;
        $user->email = $request->email;
        $user->save();

    }

    public function add_to_cart(Request $request)
    {

//        return $request->cart;
//        dd(Cookie::get('cart'));
//        $cart = json_decode($request->cart, true);
        $old_cart = $request->cart;

        $cart = [];
        $count = 0;
        $sum = 0;
        foreach ($old_cart as $price) {

            $cart['prices'][$price['id']] = [
                'count' => $price['count']
            ];

            $count += $price['count'];
            $sum += ($price['price'] * $price['count']);

        }

        $cart['count'] = $count;
        $cart['sum'] = $sum;

//        dd($cart);
//
        if ($cart['count'] == 0 && $cart['sum'] == 0) {
            Cookie::queue(Cookie::forget('cart'));
        } else {
            Cookie::queue(Cookie::forever('cart', json_encode($cart)));
        }


//        return $cart;
//
        $site = $this->site;
        return view($site->alias.'.layouts.headers.includes.cart', compact('cart'));
    }

    public function cart_store(Request $request)
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

                // TODO - 23.10.19 - Сделать адекатное сохранение в корзине
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

    public function confirmation(Request $request)
    {

        $site = $this->site;
        $company = $site->company;
        $page = $site->pages_public->firstWhere('alias', 'confirmation');

        return view($site->alias.'.pages.confirmation.index', compact('site','page', 'company'));
    }

    public function get_access_code(Request $request)
    {

        $site = $this->site;

        // $company = $site->company;
        // return $company->accounts->where('alias', 'smssend')->first()->api_token;

        $confirmation = session('confirmation');

        // Если сессия найдена (Сессия может закончится по времени)
        if($confirmation){

            $lead = $confirmation['lead'];

            // Если найден лид
            if($lead){

                if(!isset($lead->user)){return 'Пользователь не существует';}
                $user = $lead->user;

                // Проверяем, не частит ли пользователь с запросом кода


                // Конец проверки

                // Генерируем код доступа и записываем для пользователя
                $access_code = rand(1000, 9999);
                $user->access_code = $access_code;
                $user->save();

                 // Пишем в сессию время отправки СМС
                session(['time_get_access_code' => now()]);

                $phone = $lead->user->main_phone->phone;
                $msg = 'Код для входа: ' . $access_code;
                sendSms($phone, $msg);

                return 'ок';


            } else {

                return 'Не существует лида';

            }
            
        } else {

            // Сессия не существует
            return 'Сессия не существует';

        }

    }
	
	public function delivery_update(Request $request)
	{
		$data = Carbon::createFromFormat('d.m.Y H:i', $request->delivery_date . ' ' . $request->delivery_time);
//		dd($data);
		
		$res = Lead::where('id', $request->lead_id)
			->update([
				'delivered_at' => $data
			]);
//		dd($res);
		
		if ($res) {
			return response()->json(true);
		}
		
		
	}
    
}
