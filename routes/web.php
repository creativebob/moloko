<?php

use App\Lead;
use App\User;
use App\Claim;

use App\Entity;
use App\Page;
use App\Location;

use App\RawsArticle;

use Carbon\Carbon;

// use GuzzleHttp\Client;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

// Вход в панель управления
Route::get('/', function () {
    return view('layouts.enter');
});

// Всякая хрень для проверки
// Route::resource('/site_api', 'ApiController');
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/img/{path}', 'ImageController@show')->where('path', '.*');

Route::get('/home', 'HomeController@index')->name('home');

Route::any('getaccess', 'GetAccessController@set')->middleware('auth')->name('getaccess.set');

// -------------------------------------- Директории ---------------------------------------------------
Route::get('directories', 'DirectoryController@index')->middleware('auth')->name('directories.index');

// ---------------------------- Методы для парсера и одноразовые ----------------------------------------
Route::any('/check_class', 'ClassController@check_class');

Route::any('/lol', 'GoodsController@check_coincidence_name');


// Route::get('/lol', function () {

//     $leads = Lead::withCount(['choices_goods_categories', 'choices_services_categories', 'choices_raws_categories'])->get();
//     $count_goods = $leads->where('choices_goods_categories_count', '>', 1)->count();
//     dd($count_goods);

//     $count_services = $leads->where('choices_services_categories_count', '>', 1)->count();
//     dd($count_services);

//     $count_raws = $leads->where('choices_raws_categories_count', '>', 1)->count();
//     dd($count_raws);
// });
// Route::get('/columns', function () {
//     $columns = Schema::getColumnListing('leads');
//     // dd($columns);
//     $text = "<select>";
//     foreach ($columns as $column) {
//      $text .= "<option>" . $column . "</option>";
//     }
//     $text .= "</select>";
//    echo $text;
// });

// Route::any('/lol', function () {

//     $leads = Lead::with('location.city')
//     ->whereHas('location', function ($q) {
//         $q->whereNotNull('address')->whereNull('longitude')->whereNull('latitude');
//     })
//     // ->inRandomOrder()
//     // ->first();
//     ->get();
//     // dd($lead);

//     $count = 0;

//     foreach ($leads as $lead) {

//         // $client = new Client('https://geocode-maps.yandex.ru/1.x/?');
//         // $request = $client->createRequest();
//         // $request->getQuery()
//         // ->set('geocode', $lead->location->city->name . ', ' .$lead->location->address)
//         // ->set('format', 'json');

//         $request_params = [
//             'geocode' => $lead->location->city->name . ', ' .$lead->location->address,
//             'format' => 'json',

//         ];
//         $params = http_build_query($request_params);

//     // dd($get_params);

//         $result = (file_get_contents('https://geocode-maps.yandex.ru/1.x/?' . $params));
//     // dd($get_params);

//          /** @var $response Response */
//  $result = $request->send();

//         $res = json_decode($result);
//         if (count($res->response->GeoObjectCollection->featureMember) == 1) {

//             // echo $request_params['geocode']. "\r\n";
//             $string = $res->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
//             $position = explode(' ', $string);

//             $location = Location::whereId($lead->location_id)->update(['longitude' => $position[0], 'latitude' => $position[1]]);
//             // dd($lead->id);
//         // $date = $

//             $count++;
//         }
//     }

//     dd('Гатова - ' . $count);


//     // $result = (file_get_contents('http://search.maps.sputnik.ru/search?'.$get_params));

//     // echo $request_params['q']. "\r\n";

//     // $res = json_decode($result);
//     // dd($res->result);
// });

