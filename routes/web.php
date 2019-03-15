<?php

use App\Lead;
use App\User;
use App\Claim;

use App\Entity;
use App\Page;
use App\Location;

use App\RawsArticle;

use Carbon\Carbon;

use Fomvasss\Dadata\Facades\DadataSuggest;

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
Route::get('/', 'AppController@enter');

// Всякая хрень для проверки
// Route::resource('/site_api', 'ApiController');

Route::get('/img/{path}', 'ImageController@show')->where('path', '.*');
Route::get('/home', 'HomeController@index')->name('home');
Route::any('getaccess', 'GetAccessController@set')->middleware('auth')->name('getaccess.set');


// ----------------------------- Рабочий стол -------------------------------------

Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

// -------------------------------------- Директории ---------------------------------------------------
Route::get('directories', 'DirectoryController@index')->middleware('auth')->name('directories.index');

// ---------------------------- Методы для парсера и одноразовые ----------------------------------------
Route::any('/check_class', 'ClassController@check_class');
Route::any('/lol', 'GoodsController@check_coincidence_name');
Route::get('/entity_page', 'ParserController@entity_page')->middleware('auth');

Route::get('/update_menus', 'ParserController@update_menus')->middleware('auth');

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

// Route::get('/dadata', function() {
//     $result = DadataSuggest::suggest("party", ["query"=>"солтысяк"]);
//     dd($result);
// })->middleware('auth');


// Route::get('/map', function() {

//     // $lead = Lead::with('location')
//     // ->whereHas('location', function ($q) {
//     //     $q->whereNotNull('latitude');
//     // })
//     // // ->inRandomOrder()
//     // // ->first();

//     $leads = Lead::with(['location.city', 'main_phones', 'stage', 'claims' => function ($q) {
//         $q->whereStatus(1);
//     }])
//     ->whereHas('location', function ($q) {
//         $q->whereNotNull('longitude')->whereNotNull('latitude');
//     })
//     ->where('stage_id', '!=', 13)
//     ->where('lead_type_id', '!=', 3)
//     ->get();
//     // dd($leads);

//     $lead = $leads->first();
//     // dd($lead);

//     $mass = [];
//     foreach ($leads as $lead) {

//         $claims_count = count($lead->claims) > 0 ? count($lead->claims) : 0;

//         $mass[] = [
//             'coords' => [(float)$lead->location->latitude, (float)$lead->location->longitude],
//             'info' => [
//                 'name' => "<a href=" . asset('admin/leads/' . $lead->id .'/edit') . " target=_blank>" . $lead->name . "</a>",
//                 'order' => $lead->case_number,
//                 'phone' => decorPhone($lead->main_phone->phone),
//                 'address' => 'г. ' . $lead->location->city->name . ', ' . $lead->location->address,
//                 'stage' => [
//                     'id' => $lead->stage_id,
//                     'name' => $lead->stage->name,
//                 ],
//                 'claims_count' => $claims_count,
//             ]
//         ];
//     };

//     // $coords = json_encode($mass, JSON_UNESCAPED_UNICODE);

//     // dd($mass);
//     // $coords = $mass;

//     $coords = json_encode($mass, JSON_UNESCAPED_UNICODE);

//     // dd($coords);

//     // dd($lead);

//     return view('leads.map', compact('lead', 'coords'));
// })->middleware('auth');

// Route::get('/route', function() {

//     $lead = Lead::with(['location'])
//     ->whereHas('location', function ($q) {
//         $q->whereNotNull('longitude')->whereNotNull('latitude');
//     })
//     ->where('stage_id', '!=', 13)
//     ->where('lead_type_id', '!=', 3)
//     ->inRandomOrder()
//     ->first();


//     $mass = [
//         'coords' => [(float)$lead->location->latitude, (float)$lead->location->longitude],
//     ];

//     $coords = json_encode($mass, JSON_UNESCAPED_UNICODE);

//     return view('leads.route', compact('coords'));
// })->middleware('auth');

// Route::get('/mounth', function() {


//     $start = new Carbon('first day of last month');
//     $start->startOfMonth();
//     $end = new Carbon('last day of last month');
//     $end->endOfMonth();

//     // dd($end);

//     $leads = Lead::where('created_at', '>=', $start)->where('created_at', '<=', $end)->whereNull('draft')->get();
//     // dd($leads);
//     $telegram_message = "Отчет за сентябрь: \r\n\r\nЗвонков: ".count($leads->where('lead_type_id', 1))."\r\Заявок с сайта: ".count($leads->where('lead_type_id', 2))."\r\n\r\nВсего: ".count($leads);

