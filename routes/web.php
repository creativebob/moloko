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
Route::get('/', 'GetAccessController@enter');
Route::any('getaccess', 'GetAccessController@set')
    ->middleware('auth')
    ->name('getaccess.set');

// Кеширование
Route::get('/set_cache', 'System\CacheController@setCache');
Route::get('/clear_cache', 'System\CacheController@clearCache');
Route::get('/recache', 'System\CacheController@reCache');

// Обновления системы
Route::get('/update', 'System\UpdateController@update');
Route::get('/updates/vkusnyashka', 'System\UpdateController@update_vkusnyashka');
Route::get('/updates/add_discounts_entity', 'System\UpdateController@addDiscountsEntity');
Route::get('/updates/add_discounts_recalculate_notification', 'System\UpdateController@addDiscountsRecalculateNotification');

// Парсеры
Route::get('/update_parser', 'ParserController@parser');
Route::get('/roll_house_parser', 'ParserController@roll_house_parser');
Route::get('/parser_rh_goods_metrics', 'ParserController@parserRhGoodsMetrics');
Route::get('/parsers/130420', 'ParserController@parser_130420');
Route::get('/parsers/archive_goods', 'ParserController@parserArchiveGoods');
Route::get('/parsers/prices_goods_total', 'ParserController@parserPricesGoodsTotal');
Route::get('/parsers/add_role', 'ParserController@addRole');
Route::get('/parsers/set-morphs-aliases', 'ParserController@setMorphsAliases');

Route::get('/parsers/test', 'ParserController@test');


// Ролл Хаус (парсинг старой базы)
Route::get('/roll_house/lead_client', 'System\External\RollHouseController@leadClient');
Route::get('/roll_house/user_location', 'System\External\RollHouseController@userLocation');
Route::get('/roll_house/user_name', 'System\External\RollHouseController@puserName');
Route::get('/roll_house/old_base', 'System\External\RollHouseController@oldBase');
Route::get('/roll_house/external_id', 'System\External\RollHouseController@externalId');
Route::get('/roll_house/external_categories', 'System\External\RollHouseController@externalCategories');
Route::get('/roll_house/external_goods', 'System\External\RollHouseController@externalGoods');
Route::get('/roll_house/external_prices', 'System\External\RollHouseController@externalPrices');
Route::get('/roll_house/set_company_id', 'System\External\RollHouseController@setCompanyId');

// Тесты
Route::get('/test', 'System\TestController@test');

// Всякая хрень для проверки
// Route::resource('/site_api', 'ApiController');

Route::get('/img/{item_id}/{entity}/{size?}', 'ImageController@show')->name('get_photo')//    ->where('path', '.*')
;
Route::get('/home', 'HomeController@index')->name('home');


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
// Route::get('/choice_parser', 'ParserController@choice_parser')->middleware('auth');
Route::get('/sort_catalog_parser', 'ParserController@sort_catalog_parser')->middleware('auth');

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
// Пересохраниение связи категории с группой (пока категории товаров)
Route::get('/resave_categories_groups', 'AppController@resaveCategoriesGroups');


Route::get('/draft_article/{entity}/{id}', 'AppController@draft_article')->middleware('auth');
Route::get('/draft_process/{entity}/{id}', 'AppController@draft_process')->middleware('auth');
// --------------------------------------- Настройки -----------------------------------------------

Route::any('/set_setting', 'SettingController@ajax_set_setting')->middleware('auth');

Route::resource('/settings', 'SettingController')->middleware('auth');


// ---------------------------------------- Телефоны --------------------------------------------------

Route::post('/add_extra_phone', 'PhoneController@ajax_add_extra_phone')->middleware('auth')->name('phones.add_extra_phone');;


// -------------------------------------- Пользователи ------------------------------------------------

//Route::resource('/users', 'UserController')->middleware('auth');
Route::get('/profile', 'UserController@profile')
    ->middleware('auth')
    ->name('users.profile');