Route::get('/entity_page', 'ParserController@entity_page')->middleware('auth');
// Route::get('/geoposition_locations', 'ParserController@geoposition_locations')->middleware('auth');
// Route::get('/geoposition_locations_parse', 'ParserController@geoposition_locations_parse')->middleware('auth');
// Route::get('/city', 'ParserController@city')->middleware('auth');
// Route::get('/dublicator', 'ParserController@dublicator')->middleware('auth');
// Route::get('/dublicator_old', 'ParserController@dublicator_old')->middleware('auth');
// Route::get('/adder', 'ParserController@adder')->middleware('auth');
// Route::get('/parser', 'ParserController@index')->middleware('auth');
// Route::get('/lead_type', 'ParserController@lead_type')->middleware('auth');
// Route::get('/old_claims', 'ParserController@old_claims')->middleware('auth');
// Route::get('/phone_parser', 'ParserController@phone_parser')->middleware('auth');
// Route::get('/cac_parser', 'ParserController@challenges_active_count')->middleware('auth');
Route::get('/choice_parser', 'ParserController@choice_parser')->middleware('auth');

Route::get('/map', function() {

    // $lead = Lead::with('location')
    // ->whereHas('location', function ($q) {
    //     $q->whereNotNull('latitude');
    // })
    // // ->inRandomOrder()
    // // ->first();

    $leads = Lead::with(['location.city', 'main_phones', 'stage', 'claims' => function ($q) {
        $q->whereStatus(1);
    }])
    ->whereHas('location', function ($q) {
        $q->whereNotNull('longitude')->whereNotNull('latitude');
    })
    ->where('stage_id', '!=', 13)
    ->where('lead_type_id', '!=', 3)
    ->get();
    // dd($leads);

    $lead = $leads->first();
    // dd($lead);

    $mass = [];
    foreach ($leads as $lead) {

        $claims_count = count($lead->claims) > 0 ? count($lead->claims) : 0;

        $mass[] = [
            'coords' => [(float)$lead->location->latitude, (float)$lead->location->longitude],
            'info' => [
                'name' => "<a href=" . asset('admin/leads/' . $lead->id .'/edit') . " target=_blank>" . $lead->name . "</a>",
                'order' => $lead->case_number,
                'phone' => decorPhone($lead->main_phone->phone),
                'address' => 'г. ' . $lead->location->city->name . ', ' . $lead->location->address,
                'stage' => [
                    'id' => $lead->stage_id,
                    'name' => $lead->stage->name,
                ],
                'claims_count' => $claims_count,
            ]
        ];
    };

    // $coords = json_encode($mass, JSON_UNESCAPED_UNICODE);

    // dd($mass);
    // $coords = $mass;

    $coords = json_encode($mass, JSON_UNESCAPED_UNICODE); 

    // dd($coords);

    // dd($lead);

    return view('leads.map', compact('lead', 'coords'));
})->middleware('auth');

Route::get('/route', function() {

    $lead = Lead::with(['location'])
    ->whereHas('location', function ($q) {
        $q->whereNotNull('longitude')->whereNotNull('latitude');
    })
    ->where('stage_id', '!=', 13)
    ->where('lead_type_id', '!=', 3)
    ->inRandomOrder()
    ->first();


    $mass = [
        'coords' => [(float)$lead->location->latitude, (float)$lead->location->longitude],
    ];

    $coords = json_encode($mass, JSON_UNESCAPED_UNICODE); 

    return view('leads.route', compact('coords'));
})->middleware('auth');

Route::get('/mounth', function() {


    $start = new Carbon('first day of last month');
    $start->startOfMonth();
    $end = new Carbon('last day of last month');
    $end->endOfMonth();

    // dd($end);

    $leads = Lead::where('created_at', '>=', $start)->where('created_at', '<=', $end)->whereNull('draft')->get();
    // dd($leads);
    $telegram_message = "Отчет за сентябрь: \r\n\r\nЗвонков: ".count($leads->where('lead_type_id', 1))."\r\Заявок с сайта: ".count($leads->where('lead_type_id', 2))."\r\n\r\nВсего: ".count($leads);

    dd($telegram_message);
})->middleware('auth');

// ------------------------------------ Telegram ----------------------------------------

// Получаем бота
Route::get('/get_bot', 'TelegramController@get_bot')->middleware('auth');

