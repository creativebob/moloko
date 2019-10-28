<?php

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

Route::get('/cities-test', 'CityController@test');

Auth::routes();

// Вход в панель управления
Route::get('/', 'AppController@enter');

// Всякая хрень для проверки
// Route::resource('/site_api', 'ApiController');

Route::get('/img/{path}', 'ImageController@show')->where('path', '.*');
Route::get('/home', 'HomeController@index')->name('home');
Route::any('getaccess', 'GetAccessController@set')->middleware('auth')->name('getaccess.set');


// ----------------------------- Рабочий стол -------------------------------------

Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');

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
Route::post('/system', 'AppController@ajax_system')->middleware('auth');
// Отображение на сайте
Route::post('/display', 'AppController@ajax_display')->middleware('auth');
// Отображение на сайте
Route::post('/check', 'AppController@ajax_check')->middleware('auth');
// Пересчитать уровни категорий
Route::get('/recalculate_categories/{entity}', 'AppController@recalculate_categories')->middleware('auth');


Route::get('/draft_article/{entity}/{id}', 'AppController@draft_article')->middleware('auth');
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
Route::any('/albums_categories', 'AlbumsCategoryController@index');
// Основные методы
Route::resource('/albums_categories', 'AlbumsCategoryController');


// --------------------------------------- Альбомы -----------------------------------------------

Route::resource('/albums', 'AlbumController');
// Route::get('/albums', 'AlbumController@index')->middleware('auth')->name('albums.index');
// Route::get('/albums/create', 'AlbumController@create')->middleware('auth')->name('albums.create');
// Route::get('/albums/{alias}', 'AlbumController@sections')->middleware('auth')->name('albums.sections');
// Route::post('/albums', 'AlbumController@store')->middleware('auth')->name('albums.store');
// Route::get('/albums/{alias}/edit', 'AlbumController@edit')->middleware('auth')->name('albums.edit');
// Route::patch('/albums/{id}', 'AlbumController@update')->middleware('auth')->name('albums.update');
// Route::delete('/albums/{id}', 'AlbumController@destroy')->middleware('auth')->name('albums.destroy');


// Проверка на существование
Route::post('/albums_check', 'AlbumController@ajax_check');

// Открытие модалки прикрепления альбома
Route::post('/album_add', 'AlbumController@ajax_add')->name('album.add');
// Получение альбомов по категории
Route::any('/albums_select', 'AlbumController@ajax_get_select');
// Получение альбома
Route::any('/album_get', 'AlbumController@ajax_get');


// Группа с префиксом
Route::prefix('/albums/{alias}')->group(function () {

  // ----------------------------------- Фотографии -----------------------------------------------

	Route::resource('/photos', 'PhotoController');
  // Загрузка фоток через ajax через dropzone.js
});

Route::any('/photo_index', 'PhotoController@ajax_index');


Route::any('/photo_store', 'PhotoController@ajax_store')->name('photos.ajax_store');

Route::post('/photo_edit/{id}', 'PhotoController@ajax_edit')->name('photos.ajax_edit');

Route::patch('/photo_update/{id}', 'PhotoController@ajax_update');
Route::delete('/photo_delete/{id}', 'PhotoController@ajax_delete');


// --------------------------------------- Помещения -----------------------------------------------
// Route::resource('/places', 'PlaceController')->middleware('auth');

// --------------------------------------- Склады -----------------------------------------------
Route::resource('stocks', 'StockController')->middleware('auth');

// --------------------------------------- Свойства -----------------------------------------------
Route::post('/ajax_add_property', 'PropertyController@add_property')->middleware('auth');


// ---------------------------------------- Метрики -------------------------------------------------
Route::get('/metrics', 'MetricController@store');
// Основные методы
Route::resource('/metrics', 'MetricController')->middleware('auth');

// Добавляем метрику на страницу
Route::post('/ajax_get_metric', 'MetricController@ajax_get_metric')->middleware('auth');

// Добавляем значение метрики (список)
Route::post('/ajax_get_metric_value', 'MetricController@ajax_get_metric_value')->middleware('auth');


// ---------------------------------------- Состав -------------------------------------------------