Route::patch('/update_profile', 'UserController@update_profile')
    ->middleware('auth')
    ->name('users.update_profile');


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
Route::post('/stocks/count', 'StockController@count')->name('stocks.count');
Route::resource('/stocks', 'StockController');


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

Route::any('/ajax_get_related', 'GoodsController@ajax_get_related');

Route::any('/ajax_get_attachment', 'AttachmentController@ajax_get_attachment')->middleware('auth');

Route::any('/ajax_get_container', 'ContainerController@ajax_get_container')->middleware('auth');

Route::any('/ajax_get_goods', 'GoodsController@ajax_get_goods')->middleware('auth');

Route::post('/ajax_get_goods', 'GoodsController@ajax_get_goods')->middleware('auth');


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

// Просмотр архивных
Route::get('/raws/archives', 'RawController@archives')
    ->name('raws.archives');

// Основные методы
Route::resource('/raws', 'RawController');
// Route::get('/raws/search/{text_fragment}', 'RawController@search')->middleware('auth');
Route::post('/raws/search/{text_fragment}', 'RawController@search');
// Архив
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

// Просмотр архивных
Route::get('/containers/archives', 'ContainerController@archives')
    ->name('containers.archives');

// Основные методы
Route::resource('/containers', 'ContainerController');
Route::post('/containers/search/{text_fragment}', 'ContainerController@search');
// Дублирование
Route::post('/containers/replicate/{id}', 'ContainerController@replicate');
// Архив
Route::post('/containers/archive/{id}', 'ContainerController@archive');
// Фото
Route::any('/container/add_photo', 'ContainerController@add_photo');
Route::post('/container/photos', 'ContainerController@photos');

Route::any('/containers_create_mode', 'ContainerController@ajax_change_create_mode');


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

// Просмотр архивных
Route::get('/attachments/archives', 'AttachmentController@archives')
    ->name('attachments.archives');

// Основные методы
Route::resource('/attachments', 'AttachmentController');
Route::post('/attachments/search/{text_fragment}', 'AttachmentController@search');
// Дублирование
Route::post('/attachments/replicate/{id}', 'AttachmentController@replicate');
// Архив
Route::post('/attachments/archive/{id}', 'AttachmentController@archive');
// Фото
Route::any('/attachment/add_photo', 'ContainerController@add_photo');
Route::post('/attachment/photos', 'AttachmentController@photos');

Route::any('/attachments_create_mode', 'ContainerController@ajax_change_create_mode');


// ---------------------------------- Склады вложений -------------------------------------------
// Основные методы
Route::resource('/attachments_stocks', 'AttachmentsStockController');


// ------------------------------------- Категории инструментов -------------------------------------------

// Текущая добавленная/удаленная категория
Route::any('/tools_categories', 'ToolsCategoryController@index')->middleware('auth');
Route::match(['get', 'post'], '/tools_categories/{id}/edit', 'ToolsCategoryController@edit')->middleware('auth');
// Основные методы
Route::resource('/tools_categories', 'ToolsCategoryController')->middleware('auth');

// ---------------------------------- Инструменты-------------------------------------------

// Просмотр архивных
Route::get('/tools/archives', 'ToolController@archives')
    ->name('tools.archives');

// Основные методы
Route::resource('/tools', 'ToolController');

// Архив
Route::post('/tools/archive/{id}', 'ToolController@archive')->middleware('auth');
// Фото
Route::any('/tool/add_photo', 'ToolController@add_photo')->middleware('auth');
Route::post('/tool/photos', 'ToolController@photos')->middleware('auth');

Route::any('/tools_create_mode', 'ToolController@ajax_change_create_mode')->middleware('auth');

// ---------------------------------- Склады инструментов -------------------------------------------
// Основные методы
Route::resource('/tools_stocks', 'ToolsStockController');

// ---------------------------------- Помещения -------------------------------------------

// Просмотр архивных
Route::get('/rooms/archives', 'RoomController@archives')
    ->name('rooms.archives');