//     dd($telegram_message);
// })->middleware('auth');

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
// Отображение на сайте
Route::post('/check', 'AppController@ajax_check')->middleware('auth');


// --------------------------------------- Настройки -----------------------------------------------

Route::any('/set_setting', 'SettingController@ajax_set_setting')->middleware('auth');

Route::resource('/settings', 'SettingController')->middleware('auth');


// ---------------------------------------- Телефоны --------------------------------------------------

Route::post('/add_extra_phone', 'PhoneController@ajax_add_extra_phone')->middleware('auth')->name('phones.add_extra_phone');;


// -------------------------------------- Пользователи ------------------------------------------------

Route::resource('/users', 'UserController')->middleware('auth');
Route::get('/myprofile', 'UserController@myprofile')->middleware('auth')->name('users.myprofile');
Route::patch('/updatemyprofile', 'UserController@updatemyprofile')->middleware('auth')->name('users.updatemyprofile');


// ---------------------------------- Категории альбомов -------------------------------------------

// Текущая добавленная/удаленная категория альбомов
Route::any('/albums_categories', 'AlbumsCategoryController@index')->middleware('auth');
// Основные методы
Route::resource('/albums_categories', 'AlbumsCategoryController')->middleware('auth');


// --------------------------------------- Альбомы -----------------------------------------------

// Route::resource('/albums', 'AlbumController')->middleware('auth');
Route::get('/albums', 'AlbumController@index')->middleware('auth')->name('albums.index');
Route::get('/albums/create', 'AlbumController@create')->middleware('auth')->name('albums.create');
Route::get('/albums/{alias}', 'AlbumController@sections')->middleware('auth')->name('albums.sections');
Route::post('/albums', 'AlbumController@store')->middleware('auth')->name('albums.store');
Route::get('/albums/{alias}/edit', 'AlbumController@edit')->middleware('auth')->name('albums.edit');
Route::patch('/albums/{id}', 'AlbumController@update')->middleware('auth')->name('albums.update');
Route::delete('/albums/{id}', 'AlbumController@destroy')->middleware('auth')->name('albums.destroy');


// Проверка на существование
Route::post('/albums_check', 'AlbumController@ajax_check')->middleware('auth');

// Открытие модалки прикрепления альбома
Route::post('/album_add', 'AlbumController@album_add')->middleware('auth')->name('album.add');
// Получение альбомов по категории
Route::post('/albums_select', 'AlbumController@albums_select')->middleware('auth');
// Получение альбома
Route::any('/album_get', 'AlbumController@album_get')->middleware('auth');


// Группа с префиксом
Route::prefix('/albums/{alias}')->group(function () {

  // ----------------------------------- Фотографии -----------------------------------------------

	Route::resource('/photos', 'PhotoController');
  // Загрузка фоток через ajax через dropzone.js
});

Route::post('/photo_index', 'PhotoController@ajax_index');

Route::any('/photo_store', 'PhotoController@ajax_store')->name('photos.ajax_store');

Route::post('/photo_edit/{id}', 'PhotoController@ajax_edit')->name('photos.ajax_edit');

Route::patch('/photo_update/{id}', 'PhotoController@ajax_update');


// --------------------------------------- Помещения -----------------------------------------------
Route::resource('/places', 'PlaceController')->middleware('auth');

// --------------------------------------- Склады -----------------------------------------------
// Route::resource('stocks', 'StockController')->middleware('auth');

// --------------------------------------- Свойства -----------------------------------------------
Route::post('/ajax_add_property', 'PropertyController@add_property')->middleware('auth');


// ---------------------------------------- Метрики -------------------------------------------------
// Основные методы
Route::resource('/metrics', 'MetricController')->middleware('auth');

// Пишем метрику через ajax
Route::post('/ajax_store_metric', 'MetricController@ajax_store')->middleware('auth');

// Добавляем / удаляем связь сущности с метрикой
Route::match(['get', 'post'], '/ajax_add_relation_metric', 'MetricController@ajax_add_relation')->middleware('auth')->name('metrics.add_relation');
Route::any('/ajax_delete_relation_metric', 'MetricController@ajax_delete_relation')->middleware('auth');

Route::post('/ajax_add_metric_value', 'MetricController@add_metric_value')->middleware('auth');


// ---------------------------------------- Состав -------------------------------------------------