// Устанавливаем webhook
Route::get('/set_webhook', 'TelegramController@set_webhook')->middleware('auth');

// Удаляем webhook
Route::get('/remove_webhook', 'TelegramController@remove_webhook')->middleware('auth');

// Ручное получение сообщений, для тестов
Route::get('/telegram_updates', 'TelegramController@get_updates');

// Получаем сообщение от бота
Route::post('/telegram_message', 'TelegramController@get_message');
// Route::post('/'.env('TELEGRAM_BOT_TOKEN'), 'TelegramController@get_message');


Route::get('/vk', 'VkController@market')->middleware('auth');

// // Ответ для VK
// Route::post('/vk_response', function() {
//     $resp = '569cecce'
//     echo $resp;
// });


// -------------------------------------- Основные операции ------------------------------------------
// Сортировка
Route::post('/sort/{entity_alias}', 'AppController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/system_item', 'AppController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/display', 'AppController@ajax_display')->middleware('auth');


// --------------------------------------- Настройки -----------------------------------------------

Route::any('/set_setting', 'SettingController@ajax_set_setting')->middleware('auth');

Route::resource('/settings', 'SettingController')->middleware('auth');


// ---------------------------------------- Телефоны --------------------------------------------------

Route::post('/add_extra_phone', 'PhoneController@ajax_add_extra_phone')->middleware('auth');


// -------------------------------------- Пользователи ------------------------------------------------

Route::resource('/users', 'UserController')->middleware('auth');
Route::get('/myprofile', 'UserController@myprofile')->middleware('auth')->name('users.myprofile');
Route::patch('/updatemyprofile', 'UserController@updatemyprofile')->middleware('auth')->name('users.updatemyprofile');

// Поиск продукции для добавления на сайт
Route::any('/catalog_products/add_product', 'CatalogProductController@add_product')->middleware('auth');

// ---------------------------------- Категории альбомов -------------------------------------------

// Текущая добавленная/удаленная категория альбомов
Route::any('/albums_categories', 'AlbumsCategoryController@index')->middleware('auth');
// Основные методы
Route::resource('/albums_categories', 'AlbumsCategoryController')->middleware('auth');
// Проверка на существование категории альбомов
Route::post('/albums_category_check', 'AlbumsCategoryController@ajax_check')->middleware('auth');
// Select категорий альбомов
Route::post('/albums_categories_list', 'AlbumsCategoryController@albums_categories_list')->middleware('auth');


// --------------------------------------- Альбомы -----------------------------------------------

// Route::resource('/albums', 'AlbumController')->middleware('auth');
Route::get('/albums', 'AlbumController@index')->middleware('auth')->name('albums.index');
Route::get('/albums/create', 'AlbumController@create')->middleware('auth')->name('albums.create');
Route::get('/albums/{alias}', 'AlbumController@show')->middleware('auth')->name('albums.show');
Route::post('/albums', 'AlbumController@store')->middleware('auth')->name('albums.store');
Route::get('/albums/{alias}/edit', 'AlbumController@edit')->middleware('auth')->name('albums.edit');
Route::patch('/albums/{id}', 'AlbumController@update')->middleware('auth')->name('albums.update');
Route::delete('/albums/{id}', 'AlbumController@destroy')->middleware('auth')->name('albums.destroy');

// Получение альбомов по категории
Route::post('/albums_list', 'AlbumController@albums_list')->middleware('auth');
// Получение альбома
Route::post('/get_album', 'AlbumController@get_album')->middleware('auth');
// Проверка на существование
Route::post('/albums_check', 'AlbumController@ajax_check')->middleware('auth');


// Группа с префиксом
Route::prefix('/albums/{alias}')->group(function () {

  // ----------------------------------- Фотографии -----------------------------------------------

    Route::resource('/photos', 'PhotoController')->middleware('auth');
  // Загрузка фоток через ajax через dropzone.js
});

Route::post('/ajax_get_photo', 'PhotoController@get_photo')->middleware('auth');
Route::patch('/ajax_update_photo/{id}', 'PhotoController@update_photo')->middleware('auth');