// Основные методы
Route::resource('/rooms', 'RoomController');
// Route::get('/rooms/search/{text_fragment}', 'RawController@search')->middleware('auth');
Route::post('/rooms/search/{text_fragment}', 'RoomController@search')->middleware('auth');
// Архив
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
// Фотки
Route::any('/goods/add_photo', 'GoodsController@add_photo');
Route::post('/goods/photos', 'GoodsController@photos');

// Просмотр архивных
Route::get('/goods/archives', 'GoodsController@archives')
    ->name('goods.archives');

// Основные методы
Route::resource('/goods', 'GoodsController');
Route::post('/goods/search/{text_fragment}', 'GoodsController@search');
// Дублирование
Route::post('/goods/replicate/{id}', 'GoodsController@replicate');
// Архив
Route::any('/goods/archive/{id}', 'GoodsController@archive')
    ->name('goods.archive');


// Отображение на сайте
Route::post('/goods_sync', 'GoodsController@ajax_sync');

Route::any('/goods_check', 'GoodsController@ajax_check');

// Отображение на сайте
Route::any('/goods_categories_get_products', 'GoodsController@ajax_get_products');


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


// ---------------------------------- Услуги -------------------------------------------

Route::get('/services/search/{search}', 'ServiceController@search');
// Основные методы
Route::resource('/services', 'ServiceController');
// Поиск
Route::post('/services/search/{text_fragment}', 'ServiceController@search');
// Архив
Route::post('/services/archive/{id}', 'ServiceController@archive');
// Дублирование
Route::post('/services/replicate/{id}', 'ServiceController@replicate');
// Фотки
Route::any('/service/add_photo', 'ServiceController@add_photo');
Route::post('/service/photos', 'ServiceController@photos');


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
Route::resource('/workflows', 'WorkflowController');
// Поиск
Route::post('/workflows/search/{text_fragment}', 'WorkflowController@search');
// Архив
Route::post('/workflows/archive/{id}', 'WorkflowController@archive');
// Дублирование
Route::post('/workflows/replicate/{id}', 'WorkflowController@replicate');
// Фото
Route::any('/workflow/add_photo', 'WorkflowController@add_photo');
Route::post('/workflow/photos', 'WorkflowController@photos');

Route::any('/workflows_create_mode', 'WorkflowController@ajax_change_create_mode');

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

Route::post('/companies/search-by-name', 'CompanyController@searchByName');
// Проверка существования компании в базе по ИНН
Route::post('/companies/check_company', 'CompanyController@checkcompany')
    ->name('companies.checkcompany');

// Основные методы
Route::resource('/companies', 'CompanyController');


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

// Печать
Route::get('/leads/{id}/print', 'LeadController@print');


// Route::get('/lead/calls', 'LeadController@index')->middleware('auth');
Route::post('/leads', 'LeadController@resetFilter')
    ->name('leads.resetFilter');

Route::any('/leads/axios_update/{id}', 'LeadController@axiosUpdate');

// Основные методы
Route::resource('/leads', 'LeadController')
    ->except([
        'store',
        'show'
    ]);

Route::get('/leads_export', 'LeadController@export');
// Route::resource('/leads_calls', 'LeadController@leads_calls')->middleware('auth');


// Поиск
Route::get('/leads/search/{text}', 'LeadController@search');

// Назначение лида
Route::any('/lead_appointed_check', 'LeadController@ajax_appointed_check');
Route::any('/lead_appointed', 'LeadController@ajax_lead_appointed');
Route::any('/lead_distribute', 'LeadController@ajax_distribute');
Route::any('/lead_take', 'LeadController@ajax_lead_take');

Route::post('/open_change_lead_type', 'LeadController@ajax_open_change_lead_type');
Route::post('/change_lead_type', 'LeadController@ajax_change_lead_type');

// Освобождаем лида
Route::any('/lead_free', 'LeadController@ajax_lead_free');

// Добавление комментария
Route::post('/leads_add_note', 'LeadController@ajax_add_note');
// Поиск лида по номеру телефона
Route::post('/leads/autofind/{phone}', 'LeadController@ajax_autofind_phone');


// --------------------------------------- Расчеты (Сметы) -----------------------------------------------

// Регистрация
Route::patch('/estimates/{id}/registering', 'EstimateController@registering');

