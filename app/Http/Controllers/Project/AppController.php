<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Project\Traits\Commonable;
use Carbon\Carbon;
use App\Lead;
use App\PricesGoods;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

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
                return view($site->alias.'.pages.' . $page_alias . '.index', compact('site', 'page'));

            } else {

                // Не нашли. Но нет повода для печали, отправляем на общий шаблон
                return view($site->alias.'.pages.common.index', compact('site', 'page'));
            }
        }
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

    // Личный кабинет пользователя
    public function cabinet(Request $request)
    {

        $user = $request->user();
        $estimates = null;

        $site = $this->site;
        $page = $site->pages_public->firstWhere('alias', 'cabinet');

        return view($site->alias.'.pages.cabinet.index', compact('site', 'page', 'estimates', 'user'));
    }

    // Авторизация пользоваеля сайта через телефон и код СМС
    public function site_user_login(Request $request)
    {

        $site = $this->site;
        $access_code = $request->access_code;
        $main_phone = $request->main_phone;

        // Пришли необходимые данные для авторизации?
        if((isset($main_phone))&&(isset($access_code))){

            // Делаем запрос к базе данных
            $user = check_user_by_phones($main_phone, $site->company->id);

            // Зарегистрирован ли кто-нибудь по такому номеру?    
            if($user != null){

                // Есть ли аккаунт на текущем сайте?
                if($user->site_id == $site->id){

                    if($user->access_block == false){

                        if($user->access_code == $access_code){

                            if($user->user_type == false){

                                // Если проверка пройдена - АВТОРИЗУЕМ!
                                Auth::loginUsingId($user->id);
                                return redirect('/cabinet');

                            } else {
                                abort(403, 'Вы были близки к цели, но по каким то страннам обстоятельствам, мы все еще делим людей на своих и чужих. Так вот: вы чужой! Сорри.');
                            }

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
        Log::info('Пользователь запросил код доступа');
        $phone = cleanPhone($request->phone);

        $site = $this->site;
        $company = $site->company;

        // Смотрим, есть ли пользователь с таким номером телефона в базе
        $user = check_user_by_phones($phone, $company->id);
        Log::info('Чекнули юзера в базе по номеру телефона:');

        // Если пользователь не найден - то создаем
        if($user == null){
            $user = $this->createUserByPhone($phone, null, $company);
            Log::info('Не нашли, и создали нового с ID: ' . $user->id);
        }

        // Генерируем код доступа и записываем для пользователя
        $access_code = rand(1000, 9999);
        $user->access_code = $access_code;
        $user->save();
        Log::info('Сгенерироваи код и вписали юзеру');

        if(session('time_get_access_code')){

            $second_blocking = 180 - session('time_get_access_code')->diffInSeconds(now());

            if($second_blocking < 1){

                // Пишем в сессию время отправки СМС
                session(['time_get_access_code' => now()]);
                $msg = 'Код для входа: ' . $access_code;
                sendSms($company, $phone, $msg);
            };

        } else {

                // Пишем в сессию время отправки СМС
                session(['time_get_access_code' => now()]);
                $msg = 'Код для входа: ' . $access_code;

                Log::info('Просим функцию отправки СМС сообщения отправить этот код');
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
        $user = $request->user();

        $user->first_name = $request->first_name;
        $user->second_name = $request->second_name;
        $user->email = $request->email;
        $user->save();

    }



    public function confirmation(Request $request)
    {

        $site = $this->site;
        $company = $site->company;
        $page = $site->pages_public->firstWhere('alias', 'confirmation');

        return view($site->alias.'.pages.confirmation.index', compact('site', 'page', 'company'));
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
		
		$res = Lead::where('id', $request->lead_id)
			->update([
				'delivered_at' => $data
			]);
		
		if ($res) {
			return response()->json(true);
		}
		
	}
    
}
