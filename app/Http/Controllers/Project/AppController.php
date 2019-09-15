<?php

namespace App\Http\Controllers\Project;

use Telegram;
use App\CatalogsGoodsItem;
use App\Estimate;
use App\EstimatesItem;
use App\Lead;
use App\PricesGoods;
use App\Site;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;

use Illuminate\Support\Facades\Auth;

// Подрубаем трейт записи и обновления
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Http\Controllers\Traits\LeadControllerTrait;

class AppController extends Controller
{

    // Настройки контроллера
    public function __construct(Request $request)
    {
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

    public function start(Request $request)
    {
        if (is_null($this->site)) {
            return view('project.pages.mains.main');
        } else {

            return redirect('catalogs-goods/tovary-dlya-sayta/tekstil');
            $site = $this->site;
            $page = $site->pages_public
                ->where('alias', 'main')
                ->first();

            return view($site->alias.'.pages.mains.index', compact('site','page'));
        }
    }


    // Метод динамического формирования страницы
    public function dynamic_pages(Request $request, $page_alias)
    {

        if (is_null($this->site)) {

            return view('project.pages.mains.main');

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


    public function catalogs_goods(Request $request, $catalog_slug, $catalog_item_slug)
    {

//        dd($request);

        $site = $this->site;

        $page = $site->pages_public->where('alias', 'catalogs-goods')->first();

        $catalog_goods_item = CatalogsGoodsItem::whereHas('catalog_public', function ($q) use ($site, $catalog_slug) {
            $q->whereHas('sites', function ($q) use ($site) {
                $q->where('id', $site->id);
            })
                ->where('slug', $catalog_slug);
        })
            ->where([
                'slug' => $catalog_item_slug,
                'display' => true

            ])
            ->first();
//        dd($catalog_goods_item);

        $page->title = $catalog_goods_item->title;

        $prices_goods = PricesGoods::with([
            'goods_public'
        ])
            ->whereHas('catalogs_item_public', function ($q) use ($site, $catalog_slug, $catalog_item_slug) {
                $q->whereHas('catalog_public', function ($q) use ($site, $catalog_slug) {
                    $q->whereHas('sites', function ($q) use ($site) {
                        $q->where('id', $site->id);
                    })
                        ->where('slug', $catalog_slug);
                })
                    ->where('slug', $catalog_item_slug);

            })
            ->has('goods_public')
            ->where([
                'display' => true,
                'archive' => false
            ])
            ->filter($request)
            ->paginate(16);

        return view($site->alias.'.pages.catalogs_goods.index', compact('site','page', 'request', 'catalog_goods_item', 'prices_goods'));
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

                // dd('Привет, ' . $user->name);
                Auth::loginUsingId($user->id);
                return redirect()->route('project.start');

            } else {

                dd('Досуп закрыт');
            }

        } else {

            dd('Мы не в курсе кто вы такие!');
        }


    }

    public function logout_siteuser(Request $request)
    {
        Auth::logout();
        return redirect()->route('project.start');
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

        if ($request->personal_data) {

            $lead = createLeadFromSite($request);

            // Формируем сообщение
            $message = "Заказ с сайта:\r\n";
            $message .= "Имя клиента: " . $lead->name . "\r\n";
            $message .= "Тел: " . decorPhone($phone) . "\r\n";
            $message .= "Кол-во товаров: " . $count . "\r\n";
            $message .= "Сумма заказа: " . num_format($lead->badget, 0) . ' руб.';

            $lead->notes()->create([
                'company_id' => 1,
                'body' => $message,
                'author_id' => 1,
            ]);

            // Находим или создаем заказ для лида
            $estimate = Estimate::firstOrCreate([
                'lead_id' => $lead->id,
                'company_id' => $company_id
            ], [
                'author_id' => 1,
                'number' => $lead->case_number,
                'date' => $lead->created_at,
            ]);
            // dd($estimate);

            $prices_goods_ids = array_keys($cart['prices']);
            $prices_goods = PricesGoods::with('goods')
                ->find($prices_goods_ids);

            $data = [];
            foreach ($prices_goods as $price_goods) {
                $data[] = new EstimatesItem([
                    'product_id' => $price_goods->goods->id,
                    'product_type' => 'App\Goods',

                    'price_product_id' => $price_goods->id,
                    'price_product_type' => 'App\PricesGoods',

                    'company_id' => $company_id,
                    'author_id' => 1,

                    'price' => $price_goods->price,
                    'count' => $cart['prices'][$price_goods->id]['count'],

                    'sum' => $cart['prices'][$price_goods->id]['count'] * $price_goods->price
                ]);
            }
    //        dd($data);

            $estimate->items()->saveMany($data);

            $destinations = User::whereHas('staff', function ($query) {
                $query->whereHas('position', function ($query) {
                    $query->whereHas('notifications', function ($query) {
                        $query->where('notification_id', 1);
                    });
                });
            })
                ->whereNotNull('telegram')
                ->get(['telegram']);

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

            Cookie::queue(Cookie::forget('cart'));
            // $cookie = Cookie::forget('cart');

            return redirect()->route('project.start');
        }
    }
}