// --------------------------------------- Помещения -----------------------------------------------
Route::resource('/places', 'PlaceController')->middleware('auth');

// --------------------------------------- Склады -----------------------------------------------
Route::resource('stocks', 'StockController')->middleware('auth');

// --------------------------------------- Свойства -----------------------------------------------
Route::post('/ajax_add_property', 'PropertyController@add_property')->middleware('auth');


// ---------------------------------------- Метрики -------------------------------------------------
// Основные методы
Route::resource('/metrics', 'MetricController')->middleware('auth');

// Пишем метрику через ajax
Route::post('/ajax_store_metric', 'MetricController@ajax_store')->middleware('auth');
// Добавляем / удаляем связь сущности с метрикой
Route::match(['get', 'post'], '/ajax_add_relation_metric', 'MetricController@ajax_add_relation')->middleware('auth');
Route::post('/ajax_delete_relation_metric', 'MetricController@ajax_delete_relation')->middleware('auth');

Route::post('/ajax_add_metric_value', 'MetricController@add_metric_value')->middleware('auth');




// ---------------------------------------- Состав -------------------------------------------------

Route::post('/ajax_add_relation_composition', 'CompositionController@ajax_add_relation')->middleware('auth');
Route::post('/ajax_delete_relation_composition', 'CompositionController@ajax_delete_relation')->middleware('auth');

Route::post('/ajax_add_page_composition', 'CompositionController@ajax_add')->middleware('auth');

Route::post('/get_units_list', 'UnitController@get_units_list')->middleware('auth');
Route::post('/ajax_get_article_inputs', 'ArticleController@get_inputs')->middleware('auth');


// ------------------------------------- Категории сырья -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/raws_categories', 'RawsCategoryController@index')->middleware('auth');
// Основные методы
Route::resource('/raws_categories', 'RawsCategoryController')->middleware('auth');
// Проверка на существование категории продукции
Route::post('/raws_category_check', 'RawsCategoryController@ajax_check')->middleware('auth');


// --------------------------------- Продукция сырья --------------------------------------------

// Основные методы
Route::resource('/raws_products', 'RawsProductController')->middleware('auth');

Route::any('/ajax_raws_count', 'RawsProductController@ajax_count')->middleware('auth');
Route::any('/ajax_raws_modes', 'RawsProductController@ajax_modes')->middleware('auth');


// ---------------------------------- Сырьё (Артикулы) -------------------------------------------

// Основные методы
Route::resource('/raws', 'RawController')->middleware('auth');
// Route::get('/raws/search/{text_fragment}', 'RawController@search')->middleware('auth');
Route::post('/raws/search/{text_fragment}', 'RawController@search')->middleware('auth');
// Архивация
Route::post('/raws/archive/{id}', 'RawController@archive')->middleware('auth');
// Фото
Route::any('/raw/add_photo', 'RawController@add_photo')->middleware('auth');
Route::post('/raw/photos', 'RawController@photos')->middleware('auth');


// -------------------------------- Категории товаров -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/goods_categories', 'GoodsCategoryController@index')->middleware('auth');
Route::match(['get', 'post'], '/goods_categories/{id}/edit', 'GoodsCategoryController@edit')->middleware('auth');
// Основные методы
Route::resource('/goods_categories', 'GoodsCategoryController')->middleware('auth');
// Проверка на существование
Route::post('/goods_category_check', 'GoodsCategoryController@ajax_check')->middleware('auth');

// Отображение на сайте
Route::any('/goods_categories_get_products', 'GoodsCategoryController@ajax_get_products')->middleware('auth');

Route::any('/goods_category_metrics', 'GoodsCategoryController@ajax_get_metrics')->middleware('auth');
Route::any('/goods_category_compositions', 'GoodsCategoryController@ajax_get_compositions')->middleware('auth');


// --------------------------------- Группы товаров --------------------------------------------

// Основные методы
Route::resource('/goods_products', 'GoodsProductController')->middleware('auth');