Route::any('/get_units_list', 'UnitController@get_units_list')->middleware('auth');
Route::post('/ajax_get_article_inputs', 'ArticleController@get_inputs')->middleware('auth');

Route::post('/ajax_get_category_raw', 'RawController@ajax_get_category_raw')->middleware('auth');
Route::post('/ajax_get_raw', 'RawController@ajax_get_raw')->middleware('auth');

Route::any('/ajax_get_container', 'ContainerController@ajax_get_container')->middleware('auth');

Route::any('/ajax_get_attachment', 'AttachmentController@ajax_get_attachment')->middleware('auth');

Route::any('/ajax_get_goods', 'GoodsController@ajax_get_goods')->middleware('auth');

Route::post('/ajax_get_category_workflow', 'WorkflowController@ajax_get_category_workflow')->middleware('auth');
Route::post('/ajax_get_workflow', 'WorkflowController@ajax_get_workflow')->middleware('auth');

Route::post('/ajax_get_service', 'ServiceController@ajax_get_service')->middleware('auth');


// ---------------------------------- Артикулы -------------------------------------------
// Основные методы
Route::resource('articles_groups', 'ArticlesGroupController')->middleware('auth');


// ------------------------------------- Категории сырья -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/raws_categories', 'RawsCategoryController@index');
Route::post('/raws_categories/{id}/edit', 'RawsCategoryController@edit');
// Основные методы
Route::resource('/raws_categories', 'RawsCategoryController');


// ---------------------------------- Сырьё (Артикулы) -------------------------------------------

Route::get('/raws/search/{search}', 'RawController@search');

// Основные методы
Route::resource('/raws', 'RawController');
// Route::get('/raws/search/{text_fragment}', 'RawController@search')->middleware('auth');
Route::post('/raws/search/{text_fragment}', 'RawController@search');
// Архивация
Route::post('/raws/archive/{id}', 'RawController@archive');
// Дублирование
Route::post('/raws/replicate/{id}', 'RawController@replicate');
// Фото
Route::any('/raw/add_photo', 'RawController@add_photo');
Route::post('/raw/photos', 'RawController@photos');

Route::any('/raws_create_mode', 'RawController@ajax_change_create_mode');


// ---------------------------------- Склады сырья -------------------------------------------
// Основные методы
Route::resource('/raws_stocks', 'RawsStockController');

// ------------------------------------- Категории упаковок -------------------------------------------
// Текущая добавленная/удаленная категория
Route::any('/containers_categories', 'ContainersCategoryController@index');
Route::match(['get', 'post'], '/containers_categories/{id}/edit', 'ContainersCategoryController@edit');
// Основные методы
Route::resource('/containers_categories', 'ContainersCategoryController');


// ---------------------------------- Упаковки (Артикулы) -------------------------------------------

Route::get('/containers/search/{search}', 'ContainerController@search');
// Основные методы
Route::resource('/containers', 'ContainerController');
Route::post('/containers/search/{text_fragment}', 'СontainerController@search');
// Дублирование
Route::post('/containers/replicate/{id}', 'ContainerController@replicate');
// Архивация
Route::post('/containers/archive/{id}', 'ContainerController@archive');
// Фото
Route::any('/container/add_photo', 'СontainerController@add_photo');
Route::post('/container/photos', 'СontainerController@photos');

Route::any('/containers_create_mode', 'СontainerController@ajax_change_create_mode');


// ---------------------------------- Склады упаковок -------------------------------------------
// Основные методы
Route::resource('/containers_stocks', 'ContainersStockController');


// ------------------------------------- Категории вложений -------------------------------------------
// Текущая добавленная/удаленная категория
Route::any('/attachments_categories', 'AttachmentsCategoryController@index');
Route::match(['get', 'post'], '/attachments_categories/{id}/edit', 'AttachmentsCategoryController@edit');
// Основные методы
Route::resource('/attachments_categories', 'AttachmentsCategoryController');


// ---------------------------------- Вложения (Артикулы) -------------------------------------------

Route::get('/attachments/search/{search}', 'AttachmentController@search');