// Производство
Route::patch('/estimates/{id}/produce', 'EstimateController@produce');

// Продажа
Route::patch('/estimates/{id}/saling', 'EstimateController@saling');

// Резервирование
Route::post('/estimates/{id}/reserving', 'EstimateController@reserving');
Route::post('/estimates/{id}/unreserving', 'EstimateController@unreserving');

// Основные методы
Route::resource('/estimates', 'EstimateController');

// Отображение на сайте
Route::any('/create_estimate', 'EstimateController@ajax_create');
Route::any('/update_estimate', 'EstimateController@ajax_update');

//Route::any('/estimates_goods_items/{id}', 'EstimatesGoodsItemController@update');

Route::any('/estimates_goods_items/{id}/reserving', 'EstimatesGoodsItemController@reserving');
Route::any('/estimates_goods_items/{id}/unreserving', 'EstimatesGoodsItemController@unreserving');
Route::resource('/estimates_goods_items', 'EstimatesGoodsItemController');
Route::resource('/estimates_services_items', 'EstimatesServicesItemController');

//Route::any('/estimate_items_edit/{id}', 'EstimatesItemController@ajax_edit')->middleware('auth');


// Route::delete('/workflows/{id}', 'EstimateController@ajax_destroy_composition')->middleware('auth');
// Route::any('/estimates_items/add', 'EstimateController@ajax_add')->middleware('auth');

// --------------------------------------- Платежи -----------------------------------------------
// Основные методы
Route::resource('/payments', 'PaymentController');

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


// ---------------------------------------- Источники --------------------------------------------

// Основные методы
Route::resource('/sources', 'SourceController')->middleware('auth');


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
// Архив
Route::post('/suppliers/archive/{id}', 'SupplierController@archive');
// Основные методы
Route::resource('/suppliers', 'SupplierController')
    ->except([
        'show',
        'destroy'
    ]);


// -------------------------------------- Заявки поставщикам ---------------------------------------------

// Основные методы
Route::resource('applications', 'ApplicationController')->middleware('auth');


// -------------------------------------- Товарные накладные ---------------------------------------------


Route::any('/consignments/categories', 'ConsignmentController@categories')->name('consignments.categories');
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
Route::get('/productions/reproduced/{num}', 'ProductionController@reproduced')->name('productions.reproduced');
// Основные методы
Route::resource('/productions', 'ProductionController');

//Route::get('/productions_items', 'ProductionsItemController@store');
Route::resource('/productions_items', 'ProductionsItemController');


// ------------------------------------ Производители ----------------------------------------------------
// Архив
Route::post('/manufacturers/archive/{id}', 'ManufacturerController@archive');
// Основные методы
Route::resource('/manufacturers', 'ManufacturerController')
    ->except([
        'show',
        'destroy'
    ]);


// ------------------------------------ Продавцы ----------------------------------------------------
// Архив
Route::post('/vendors/archive/{id}', 'VendorController@archive');
// Основные методы
Route::resource('/vendors', 'VendorController')
    ->except([
        'show',
        'destroy'
    ]);


// ------------------------------------ Дилеры ----- ----------------------------------------------------
Route::get('/dealers/create-user', 'DealerController@createDealerUser')->middleware('auth')->name('dealers.createDealerUser');
Route::get('/dealers/create-company', 'DealerController@createDealerCompany')->middleware('auth')->name('dealers.createDealerCompany');

Route::post('/dealers/store-user', 'DealerController@storeUser')->middleware('auth')->name('dealers.storeUser');
Route::post('/dealers/store-company', 'DealerController@storeCompany')->middleware('auth')->name('dealers.storeCompany');

Route::patch('/dealers/update-user/{id}', 'DealerController@updateDealerUser')->middleware('auth')->name('dealers.updateDealerUser');
Route::patch('/dealers/update-company/{id}', 'DealerController@updateDealerCompany')->middleware('auth')->name('dealers.updateDealerCompany');

// Основные методы
Route::resource('/dealers', 'DealerController')
    ->middleware('auth');