Route::any('/ajax_goods_count', 'GoodsProductController@ajax_count')->middleware('auth');
Route::any('/ajax_goods_modes', 'GoodsProductController@ajax_modes')->middleware('auth');

Route::any('/goods_products_list', 'GoodsProductController@ajax_get_products_list')->middleware('auth');


// ---------------------------------- Товары (Артикулы) -------------------------------------------
// Route::any('/goods/create', 'GoodsController@create')->middleware('auth');

// Основные методы
Route::resource('/goods', 'GoodsController')->middleware('auth');
Route::post('/goods/search/{text_fragment}', 'GoodsController@search')->middleware('auth');
// Архивация
Route::post('/goods/archive/{id}', 'GoodsController@archive')->middleware('auth');

// Отображение на сайте
Route::post('/goods_sync', 'GoodsController@ajax_sync')->middleware('auth');

// Фотки
Route::any('/goods/add_photo', 'GoodsController@add_photo')->middleware('auth');
Route::post('/goods/photos', 'GoodsController@photos')->middleware('auth');


// -------------------------------- Категории услуг -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/services_categories', 'ServicesCategoryController@index')->middleware('auth');
// Основные методы
Route::resource('/services_categories', 'ServicesCategoryController')->middleware('auth');
// Проверка на существование
Route::post('/services_category_check', 'ServicesCategoryController@ajax_check')->middleware('auth');


// --------------------------------- Продукция услуг --------------------------------------------

// Основные методы
Route::resource('/services_products', 'ServicesProductController')->middleware('auth');

Route::any('/ajax_services_count', 'ServicesProductController@ajax_count')->middleware('auth');
Route::any('/ajax_services_modes', 'ServicesProductController@ajax_modes')->middleware('auth');


// ---------------------------------- Услуги (Артикулы) -------------------------------------------

Route::any('/services/create', 'ServiceController@create')->middleware('auth');

// Основные методы
Route::resource('/services', 'ServiceController')->middleware('auth');
// Route::get('/services/search/{text_fragment}', 'ServiceController@search')->middleware('auth');
Route::post('/services/search/{text_fragment}', 'ServiceController@search')->middleware('auth');
// Архивация
Route::post('/services/archive/{id}', 'ServiceController@archive')->middleware('auth');
// Фотки
Route::any('/service/add_photo', 'ServiceController@add_photo')->middleware('auth');
Route::post('/service/photos', 'ServiceController@photos')->middleware('auth');


// ----------------------------------------- Секторы -----------------------------------------------
// Текущий добавленный/удаленный сектор
Route::any('/sectors', 'SectorController@index')->middleware('auth');
// Основные методы
Route::resource('/sectors', 'SectorController')->middleware('auth');
// Проверка на существование сектора
Route::post('/sector_check', 'SectorController@ajax_check')->middleware('auth');
// Select секторов
Route::post('/sectors_list', 'SectorController@sectors_list')->middleware('auth');


// --------------------------------------- Компании -----------------------------------------------

// Основные методы
Route::resource('/companies', 'CompanyController')->middleware('auth');
// Проверка существования компании в базе по ИНН
Route::post('/companies/check_company', 'CompanyController@checkcompany')->middleware('auth')->name('companies.checkcompany');


// --------------------------------------- Лиды -----------------------------------------------

// Основные методы
// Route::get('/lead/calls', 'LeadController@index')->middleware('auth');
Route::resource('/leads', 'LeadController')->middleware('auth');
// Route::resource('/leads_calls', 'LeadController@leads_calls')->middleware('auth');

// Поиск
Route::post('/leads/search', 'LeadController@search')->middleware('auth');

// Назначение лида
Route::any('/lead_appointed_check', 'LeadController@ajax_appointed_check')->middleware('auth');
Route::any('/lead_appointed', 'LeadController@ajax_lead_appointed')->middleware('auth');
Route::any('/lead_distribute', 'LeadController@ajax_distribute')->middleware('auth');
Route::any('/lead_take', 'LeadController@ajax_lead_take')->middleware('auth');