// Основные методы
Route::resource('/attachments', 'AttachmentController');
Route::post('/attachments/search/{text_fragment}', 'AttachmentController@search');
// Дублирование
Route::post('/attachments/replicate/{id}', 'AttachmentController@replicate');
// Архивация
Route::post('/attachments/archive/{id}', 'AttachmentController@archive');
// Фото
Route::any('/attachment/add_photo', 'СontainerController@add_photo');
Route::post('/attachment/photos', 'AttachmentController@photos');

Route::any('/attachments_create_mode', 'СontainerController@ajax_change_create_mode');


// ---------------------------------- Склады вложений -------------------------------------------
// Основные методы
Route::resource('/attachments_stocks', 'AttachmentsStockController');


// ------------------------------------- Категории оборудования -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/equipments_categories', 'EquipmentsCategoryController@index')->middleware('auth');
Route::match(['get', 'post'], '/equipments_categories/{id}/edit', 'EquipmentsCategoryController@edit')->middleware('auth');
// Основные методы
Route::resource('/equipments_categories', 'EquipmentsCategoryController')->middleware('auth');

// ---------------------------------- Оборудование-------------------------------------------

// Основные методы
Route::resource('/equipments', 'EquipmentController');

// Архивация
Route::post('/equipments/archive/{id}', 'EquipmentController@archive')->middleware('auth');
// Фото
Route::any('/equipment/add_photo', 'EquipmentController@add_photo')->middleware('auth');
Route::post('/equipment/photos', 'EquipmentController@photos')->middleware('auth');

Route::any('/equipments_create_mode', 'EquipmentController@ajax_change_create_mode')->middleware('auth');

// ---------------------------------- Помещения -------------------------------------------

// Основные методы
Route::resource('/rooms', 'RoomController');
// Route::get('/rooms/search/{text_fragment}', 'RawController@search')->middleware('auth');
Route::post('/rooms/search/{text_fragment}', 'RoomController@search')->middleware('auth');
// Архивация
Route::post('/rooms/archive/{id}', 'RoomController@archive')->middleware('auth');
// Фото
Route::any('/room/add_photo', 'RoomController@add_photo')->middleware('auth');
Route::post('/room/photos', 'RoomController@photos')->middleware('auth');


// -------------------------------- Категории товаров -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/goods_categories', 'GoodsCategoryController@index');
Route::match(['get', 'post'], '/goods_categories/{id}/edit', 'GoodsCategoryController@edit')->middleware('auth');
// Основные методы
Route::resource('/goods_categories', 'GoodsCategoryController');

Route::any('/goods_category_metrics', 'GoodsCategoryController@ajax_get_metrics');
Route::any('/goods_category_compositions', 'GoodsCategoryController@ajax_get_compositions');


// ------------------------------------- Категории помещений -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/rooms_categories', 'RoomsCategoryController@index')->middleware('auth');
Route::match(['get', 'post'], '/rooms_categories/{id}/edit', 'RoomsCategoryController@edit')->middleware('auth');
// Основные методы
Route::resource('/rooms_categories', 'RoomsCategoryController')->middleware('auth');


// --------------------------------- Группы товаров --------------------------------------------

Route::any('/ajax_articles_groups_count', 'ArticlesGroupController@ajax_count')->middleware('auth');
Route::any('/ajax_articles_groups_set_status', 'ArticlesGroupController@ajax_set_status')->middleware('auth');



// ---------------------------------- Товары (Артикулы) -------------------------------------------
// Route::any('/goods/create', 'GoodsController@create')->middleware('auth');

Route::get('/goods/search/{search}', 'GoodsController@search');
//
// Фотки
Route::any('/goods/add_photo', 'GoodsController@add_photo')->middleware('auth');
Route::post('/goods/photos', 'GoodsController@photos')->middleware('auth');

// Основные методы
Route::resource('/goods', 'GoodsController');
Route::post('/goods/search/{text_fragment}', 'GoodsController@search')->middleware('auth');
// Дублирование
Route::post('/goods/replicate/{id}', 'GoodsController@replicate');
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