Route::post('/ajax_add_relation_composition', 'CompositionController@ajax_add_relation')->middleware('auth');
Route::post('/ajax_delete_relation_composition', 'CompositionController@ajax_delete_relation')->middleware('auth');

Route::post('/ajax_add_page_composition', 'CompositionController@ajax_add')->middleware('auth');

Route::any('/get_units_list', 'UnitController@get_units_list')->middleware('auth');
Route::post('/ajax_get_article_inputs', 'ArticleController@get_inputs')->middleware('auth');


// ---------------------------------- Артикулы -------------------------------------------
// Основные методы
Route::resource('articles_groups', 'ArticlesGroupController')->middleware('auth');


// ------------------------------------- Категории сырья -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/raws_categories', 'RawsCategoryController@index')->middleware('auth');
Route::match(['get', 'post'], '/raws_categories/{id}/edit', 'RawsCategoryController@edit')->middleware('auth');
// Основные методы
Route::resource('/raws_categories', 'RawsCategoryController')->middleware('auth');


// --------------------------------- Продукция сырья --------------------------------------------

// Основные методы
Route::resource('/raws_products', 'RawsProductController')->middleware('auth');

// Route::any('/ajax_raws_count', 'RawsProductController@ajax_count')->middleware('auth');
// Route::any('/raws_products_create_mode', 'RawsProductController@ajax_change_create_mode')->middleware('auth');

// Route::any('/raws_products_list', 'RawsProductController@ajax_get_products_list')->middleware('auth');


// ---------------------------------- Сырьё (Артикулы) -------------------------------------------

// Основные методы
Route::resource('/raws', 'RawController');
// Route::get('/raws/search/{text_fragment}', 'RawController@search')->middleware('auth');
Route::post('/raws/search/{text_fragment}', 'RawController@search')->middleware('auth');
// Архивация
Route::post('/raws/archive/{id}', 'RawController@archive')->middleware('auth');
// Фото
Route::any('/raw/add_photo', 'RawController@add_photo')->middleware('auth');
Route::post('/raw/photos', 'RawController@photos')->middleware('auth');

Route::any('/raws_create_mode', 'RawController@ajax_change_create_mode')->middleware('auth');


// -------------------------------- Категории товаров -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/goods_categories', 'GoodsCategoryController@index')->middleware('auth');
Route::match(['get', 'post'], '/goods_categories/{id}/edit', 'GoodsCategoryController@edit')->middleware('auth');
// Основные методы
Route::resource('/goods_categories', 'GoodsCategoryController')->middleware('auth');

Route::any('/goods_category_metrics', 'GoodsCategoryController@ajax_get_metrics')->middleware('auth');
Route::any('/goods_category_compositions', 'GoodsCategoryController@ajax_get_compositions')->middleware('auth');


// --------------------------------- Группы товаров --------------------------------------------

// Основные методы
Route::resource('/goods_products', 'GoodsProductController')->middleware('auth');

Route::any('/ajax_articles_groups_count', 'ArticlesGroupController@ajax_count')->middleware('auth');
Route::any('/ajax_articles_groups_set_status', 'ArticlesGroupController@ajax_set_status')->middleware('auth');

Route::any('/goods_products_list', 'GoodsProductController@ajax_get_products_list')->middleware('auth');


// ---------------------------------- Товары (Артикулы) -------------------------------------------
// Route::any('/goods/create', 'GoodsController@create')->middleware('auth');
//
// Фотки
Route::any('/goods/add_photo', 'GoodsController@add_photo')->middleware('auth');
Route::post('/goods/photos', 'GoodsController@photos')->middleware('auth');

// Основные методы
Route::resource('/goods', 'GoodsController');
Route::post('/goods/search/{text_fragment}', 'GoodsController@search')->middleware('auth');
// Архивация
Route::post('/goods/archive/{id}', 'GoodsController@archive')->middleware('auth');

// Отображение на сайте
Route::post('/goods_sync', 'GoodsController@ajax_sync')->middleware('auth');

Route::any('/goods_check', 'GoodsController@ajax_check')->middleware('auth');

// Отображение на сайте
Route::any('/goods_categories_get_products', 'GoodsController@ajax_get_products')->middleware('auth');



Route::any('/create_mode', 'CreateModeController@ajax_change_create_mode')->middleware('auth');

Route::any('/ajax_articles_groups_count', 'ArticlesGroupController@ajax_count');

Route::any('/ajax_articles_groups_set_status', 'ArticlesGroupController@ajax_set_status');