Route::post('/open_change_lead_type', 'LeadController@ajax_open_change_lead_type')->middleware('auth');
Route::post('/change_lead_type', 'LeadController@ajax_change_lead_type')->middleware('auth');

// Освобождаем лида
Route::post('/lead_free', 'LeadController@ajax_lead_free')->middleware('auth');

// Добавление комментария
Route::post('/leads_add_note', 'LeadController@ajax_add_note')->middleware('auth');
// Поиск лида по номеру телефона
Route::post('/leads/autofind/{phone}', 'LeadController@ajax_autofind_phone')->middleware('auth');


// --------------------------------------- Заказы -----------------------------------------------

// Основные методы
Route::resource('orders', 'OrderController')->middleware('auth');

// Отображение на сайте
Route::any('/orders_check', 'OrderController@ajax_check')->middleware('auth');

Route::delete('/order_compositions/{id}', 'OrderController@ajax_destroy_composition')->middleware('auth');


// ------------------------------ Внутренние комментарии -----------------------------------------------

// Основные методы
Route::resource('/notes', 'NoteController')->middleware('auth');


// --------------------------------------- Задачи -----------------------------------------------

// Route::any('/challenges/{id}', 'ChallengeController@destroy')->middleware('auth');
// Основные методы
Route::resource('/challenges', 'ChallengeController')->middleware('auth');
Route::post('/get_challenges_user', 'ChallengeController@ajax_get_challenges')->middleware('auth');

// -----------------------------------------Рекламации -----------------------------------------------

// Основные методы
Route::resource('/claims', 'ClaimController')->middleware('auth');

Route::post('/claim_add', 'ClaimController@ajax_store')->middleware('auth');
Route::post('/claim_finish', 'ClaimController@ajax_finish')->middleware('auth');

// --------------------------------------- Этапы --------------------------------------------

// Основные методы
Route::resource('/stages', 'StageController')->middleware('auth');


// --------------------------------------- Поля --------------------------------------------

// Список полей
Route::post('/fields_list', 'FieldController@ajax_fields_list')->middleware('auth');


// -------------------------------------- Правила -------------------------------------------

// Список полей
Route::resource('/rules', 'RuleController')->middleware('auth');

Route::post('/rule_add', 'RuleController@ajax_store')->middleware('auth');
Route::post('/rule_delete', 'RuleController@ajax_destroy')->middleware('auth');

// ---------------------------------------- Посты --------------------------------------------

// Основные методы
Route::resource('/posts', 'PostController')->middleware('auth');


// ---------------------------------------- Аккаунты --------------------------------------------

// Основные методы
Route::resource('/accounts', 'AccountController')->middleware('auth');

// Проверка на существование аккаунта
Route::post('/accounts_check', 'AccountController@ajax_check')->middleware('auth');


// --------------------------------------- Рекламные кампании -----------------------------------------------

// Основные методы
Route::resource('/campaigns', 'CampaignController')->middleware('auth');


// --------------------------------------- Отзывы -----------------------------------------------

// Основные методы
Route::resource('/feedback', 'FeedbackController')->middleware('auth');


// --------------------------------------- Расходы -----------------------------------------------

// Основные методы
Route::resource('/expenses', 'ExpenseController')->middleware('auth');


// --------------------------------------- Зарплаты -----------------------------------------------

// Основные методы
Route::resource('/salaries', 'SalaryController')->middleware('auth');


// --------------------------------------- Социальные сети -----------------------------------------------

// Основные методы
Route::resource('social_networks', 'SocialNetworkController')->middleware('auth');


// -------------------------------------- Поставщики -----------------------------------------------

// Основные методы
Route::resource('/suppliers', 'SupplierController')->middleware('auth');


// ------------------------------------ Производители ----------------------------------------------------

// Основные методы
Route::resource('/manufacturers', 'ManufacturerController')->middleware('auth');


// ------------------------------------ Дилеры ----- ----------------------------------------------------