Route::any('/ajax_processes_groups_count', 'ProcessesGroupController@ajax_count');
Route::any('/ajax_processes_groups_set_status', 'ProcessesGroupController@ajax_set_status');
Route::any('/processes_groups_list', 'ProcessesGroupController@ajax_processes_groups_list');


// ---------------------------------- Склады упаковок -------------------------------------------
// Основные методы
Route::resource('/goods_stocks', 'GoodsStockController');



// -------------------------------- Категории услуг -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/services_categories', 'ServicesCategoryController@index')->middleware('auth');
Route::match(['get', 'post'], '/services_categories/{id}/edit', 'ServicesCategoryController@edit')->middleware('auth');
// Основные методы
Route::resource('/services_categories', 'ServicesCategoryController')->middleware('auth');
// Проверка на существование
Route::post('/services_category_check', 'ServicesCategoryController@ajax_check')->middleware('auth');


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


// -------------------------------- Категории рабочих процессов -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/workflows_categories', 'WorkflowsCategoryController@index')->middleware('auth');
Route::match(['get', 'post'], '/workflows_categories/{id}/edit', 'WorkflowsCategoryController@edit')->middleware('auth');
// Основные методы
Route::resource('/workflows_categories', 'WorkflowsCategoryController')->middleware('auth');
// Проверка на существование
// Route::post('/workflows_category_check', 'ServicesCategoryController@ajax_check')->middleware('auth');


// ---------------------------------- Рабочие процессы -------------------------------------------

// Основные методы
Route::resource('workflows', 'WorkflowController');

// Архивация
Route::post('/workflows/archive/{id}', 'WorkflowController@archive')->middleware('auth');
// Фото
Route::any('/workflow/add_photo', 'WorkflowController@add_photo')->middleware('auth');
Route::post('/workflow/photos', 'WorkflowController@photos')->middleware('auth');

Route::any('/workflows_create_mode', 'WorkflowController@ajax_change_create_mode')->middleware('auth');

// -------------------------------- Расходные материалы -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/expendables_categories', 'ExpendablesCategoryController@index')->middleware('auth');
Route::match(['get', 'post'], '/expendables_categories/{id}/edit', 'ExpendablesCategoryController@edit')->middleware('auth');
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

Route::get('/leads_export', 'LeadController@export')->middleware('auth');
// Route::resource('/leads_calls', 'LeadController@leads_calls')->middleware('auth');

// Продажа
Route::patch('/leads/{id}/saling', 'LeadController@saling')->middleware('auth');

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
Route::any('/lead_free', 'LeadController@ajax_lead_free')->middleware('auth');

// Добавление комментария
Route::post('/leads_add_note', 'LeadController@ajax_add_note')->middleware('auth');
// Поиск лида по номеру телефона
Route::post('/leads/autofind/{phone}', 'LeadController@ajax_autofind_phone')->middleware('auth');


// --------------------------------------- Расчеты -----------------------------------------------

// Основные методы
Route::resource('estimates', 'EstimateController')->middleware('auth');

// Отображение на сайте
Route::any('/create_estimate', 'EstimateController@ajax_create');
Route::any('/update_estimate', 'EstimateController@ajax_update');

Route::resource('/estimates_goods_items', 'EstimatesGoodsItemController');
Route::resource('/estimates_services_items', 'EstimatesServicesItemController');

Route::any('/estimate_items_edit/{id}', 'EstimatesItemController@ajax_edit')->middleware('auth');


// Route::delete('/workflows/{id}', 'EstimateController@ajax_destroy_composition')->middleware('auth');
// Route::any('/estimates_items/add', 'EstimateController@ajax_add')->middleware('auth');

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

Route::any('/consignments/categories', 'ConsignmentController@categories')->name('consignments.categories');;
Route::patch('/consignments/{id}/posting', 'ConsignmentController@posting')->name('consignments.posting');
Route::get('/consignments/{id}/unpost', 'ConsignmentController@unpost')->name('consignments.unpost');

// Перерасчет накладых
Route::get('/consignments/reposting', 'ConsignmentController@reposting')->name('consignments.reposting');

// Основные методы
Route::resource('/consignments', 'ConsignmentController');