// ------------------------------------ Клиенты ----------------------------------------------------------
Route::patch('/create_client', 'ClientController@ajax_create');
Route::any('/store_client', 'ClientController@ajax_store');

// Компания (Юр. лицо)
Route::get('/clients/create-company', 'ClientController@createClientCompany')
    ->name('clients.createClientCompany');
Route::post('/clients/store-company', 'ClientController@storeClientCompany')
    ->name('clients.storeClientCompany');
Route::get('/clients/edit-company/{id}', 'ClientController@editClientCompany')
    ->name('clients.editClientCompany');
Route::patch('/clients/update-company/{id}', 'ClientController@updateClientCompany')
    ->name('clients.updateClientCompany');

// Пользователь (Физ. лицо)
Route::get('/clients/create-user', 'ClientController@createClientUser')
    ->name('clients.createClientUser');
Route::post('/clients/store-user', 'ClientController@storeClientUser')
    ->name('clients.storeClientUser');
Route::get('/clients/edit-user/{id}', 'ClientController@editClientUser')
    ->name('clients.editClientUser');
Route::patch('/clients/update-user/{id}', 'ClientController@updateClientUser')
    ->name('clients.updateClientUser');

// Архив
Route::post('/clients/archive/{id}', 'ClientController@archive');

// Поиск
Route::any('/clients/search/{text}', 'ClientController@search');

Route::post('/clients/search-user/{text}', 'ClientController@searchClientUser');
Route::any('/clients/search-company/{text}', 'ClientController@searchClientCompany');

Route::get('/clients/excel-export', 'ClientController@excelExport')
    ->name('clients.excelExport');

// Основные методы
Route::resource('/clients', 'ClientController')
    ->only([
        'index'
    ]);


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
// Архив
Route::post('/positions/archive/{id}', 'PositionController@archive');
// Основные методы
Route::resource('/positions', 'PositionController')
    ->except([
        'show',
        'destroy'
    ]);
// Список отделов филиала и доступных должностей
Route::post('/positions_list', 'PositionController@positions_list');


// -------------------------------------- Штат компании ---------------------------------------------
// Архив
Route::post('/staff/archive/{id}', 'StafferController@archive');
// Основные методы
Route::resource('/staff', 'StafferController')
    ->except([
        'show',
        'destroy'
    ]);


// --------------------------------------- Сотрудники --------------------------------------------
// Уволенные
Route::get('/employees/dismissal', 'EmployeeController@dismissal')
    ->name('employees.dismissal');
// Основные методы
Route::resource('/employees', 'EmployeeController')
    ->except([
        'show',
        'destroy'
    ]);

// Увольнение
Route::post('/employee_dismiss_modal', 'EmployeeController@ajax_employee_dismiss_modal');
Route::post('/employee_dismiss', 'EmployeeController@ajax_employee_dismiss');

// Трудоустройство
Route::post('/employee_employment_modal', 'EmployeeController@ajax_employee_employment_modal');
Route::post('/employee_employment', 'EmployeeController@ajax_employee_employment');


// ------------------------------------------ Списки -----------------------------------------------
Route::resource('/booklists', 'BooklistController')->middleware('auth');

Route::post('/setbooklist', 'BooklistController@setbooklist')->middleware('auth')->name('booklists.setbooklist');
Route::get('/updatebooklist', 'BooklistController@setbooklist')->middleware('auth')->name('booklists.updatebooklist');

Route::any('(:any)', 'SiteController@kek');


// ----------------------------------------- Домены ----------------------------------------------
Route::resource('/domains', 'DomainController');


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


    // --------------------------------------- Пользователи ---------------------------------------------
    Route::resource('/users', 'UserController')
        ->except([
            'show'
        ]);

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

Route::any('/catalog_goods/{id}', 'CatalogsGoodsController@get_catalog');
// Основные методы
Route::resource('/catalogs_goods', 'CatalogsGoodsController');
// Проверка на существование
// Route::post('/catalog_check', 'CatalogController@ajax_check')->middleware('auth');

// -------------------------------- Наполнение каталогов товаров -------------------------------------