// Основные методы
Route::resource('/dealers', 'DealerController')->middleware('auth');


// ------------------------------------ Клиенты ----------------------------------------------------------

// Основные методы
Route::resource('/clients', 'ClientController')->middleware('auth');


// ------------------------------------ Банки ----------------------------------------------------------

// Основные методы
Route::resource('banks', 'BankController')->middleware('auth');


// ------------------------------------- Правила доступа ----------------------------------------------------

// Основные методы
Route::resource('/rights', 'RightController')->middleware('auth');


//-------------------------------------- Группы доступа -----------------------------------------------------

// Основные методы
Route::resource('/roles', 'RoleController')->middleware('auth');
// Route::resource('rightrole', 'RightroleController')->middleware('auth');

Route::get('/roles/{id}/setting', 'RoleController@setting')->middleware('auth')->name('roles.setting');
Route::post('/roles/setright', 'RoleController@setright')->middleware('auth')->name('roles.setright');
// Получение роли дял пользоователя
Route::any('/get_role', 'RoleController@get_role')->middleware('auth');
// Маршрут связи юзера с ролями и отделами
Route::resource('/roleuser', 'RoleUserController')->middleware('auth');


// ----------------------------------------Cущности -----------------------------------------------------------

// Основные методы
Route::resource('/entities', 'EntityController')->middleware('auth');

// Маршруты для папок (директорий)
Route::resource('/folders', 'FolderController')->middleware('auth');

// Авторизуемся под выбранной компанией
Route::get('/getauthcompany/{company_id}', 'UserController@getauthcompany')->middleware('auth')->name('users.getauthcompany');

// Авторизуемся под выбранным пользователем
Route::get('/getauthuser/{user_id}', 'UserController@getauthuser')->middleware('auth')->name('users.getauthuser');

// Сбрасываем для бога company_id
Route::get('/getgod', 'UserController@getgod')->middleware('auth')->name('users.getgod');

// Получаем доступ бога
Route::get('/returngod', 'UserController@returngod')->middleware('auth')->name('users.returngod');


// ---------------------------------------- Области -------------------------------------------------

// Основные методы
Route::resource('/regions', 'RegionController')->middleware('auth');
// Получаем области из vk
Route::post('/region', 'RegionController@get_vk_region')->middleware('auth');


// ---------------------------------------- Районы --------------------------------------------------

// Основные методы
Route::resource('/areas', 'AreaController')->middleware('auth');


// ----------------------------------- Населенные пункты -------------------------------------------

// Текущий добавленный/удаленный город
Route::any('/cities', 'CityController@index')->middleware('auth');
// Основные методы
Route::resource('/cities', 'CityController')->middleware('auth');
// Проверка на существование города
Route::post('/city_check', 'CityController@ajax_check')->middleware('auth');
// Таблица городов
Route::post('/cities_list', 'CityController@cities_list')->middleware('auth');
// Получаем города из vk
Route::post('/city_vk', 'CityController@get_vk_city')->middleware('auth');

// Тестовый маршрут проверки пришедших с вк данных
Route::get('/city_vk/{city}', 'CityController@get_vk_city')->middleware('auth');


// ----------------------------------------- Филиалы и отделы --------------------------------------

// Текущий добавленный/удаленный отдел/филиал
Route::any('/departments', 'DepartmentController@index')->middleware('auth');
// Основные методы
Route::resource('/departments', 'DepartmentController')->middleware('auth');
// Текущий добавленный/удаленный отдел
Route::get('/current_department/{section_id}/{item_id}', 'DepartmentController@current_department')->middleware('auth');
// Список отделов филиала
Route::post('/departments_list', 'DepartmentController@departments_list')->middleware('auth');
// Проверка на существование филиала/отдела
Route::post('/department_check', 'DepartmentController@ajax_check')->middleware('auth');


// ----------------------------------------- Должности --------------------------------------------

// Основные методы
Route::resource('/positions', 'PositionController')->middleware('auth');
// Список отделов филиала и доступных должностей
Route::post('/positions_list', 'PositionController@positions_list')->middleware('auth');