Route::any('/articles_groups_list', 'ArticlesGroupController@ajax_articles_groups_list');



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


// -------------------------------- Расходные материалы -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/expendables_categories', 'ExpendablesCategoryController@index')->middleware('auth');
// Основные методы
Route::resource('/expendables_categories', 'ExpendablesCategoryController')->middleware('auth');


// ----------------------------------------- Секторы -----------------------------------------------
// Текущий добавленный/удаленный сектор
Route::any('/sectors', 'SectorController@index')->middleware('auth');
// Основные методы
Route::resource('/sectors', 'SectorController')->middleware('auth');

// --------------------------------------- Компании -----------------------------------------------

// Основные методы
Route::resource('/companies', 'CompanyController')->middleware('auth');
// Проверка существования компании в базе по ИНН
Route::post('/companies/check_company', 'CompanyController@checkcompany')->middleware('auth')->name('companies.checkcompany');


// --------------------------- Дополнительные реквизиты компании -------------------------------------

// Основные методы
Route::resource('/extra_requisites', 'ExtraRequisiteController')->middleware('auth');

// ------------------------------------------------ Планирование -------------------------------------

Route::get('plans', 'PlanController@index')->name('plans.index');

Route::get('plans/{alias}', 'PlanController@show')->name('plans.show');
// Основные методы
// Route::resource('plans', 'PlanController')->middleware('auth');

// ------------------------------------------------ Статистика ---------------------------------------

// Основные методы
// Route::resource('statistics', 'StatisticsController')->middleware('auth');


// ---------------------------------------------- Лиды -----------------------------------------------

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


// --------------------------------------- Расчеты -----------------------------------------------

// Основные методы
Route::resource('estimates', 'EstimateController')->middleware('auth');

// Отображение на сайте
Route::any('/estimates_check', 'EstimateController@ajax_check')->middleware('auth');

Route::delete('/workflows/{id}', 'EstimateController@ajax_destroy_composition')->middleware('auth');
Route::any('/workflows/{id}/edit', 'WorkflowController@ajax_edit')->middleware('auth');

// --------------------------------------- Заказы -----------------------------------------------

// Основные методы
Route::resource('orders', 'OrderController')->middleware('auth');


// Отображение на сайте
Route::any('/orders_check', 'OrderController@ajax_check')->middleware('auth');


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


// ---------------------------------------- Источники --------------------------------------------


// Проверка на существование аккаунта
Route::any('/get_source_services_list', 'SourceServiceController@get_source_services_list')->middleware('auth');

// ---------------------------------------- Аккаунты --------------------------------------------

// Основные методы
Route::resource('/accounts', 'AccountController')->middleware('auth');

// Проверка на существование аккаунта
Route::post('/accounts_check', 'AccountController@ajax_check')->middleware('auth');


// --------------------------------------- Рекламные кампании -----------------------------------------------

// Основные методы
// Route::resource('/campaigns', 'CampaignController')->middleware('auth');


// --------------------------------------- Отзывы -----------------------------------------------

// Основные методы
Route::resource('/feedback', 'FeedbackController')->middleware('auth');


// --------------------------------------- Расходы -----------------------------------------------

// Основные методы
// Route::resource('/expenses', 'ExpenseController')->middleware('auth');


// --------------------------------------- Зарплаты -----------------------------------------------

// Основные методы
// Route::resource('/salaries', 'SalaryController')->middleware('auth');


// --------------------------------------- Социальные сети -----------------------------------------------

// Основные методы
// Route::resource('social_networks', 'SocialNetworkController')->middleware('auth');


// -------------------------------------- Поставщики -----------------------------------------------

// Основные методы
Route::resource('/suppliers', 'SupplierController')->middleware('auth');


// -------------------------------------- Заявки поставщикам ---------------------------------------------

// Основные методы
Route::resource('applications', 'ApplicationController')->middleware('auth');


// -------------------------------------- Товарные накладные ---------------------------------------------

// Основные методы
Route::resource('consignments', 'ConsignmentController');


// ------------------------------------ Производители ----------------------------------------------------

// Основные методы
Route::resource('/manufacturers', 'ManufacturerController')->middleware('auth');


// ------------------------------------ Дилеры ----- ----------------------------------------------------

// Основные методы

Route::get('/dealers/create-user', 'DealerController@createDealerUser')->middleware('auth')->name('dealers.createDealerUser');
Route::get('/dealers/create-company', 'DealerController@createDealerCompany')->middleware('auth')->name('dealers.createDealerCompany');