//Route::any('/consignments_items', 'ConsignmentsItemController@store');
Route::resource('/consignments_items', 'ConsignmentsItemController');

// -------------------------------------- Наряды на производство ---------------------------------------------

Route::any('/productions/categories', 'ProductionController@categories')->name('productions.categories');;
Route::patch('/productions/{id}/produced', 'ProductionController@produced')->name('productions.produced');
Route::get('/productions/{id}/unproduced', 'ProductionController@unproduced')->name('productions.unproduced');
// Основные методы
Route::resource('/productions', 'ProductionController');

//Route::get('/productions_items', 'ProductionsItemController@store');
Route::resource('/productions_items', 'ProductionsItemController');


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


Route::get('/dealers/create-user', 'ClientController@createClientUser')->middleware('auth')->name('clients.createClientUser');
Route::get('/dealers/create-company', 'ClientController@createClientCompany')->middleware('auth')->name('clients.createClientCompany');

Route::post('/clients/store-user', 'ClientController@storeUser')->middleware('auth')->name('clients.storeUser');
Route::post('/clients/store-company', 'ClientController@storeCompany')->middleware('auth')->name('clients.storeCompany');

Route::patch('/clients/update-user/{id}', 'ClientController@updateClientUser')->middleware('auth')->name('clients.updateClientUser');
Route::patch('/clients/update-company/{id}', 'ClientController@updateDealerCompany')->middleware('auth')->name('clients.updateClientCompany');

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
Route::any('/city_vk', 'CityController@get_vk_city')->middleware('auth');

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


Route::any('/ajax_get_filials_for_catalogs_service', 'DepartmentController@ajax_get_filials_for_catalogs_service')->middleware('auth');
Route::any('/ajax_get_filials_for_catalogs_goods', 'DepartmentController@ajax_get_filials_for_catalogs_goods')->middleware('auth');



// ----------------------------------------- Должности --------------------------------------------

// Основные методы
Route::resource('/positions', 'PositionController')->middleware('auth');
// Список отделов филиала и доступных должностей
Route::post('/positions_list', 'PositionController@positions_list')->middleware('auth');


// -------------------------------------- Штат компании ---------------------------------------------

// Основные методы
Route::resource('/staff', 'StafferController');


// --------------------------------------- Сотрудники ---------------------------------------------

// Основные методы
Route::get('/employees/dismissal', 'EmployeeController@dismissal')->middleware('auth');

Route::resource('/employees', 'EmployeeController')->middleware('auth');

// Увольнение
Route::post('/employee_dismiss_modal', 'EmployeeController@ajax_employee_dismiss_modal')->middleware('auth');
Route::post('/employee_dismiss', 'EmployeeController@ajax_employee_dismiss')->middleware('auth');

// Трудоустройство
Route::post('/employee_employment_modal', 'EmployeeController@ajax_employee_employment_modal')->middleware('auth');
Route::post('/employee_employment', 'EmployeeController@ajax_employee_employment')->middleware('auth');


// ------------------------------------------ Списки -----------------------------------------------
Route::resource('/booklists', 'BooklistController')->middleware('auth');

Route::post('/setbooklist', 'BooklistController@setbooklist')->middleware('auth')->name('booklists.setbooklist');
Route::get('/updatebooklist', 'BooklistController@setbooklist')->middleware('auth')->name('booklists.updatebooklist');

Route::any('(:any)', 'SiteController@kek');
// ----------------------------------------- Сайты ----------------------------------------------
Route::resource('/sites', 'SiteController');
// Route::get('/sites/{id}/sections', 'SiteController@sections')->middleware('auth')->name('sites.sections');
// Проверка на существование домена сайта
Route::post('/site_check', 'SiteController@ajax_check');

// ----------------------------------------- Плагины ----------------------------------------------
Route::resource('/plugins', 'PluginController');
Route::delete('/plugins/{id}/ajax_delete', 'PluginController@ajax_destroy');


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


// ----------------------------------------- Рубрики ------------------------------------------

// Основные методы
Route::resource('rubricators', 'RubricatorController');