// -------------------------------------- Штат компании ---------------------------------------------

// Основные методы
Route::resource('/staff', 'StafferController')->middleware('auth');


// --------------------------------------- Сотрудники ---------------------------------------------

// Основные методы
Route::resource('/employees', 'EmployeeController')->middleware('auth');


// ------------------------------------------ Списки -----------------------------------------------
Route::resource('/booklists', 'BooklistController')->middleware('auth');

Route::post('/setbooklist', 'BooklistController@setbooklist')->middleware('auth')->name('booklists.setbooklist');
Route::get('/updatebooklist', 'BooklistController@setbooklist')->middleware('auth')->name('booklists.updatebooklist');


// ----------------------------------------- Сайты ----------------------------------------------
Route::get('/sites', 'SiteController@index')->middleware('auth')->name('sites.index');
Route::get('/sites/create', 'SiteController@create')->middleware('auth')->name('sites.create');
Route::post('/sites', 'SiteController@store')->middleware('auth')->name('sites.store');
Route::get('/sites/{alias}/edit', 'SiteController@edit')->middleware('auth')->name('sites.edit');
Route::patch('/sites/{id}', 'SiteController@update')->middleware('auth')->name('sites.update');
Route::delete('/sites/{id}', 'SiteController@destroy')->middleware('auth')->name('sites.destroy');
Route::get('/sites/{alias}', 'SiteController@sections')->middleware('auth')->name('sites.sections');
// Проверка на существование домена сайта
Route::post('/site_check', 'SiteController@ajax_check')->middleware('auth');

// Разделы сайта
Route::prefix('/sites/{alias}')->group(function () {


    // --------------------------------------- Страницы ---------------------------------------------

    // Основные методы
    Route::resource('/pages', 'PageController')->middleware('auth');

    // Проверка на существование страницы
    Route::post('/page_check', 'PageController@ajax_check')->middleware('auth');


    // --------------------------------------- Навигации --------------------------------------------

    // Текущая добавленная/удаленная навигация
    Route::any('/navigations', 'NavigationController@index')->middleware('auth');
    // Основные методы
    Route::resource('/navigations', 'NavigationController')->middleware('auth');
    // Проверка на существование навигации
    Route::post('/navigation_check', 'NavigationController@ajax_check')->middleware('auth');


    // -------------------------------------------Меню ---------------------------------------------

    // Основные методы
    Route::resource('/menus', 'MenuController')->middleware('auth');


    // ---------------------------------------- Новости --------------------------------------------

    // Основные методы
    Route::resource('/news', 'NewsController')->middleware('auth');
    // Проверка на существование новости
    Route::post('/news_check', 'NewsController@ajax_check')->middleware('auth');

    // ----------------------------------------- Каталог ------------------------------------------

    // Текущий добавленный/удаленный каталог
    Route::any('/catalogs', 'CatalogController@index')->middleware('auth');
    // Основные методы
    Route::resource('/catalogs', 'CatalogController')->middleware('auth');
    // Проверка на существование каталога
    Route::post('/catalog_check', 'CatalogController@ajax_check')->middleware('auth');
    // Проверка на существование алиаса каталога
    Route::post('/catalog_check_alias', 'CatalogController@ajax_check_alias')->middleware('auth');


    // -------------------------------- Продукция для каталогов сайта -------------------------------------
    // Основные методы
    Route::get('/catalog_products/{id?}', 'CatalogProductController@show')->middleware('auth');
    // Основные методы
    Route::resource('/catalog_products', 'CatalogProductController')->middleware('auth');

});

// Поиск продукции для добавления на сайт
Route::any('/catalog_products/search_add_product/{text_fragment}/{catalog_id}', 'CatalogProductController@search_add_product')->middleware('auth');


// ------------------------------------- Отображение сессии -----------------------------------------
Route::get('/show_session', 'HelpController@show_session')->middleware('auth')->name('help.show_session');