Route::post('/dealers/store-user', 'DealerController@storeUser')->middleware('auth')->name('dealers.storeUser');
Route::post('/dealers/store-company', 'DealerController@storeCompany')->middleware('auth')->name('dealers.storeCompany');

Route::patch('/dealers/update-user/{id}', 'DealerController@updateDealerUser')->middleware('auth')->name('dealers.updateDealerUser');
Route::patch('/dealers/update-company/{id}', 'DealerController@updateDealerCompany')->middleware('auth')->name('dealers.updateDealerCompany');

Route::resource('/dealers', 'DealerController')->middleware('auth');


// ------------------------------------ Клиенты ----------------------------------------------------------

// Основные методы
Route::patch('/create_client', 'ClientController@ajax_create')->middleware('auth');
Route::any('/store_client', 'ClientController@ajax_store')->middleware('auth');

Route::resource('/clients', 'ClientController')->middleware('auth');


// ------------------------------------ Банки ----------------------------------------------------------

// Основные методы
Route::resource('banks', 'BankController')->middleware('auth');


// ------------------------------------ Банковские аккаунты -------------------------------------------------

// Основные методы
Route::any('/create_bank_account', 'BankAccountController@ajax_create')->middleware('auth');
Route::any('/edit_bank_account', 'BankAccountController@ajax_edit')->middleware('auth');
Route::any('/update_bank_account', 'BankAccountController@ajax_update')->middleware('auth');
Route::any('/store_bank_account', 'BankAccountController@ajax_store')->middleware('auth');
Route::resource('/bank_accounts', 'BankAccountController')->middleware('auth');


// -------------------------------------- Показатели ---------------------------------------------

// Основные методы
Route::resource('indicators', 'IndicatorController');


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
Route::any('/cities_list', 'CityController@cities_list')->middleware('auth');
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
Route::any('/department_check', 'DepartmentController@ajax_check')->middleware('auth');


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

Route::any('(:any)', 'SiteController@kek');
// ----------------------------------------- Сайты ----------------------------------------------
Route::resource('/sites', 'SiteController')->middleware('auth');
// Route::get('/sites/{id}/sections', 'SiteController@sections')->middleware('auth')->name('sites.sections');
// Проверка на существование домена сайта
Route::post('/site_check', 'SiteController@ajax_check')->middleware('auth');




// Разделы сайта
Route::prefix('/sites/{site_id}')->group(function () {

	// --------------------------------------- Страницы ---------------------------------------------

    // Основные методы
	Route::resource('/pages', 'PageController')->middleware('auth');

    // Проверка на существование страницы
	Route::post('/page_check', 'PageController@ajax_check')->middleware('auth');

	// --------------------------------------- Навигации --------------------------------------------

	// Основные методы
	Route::resource('/navigations', 'NavigationController')->middleware('auth');

	// Проверка на существование навигации
	Route::post('/navigation_check', 'NavigationController@ajax_check')->middleware('auth');

	// Меню навигации
	Route::prefix('/navigations/{navigation_id}')->group(function () {

		// ---------------------------------------- Меню -------------------------------------------

		// Текущий добавленный/удаленный пунк меню
		Route::any('/menus', 'MenuController@index');

		// Основные методы
		Route::resource('/menus', 'MenuController');

	});

});


// Поиск продукции для добавления на сайт
// Route::any('/catalog_product/search_add_product', 'CatalogProductController@search_add_product')->middleware('auth');

// // Поиск продукции для добавления на сайт
// Route::any('/catalog_product/add_product', 'CatalogProductController@add_product')->middleware('auth');




// ---------------------------------------- Новости -------------------------------------------

// Основные методы
Route::resource('/news', 'NewsController')->middleware('auth');


// ----------------------------------------- Каталоги ------------------------------------------


// Основные методы
Route::resource('catalogs', 'CatalogController');
// Проверка на существование
// Route::post('/catalog_check', 'CatalogController@ajax_check')->middleware('auth');

// -------------------------------- Наполнение каталогов -------------------------------------

Route::prefix('catalogs/{catalog_id}')->group(function () {

	// Текущий добавленный/удаленный пунк меню
	Route::any('catalogs_items', 'CatalogsItemController@index');

	// Основные методы
	Route::resource('catalogs_items', 'CatalogsItemController');
});




// ------------------------------------- Отображение сессии -----------------------------------------
Route::get('/show_session', 'HelpController@show_session')->middleware('auth')->name('help.show_session');