<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Project\Traits\Commonable;
use App\Http\Controllers\Project\Traits\Userable;

use App\Http\Requests\Project\UserUpdateRequest;
use App\Models\Project\Subscriber;
use Carbon\Carbon;
use App\Lead;
use App\PricesGoods;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AppController extends Controller
{

    use Commonable;
    use Userable;

    public function start(Request $request)
    {
        if (is_null($this->site)) {
            return view('project.pages.mains.main');
        } else {

            $site = $this->site;

            if (isset($site->domain->start_url)) {
                return redirect($site->domain->start_url . $request->getRequestUri());
            } else {

                if (config('app.multidomains') == true) {
                    if (Cookie::get('domain') !== null) {
                        $path = 'https://' . Cookie::get('domain');
                        $params = $request->input();
                        if (count($params)) {
                            $path .= '?' . http_build_query($params);
                        }
                        return \Redirect::away($path);
                    }
                }

                $page = $site->pages_public
                    ->where('alias', 'main')
                    ->first();

                return view($site->alias . '.pages.main.index', compact('site', 'page'));
            }
        }
    }

//    public function filials(Request $request)
//    {
//        if (Cookie::get('domain') !== null) {
//            return \Redirect::away('https://'.Cookie::get('domain'));
//        } else {
//            $site = $this->site;
//        return view($site->alias.'.pages.start.index', compact('site'));
//        }
//    }

    public function changeFilial(Request $request, $domain)
    {
        // dd(__METHOD__);
        Cookie::queue(Cookie::forever('domain', $domain));
        $path = 'https://' . $domain;
        $params = $request->input();
        if (count($params)) {
            $path .= '?' . http_build_query($params);
        }
        return \Redirect::away($path);
    }

    /**
     * Перенаправление с одного города (поддомена) на другой, и записсь нового в куку
     *
     * @param Request $request
     * @param string $alias алиас города
     * @return \Illuminate\Http\RedirectResponse
     */
    public function change_city(Request $request, $alias)
    {
        Cookie::queue(Cookie::forever('city', $alias));
        $url = "https://{$alias}.{$this->site->domain}";
        return \Redirect::away($url);

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
            if (!isset($page)) {

                abort(404, "Страница не найдена");
            }

            // Формируем путь до view которая предположительно должна существовать
            $path_view = $site->alias . '/pages/' . $page_alias . '/index';

            // Проверяем существование файла view по сформирванному пути
            if (view()->exists($path_view)) {

                // Нашли - отправляем пользователя туда
                return view($site->alias . '.pages.' . $page_alias . '.index', compact('site', 'page'));

            } else {

                // Не нашли. Но нет повода для печали, отправляем на общий шаблон
                return view($site->alias . '.pages.common.index', compact('site', 'page'));
            }
        }
    }

    public function catalogs_services(Request $request, $catalog_slug, $catalog_item_slug)
    {
        $site = $this->site;

        // Вытаскивает через сайт каталог и его пункт с прайсами (не архивными), товаром и артикулом
        $site->load(['catalogs_services' => function ($q) use ($catalog_slug, $catalog_item_slug) {
            $q->with([
                'items' => function ($q) use ($catalog_item_slug) {
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

        return view($site->alias . '.pages.prices_goods.goods_composition', compact('site', 'price_goods'));
    }

    // Личный кабинет пользователя
    public function cabinet(Request $request)
    {

        $user = $request->user();
        $user->load([
            'subscriber'
        ]);

        $site = $this->site;
        $page = $site->pages_public->firstWhere('alias', 'cabinet');

        $site->load('notifications');

        // $estimates = Estimate::with('goods_items')
        //     ->whereHas('lead', function ($q) use ($user) {
        //         $q->where('user_id', $user->id);
        //     })
        //     ->get()->sortByDesc('id');
        $estimates = null;

        return view($site->alias . '.pages.cabinet.index', compact('site', 'page', 'estimates', 'user'));
    }

    // Авторизация пользоваеля сайта через телефон и код СМС
    public function site_user_login(Request $request)
    {

        Log::info('Запущена функция авторизации пользователя. Проведем ряд проверок!');

        $site = $this->site;
        $access_code = $request->access_code;
        $main_phone = $request->main_phone;

        // Пришли необходимые данные для авторизации?
        if ((isset($main_phone)) && (isset($access_code))) {

            // Делаем запрос к базе данных
            $user = checkPhoneUserForSite($main_phone, $site);

            // Зарегистрирован ли кто-нибудь по такому номеру?
            if ($user != null) {

                // Есть ли аккаунт на текущем сайте?
                if ($user->site_id == $site->id) {

                    if ($user->access_block == false) {

                        if ($user->access_code == $access_code) {

                            // Если проверка пройдена - АВТОРИЗУЕМ!
                            Auth::loginUsingId($user->id);
                            Log::info('Пользователь залогинился ==========================================================');
                            return redirect('estimates');

                        } else {
                            abort(403, 'Код устарел или введен с ошибками');
                        }

                    } else {
                        abort(403, 'По неведомым причинам - вам доступ ограничен');
                    }

                } else {
                    abort(403, 'Кажется, вы стучитесь не в ту дверь! )');
                }

            } else {
                abort(403, 'Вы у нас не прописаны )');
            }

        } else {
            abort(403, 'Это что? Попытка взлома!?');
        }
    }


    // Запрос на получение СМС кода на указанный телефон
    public function get_sms_code(Request $request)
    {

        $phone = cleanPhone($request->phone);
        Log::info('Пришел запрос на код с номера: ' . $phone);

        $site = $this->site;
        $company = $site->company;

        // Смотрим, есть ли пользователь с таким номером телефона в базе
        $user = checkPhoneUserForSite($phone, $site);
        Log::info('Чекнули юзера в базе по номеру телефона:');

        // Если пользователь не найден - то создаем
        if ($user == null) {

            // Делаем дополнительный запрос к базе данных пользователей компании
            Log::info('Делаем дополнительный запрос на поиск юзера в рамках компании');
            $user = checkPhoneUserForCompany($phone, $site->company);

            if ($user != null) {

                if ($user->site_id == null) {

                    Log::info('Нашли пользователя без привязки к сайту: ' . $user->id . ' . Сделали привязку к текущему');
                    $user->site_id = $site->id;
                    $user->save();

                } else {

                    $user = $this->createUserByPhoneFromSite($phone, $site);
                    Log::info('Нашли пользователя в базе компании. Но для текущего сайта создаем отдельный аккаунт: ' . $user->id);

                }

            } else {

                $user = $this->createUserByPhoneFromSite($phone, $site);
                Log::info('Не нашли ни в каких базах. Создали полностью новый аккаунт: ' . $user->id);
            }

        }

        // Генерируем код доступа и записываем для пользователя
        $access_code = rand(1000, 9999);
        $user->access_code = $access_code;

        $user->location_id = create_location($request, 1, $site->filial->location->city_id);

        $user->save();

        Log::info('Сгенерировали код и вписали юзеру');

        if (session('time_get_access_code')) {

            $second_blocking = 180 - session('time_get_access_code')->diffInSeconds(now());

            if ($second_blocking < 1) {

                // Пишем в сессию время отправки СМС
                session(['time_get_access_code' => now()]);
                $msg = 'Код для входа: ' . $access_code;

                Log::info('Просим функцию отправки СМС сообщения отправить этот код' . $access_code);
                sendSms($company, $phone, $msg);

            } else {

                Log::info('Время еше не истекло. Оставшееся время, сек:' . $second_blocking);
                Log::info('Код не был отправлен' . $access_code);
            }

        } else {

            // Пишем в сессию время отправки СМС
            session(['time_get_access_code' => now()]);
            $msg = 'Код для входа: ' . $access_code;

            Log::info('Просим функцию отправки СМС сообщения отправить этот код: ' . $access_code);
            sendSms($company, $phone, $msg);
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
        $user = auth()->user();

        $user->first_name = $request->first_name;
        $user->second_name = $request->second_name;
        $user->email = $request->email;
        $user->birthday_date = $request->birthday_date;
        $user->saveQuietly();

        $user->notifications()->sync($request->notifications);

        // Проверка подписчика
        $user->load([
            'subscriber',
        ]);

        $allow = $request->allow == 1 ? true : false;

        if (isset($request->email)) {

            if (isset($user->subscriber)) {
                if (isset($user->subscriber->archived_at)) {
                    $user->subscriber()->unarchive();
                }
                $user->subscriber()->update([
                    'email' => $request->email,
                ]);
            } else {
                $subscriber = \App\Subscriber::firstOrCreate([
                    'email' => $user->email,
                    'site_id' => $user->site_id
                ]);

                $subscriber->update([
                    'subscriberable_id' => $user->id,
                    'subscriberable_type' => 'App\User',
                    'name' => $user->name,
                    'denied_at' => $allow == true ? null : now(),
                    'is_self' => 1,
                ]);
            }
        } else {
            if (isset($user->subscriber)) {
                $user->subscriber()->archive();
            }
        }

        return redirect()->route('project.user.edit');

    }

    /**
     * Страница подтверждения заказа
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function confirmation(Request $request)
    {
        $site = $this->site;
        $company = $site->company;
        $page = $site->pages_public->firstWhere('alias', 'confirmation');

        return view($site->alias . '.pages.confirmation.index', compact('site', 'page', 'company'));
    }

    public function success(Request $request)
    {

        $site = $this->site;
        $page = $site->pages_public->firstWhere('alias', 'success');

        return view($site->alias . '.pages.success.index', compact('site', 'page'));
    }

    public function get_access_code(Request $request)
    {

        $site = $this->site;

        // $company = $site->company;
        // return $company->accounts->where('alias', 'smssend')->first()->api_token;

        $confirmation = session('confirmation');

        // Если сессия найдена (Сессия может закончится по времени)
        if ($confirmation) {

            $lead = $confirmation['lead'];

            // Если найден лид
            if ($lead) {

                if (!isset($lead->user)) {
                    return 'Пользователь не существует';
                }
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

    public function shipment_update(Request $request)
    {
        $data = Carbon::createFromFormat('d.m.Y H:i', $request->shipment_date . ' ' . $request->shipment_time);
        $res = Lead::where('id', $request->lead_id)
            ->update([
                'shipment_at' => $data
            ]);

        if ($res) {
            return response()->json(true);
        }
    }

    /**
     * Отписка от рассылок
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function unsubscribe(Request $request, $id)
    {
        $site = $this->site;
        $page = $site->pages_public->firstWhere('alias', 'unsubscribe');

        $subscriber = Subscriber::find($id);

        if ($subscriber) {
            if ($subscriber->token == $request->token) {
                $subscriber->update([
                    'denied_at' => now(),
                    'editor_id' => 1
                ]);
            }
        }

        return view($site->alias . '.pages.unsubscribe.index', compact('site', 'page'));
    }

}