// Проверка на существование
// Route::post('/catalog_check', 'CatalogController@ajax_check')->middleware('auth');

// -------------------------------- Наполнение каталогов услуг -------------------------------------

Route::prefix('rubricators/{rubricator_id}')->group(function () {

    // Текущий добавленный/удаленный пункт
    Route::any('rubricators_items', 'RubricatorsItemController@index');

    // Основные методы
    Route::resource('rubricators_items', 'RubricatorsItemController');

    //
    Route::any('get_rubricators_items', 'RubricatorsItemController@get_rubricators_items');
});


// ---------------------------------------- Новости -------------------------------------------

// Основные методы
Route::resource('/news', 'NewsController');


// ----------------------------------------- Каталоги товаров ------------------------------------------

// Основные методы
Route::resource('catalogs_goods', 'CatalogsGoodsController');
// Проверка на существование
// Route::post('/catalog_check', 'CatalogController@ajax_check')->middleware('auth');

// -------------------------------- Наполнение каталогов товаров -------------------------------------

Route::prefix('catalogs_goods/{catalog_id}')->group(function () {

	// Текущий добавленный/удаленный пунк меню
	Route::any('catalogs_goods_items', 'CatalogsGoodsItemController@index');

	// Основные методы
	Route::resource('catalogs_goods_items', 'CatalogsGoodsItemController');

    Route::delete('/prices_goods/{id}', 'PricesGoodsController@archive');

    Route::post('get_catalogs_goods_items', 'CatalogsGoodsItemController@ajax_get');

    Route::any('get_prices_goods/{id}', 'PricesGoodsController@ajax_get');
    Route::any('edit_prices_goods', 'PricesGoodsController@ajax_edit');
    Route::any('update_prices_goods', 'PricesGoodsController@ajax_update');
    Route::any('prices_goods/{id}/archive', 'PricesGoodsController@ajax_archive');

    Route::any('prices_goods/ajax_store', 'PricesGoodsController@ajax_store');

    Route::any('prices_goods_sync', 'PricesGoodsController@sync')->name('prices_goods.sync');
	
	Route::any('prices_goods_status', 'PricesGoodsController@ajax_status');

    Route::resource('prices_goods', 'PricesGoodsController');
});


// ----------------------------------------- Каталоги услуг ------------------------------------------

// Основные методы
Route::resource('catalogs_services', 'CatalogsServiceController');

// Проверка на существование
// Route::post('/catalog_check', 'CatalogController@ajax_check')->middleware('auth');

// -------------------------------- Наполнение каталогов услуг -------------------------------------


Route::prefix('catalogs_services/{catalog_id}')->group(function () {

    // Текущий добавленный/удаленный пунк меню
    Route::any('catalogs_services_items', 'CatalogsServicesItemController@index');

    // Основные методы
    Route::resource('catalogs_services_items', 'CatalogsServicesItemController');
    Route::delete('prices_services/{id}', 'PricesServiceController@archive');

    Route::post('get_catalogs_services_items', 'CatalogsServicesItemController@ajax_get');

    Route::any('get_prices_service/{id}', 'PricesServiceController@ajax_get');
    Route::any('edit_prices_service', 'PricesServiceController@ajax_edit');
    Route::any('update_prices_service', 'PricesServiceController@ajax_update');
    Route::any('prices_services/{id}/archive', 'PricesServiceController@ajax_archive');

    Route::any('prices_services/ajax_store', 'PricesServiceController@ajax_store');

    Route::any('prices_services_sync', 'PricesServiceController@sync')->name('prices_services.sync');

    Route::resource('prices_services', 'PricesServiceController');
});



//Route::any('catalogs_services_items/prices', 'CatalogsServicesItemController@get_prices');
//Route::any('catalogs_goods_items/prices', 'CatalogsGoodsItemController@get_prices');


// Route::any('archive_prices_service', 'PricesServiceController@ajax_archive');
// Route::delete('prices_service', 'PricesServiceController@ajax_destroy');


// ------------------------------------- Отображение сессии -----------------------------------------
Route::get('/show_session', 'HelpController@show_session')->middleware('auth')->name('help.show_session');