Route::prefix('catalogs_goods/{catalog_id}')->group(function () {

    // Текущий добавленный/удаленный пунк меню
    Route::any('/catalogs_goods_items', 'CatalogsGoodsItemController@index');

    // Основные методы
    Route::resource('/catalogs_goods_items', 'CatalogsGoodsItemController');

    Route::delete('/prices_goods/{id}', 'PricesGoodsController@archive');

    Route::post('/get_catalogs_goods_items', 'CatalogsGoodsItemController@ajax_get');

    Route::any('/get_prices_goods/{id}', 'PricesGoodsController@ajax_get');
    Route::any('/edit_prices_goods', 'PricesGoodsController@ajax_edit');
    Route::any('/update_prices_goods', 'PricesGoodsController@ajax_update');
    Route::any('/prices_goods/{id}/archive', 'PricesGoodsController@ajax_archive');

    Route::any('/prices_goods/ajax_store', 'PricesGoodsController@ajax_store');

    Route::any('/prices_goods_sync', 'PricesGoodsController@sync')->name('prices_goods.sync');

    Route::any('/prices_goods_status', 'PricesGoodsController@ajax_status');
    Route::any('/prices_goods_hit', 'PricesGoodsController@ajax_hit');
    Route::any('/prices_goods_new', 'PricesGoodsController@ajax_new');

    Route::resource('/prices_goods', 'PricesGoodsController');
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

    Route::any('prices_services_status', 'PricesServiceController@ajax_status');
    Route::any('prices_services_hit', 'PricesServiceController@ajax_hit');
    Route::any('prices_services_new', 'PricesServiceController@ajax_new');

    Route::resource('prices_services', 'PricesServiceController');
});

// --------------------------- Продвижение -------------------------------------
// Основные методы
Route::resource('/promotions', 'PromotionController')
->except([
    'show'
]);


// --------------------------- Рассылки -------------------------------------
// Основные методы
Route::resource('/dispatches', 'DispatchController');


// --------------------------- Категории выполненных работ -------------------------------------
// Текущая добавленная/удаленная категория
Route::any('/outcomes_categories', 'OutcomesCategoryController@index');
// Основные методы
Route::resource('/outcomes_categories', 'OutcomesCategoryController');


// --------------------------- Выполненные работы -------------------------------------
// Основные методы
Route::resource('/outcomes', 'OutcomeController');


// --------------------------- Портфолио -------------------------------------
// Основные методы
Route::resource('/portfolios', 'PortfolioController');

Route::prefix('/portfolios/{portfolio_id}')->group(function () {

    // --------------------------------------- Разделы портфолио ---------------------------------------------
    // Текущий добавленный/удаленный пункт
    Route::any('/portfolios_items', 'PortfoliosItemController@index');
    // Основные методы
    Route::resource('/portfolios_items', 'PortfoliosItemController');

    // --------------------------------------- Кейсы ---------------------------------------------
    // Основные методы
    Route::resource('/business_cases', 'BusinessCaseController');
});


// --------------------------- Скидки -------------------------------------
// Архив
Route::post('/discounts/archive/{id}', 'DiscountController@archive');
// Основные методы
Route::resource('/discounts', 'DiscountController');

// ---------------------- Показатели клиентской базы -----------------------
Route::post('/clients_indicators/compute/month', 'System\Widgets\ClientsIndicatorController@computeIndicatorsForMonth');
Route::any('/clients_indicators/compute/year', 'System\Widgets\ClientsIndicatorController@computeIndicatorsForYear');


//Route::any('catalogs_services_items/prices', 'CatalogsServicesItemController@get_prices');
//Route::any('catalogs_goods_items/prices', 'CatalogsGoodsItemController@get_prices');


// Route::any('archive_prices_service', 'PricesServiceController@ajax_archive');
// Route::delete('prices_service', 'PricesServiceController@ajax_destroy');


// ------------------------------------- Отображение сессии -----------------------------------------
Route::get('/show_session', 'HelpController@show_session')->middleware('auth')->name('help.show_session');
