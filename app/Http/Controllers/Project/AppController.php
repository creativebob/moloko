<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Project\Traits\Commonable;
use Carbon\Carbon;
use App\Lead;
use App\PricesGoods;

use Illuminate\Http\Request;
//use App\Http\Requests\Project\UserUpdateRequest;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

// Подрубаем трейт записи и обновления
use App\Http\Controllers\Traits\UserControllerTrait;

class AppController extends Controller
{

    use Commonable;
    use UserControllerTrait;

    public function start(Request $request)
    {
        if (is_null($this->site)) {
            return view('project.pages.mains.main');
        } else {

            return redirect('catalogs-goods/tovary-dlya-sayta/tekstil');
            $site = $this->site;
            $filial = $this->filial;
            $page = $site->pages_public
                ->where('alias', 'main')
                ->first();

            return view($site->alias.'.pages.main.index', compact('site', 'filial', 'page'));
        }
    }


    // Метод динамического формирования страницы
    public function dynamic_pages(Request $request, $page_alias)
    {

        if (is_null($this->site)) {

            return view('project.pages.main.main');

        } else {

            $site = $this->site;
            $filial = $this->filial;

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
                return view($site->alias.'.pages.' . $page_alias . '.index', compact('site', 'filial', 'page'));

            } else {

                // Не нашли. Но нет повода для печали, отправляем на общий шаблон
                return view($site->alias.'.pages.common.index', compact('site', 'filial', 'page'));
            }
        }
    }

    public function catalogs_services(Request $request, $catalog_slug, $catalog_item_slug)
    {
        $site = $this->site;
        $filial = $this->filial;

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


    public function goods_composition(Request $request, $id)
    {

        $site = $this->site;
$filial = $this->filial;
        $price_goods = PricesGoods::with([
            'goods_public'
        ])
            ->where([
                'id' => $id,
                'display' => true
            ])
            ->first();

        return view($site->alias.'.pages.prices_goods.goods_composition', compact('site', 'filial', 'price_goods'));
    }

    // Личный кабинет пользователя
    public function cabinet(Request $request)
    {

        $estimates = null;

        $site = $this->site;
        $filial = $this->filial;
        $page = $site->pages_public->firstWhere('alias', 'cabinet');

        $site->load('notifications');

        return view($site->alias.'.pages.cabinet.index', compact('site', 'filial', 'page', 'estimates', 'user'));
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
$filial = $this->filial;
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



    public function confirmation(Request $request)
    {

        $site = $this->site;
$filial = $this->filial;
        $company = $site->company;
        $page = $site->pages_public->firstWhere('alias', 'confirmation');

        return view($site->alias.'.pages.confirmation.index', compact('site', 'filial', 'page', 'company'));
    }

    public function get_access_code(Request $request)
    {

        $site = $this->site;
$filial = $this->filial;

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
