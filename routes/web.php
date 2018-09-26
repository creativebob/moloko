<?php

use App\Lead;
use App\User;

use Carbon\Carbon;
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

// Всякая хрень дял проверки
Route::resource('/site_api', 'ApiController');
Route::get('/medcosm', 'ApiController@medcosm');
Route::get('/dashboard', 'HomeController@index')->name('home');
Route::get('/img/{path}', 'ImageController@show')->where('path', '.*');
// Route::get('/lol', function () {
// 	return view('demo');
// });

Route::get('/home', 'HomeController@index')->name('home');

// Вход в панель управления
Route::get('/', function () {
	return view('layouts.enter');
});

Route::any('getaccess', 'GetAccessController@set')->middleware('auth')->name('getaccess.set');

// Директории
Route::get('directories', 'DirectoryController@index')->middleware('auth')->name('directories.index');

// Методы дял парсера и одноразовые

// Route::any('/lol', function () {
    
//     dd(App\OldLead::with(['comments.user', 'claims', 'task', 'stage', 'user', 'city', 'service', 'challenges' => function ($query) {
//     	$query->with('author', 'appointed', 'finisher', 'stage', 'task');
//     }])->find(5468));
// });

Route::get('/lol', function() {
	// $leads = App\Lead::whereMonth('created_at', Carbon\Carbon::now()->format('m'))->whereYear('created_at', Carbon\Carbon::now()->format('Y'))->whereNull('draft')->get();
	$leads = Lead::whereDate('created_at', Carbon::now()->format('Y-m-d'))->whereNull('draft')->get();

            $telegram_message = "Отчет за день (".Carbon::now()->format('d.m.Y')."): \r\nЗвонков: ".count($leads->where('lead_type_id', 1))."\r\Заявок с сайта: ".count($leads->where('lead_type_id', 2));
            
            $telegram_destinations = User::whereHas('staff', function ($query) {
                $query->whereHas('position', function ($query) {
                    $query->whereHas('notifications', function ($query) {
                        $query->where('notification_id', 3);
                    });
                });
            })
            ->where('telegram_id', '!=', null)
            ->get(['telegram_id']);

            send_message($telegram_destinations, $telegram_message);
})->middleware('auth');
// Route::get('/dublicator', 'ParserController@dublicator')->middleware('auth');

// Route::get('/dublicator_old', 'ParserController@dublicator_old')->middleware('auth');

// Route::get('/adder', 'ParserController@adder')->middleware('auth');

// Route::get('/parser', 'ParserController@index')->middleware('auth');

// Route::get('/andrey', 'ParserController@andrey')->middleware('auth');

Route::get('/old_claims', 'ParserController@old_claims')->middleware('auth');

Route::get('/phone_parser', 'ParserController@phone_parser')->middleware('auth');


// --------------------------------------- Настройки -----------------------------------------------

Route::any('/set_setting', 'SettingController@ajax_set_setting')->middleware('auth');

Route::resource('/settings', 'SettingController')->middleware('auth');

// ---------------------------------------- Телефоны --------------------------------------------------

Route::post('/add_extra_phone', 'PhoneController@ajax_add_extra_phone')->middleware('auth');


// -------------------------------------- Пользователи ------------------------------------------------

Route::resource('/users', 'UserController')->middleware('auth');
Route::get('/myprofile', 'UserController@myprofile')->middleware('auth')->name('users.myprofile');
Route::patch('/updatemyprofile', 'UserController@updatemyprofile')->middleware('auth')->name('users.updatemyprofile');

// Сортировка
Route::post('/users_sort', 'UserController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/users_system_item', 'UserController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/users_display', 'UserController@ajax_display')->middleware('auth');



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
// Сортировка
Route::post('/albums_categories_sort', 'AlbumsCategoryController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/albums_categories_system_item', 'AlbumsCategoryController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/albums_categories_display', 'AlbumsCategoryController@ajax_display')->middleware('auth');


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
// Сортировка
Route::post('/albums_sort', 'AlbumController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/albums_system_item', 'AlbumController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/albums_display', 'AlbumController@ajax_display')->middleware('auth');

// Группа с префиксом
Route::prefix('/albums/{alias}')->group(function () {


  // ----------------------------------- Фотографии -----------------------------------------------

	Route::resource('/photos', 'PhotoController')->middleware('auth');
  // Загрузка фоток через ajax через dropzone.js
});

Route::post('/ajax_get_photo', 'PhotoController@get_photo')->middleware('auth');
Route::patch('/ajax_update_photo/{id}', 'PhotoController@update_photo')->middleware('auth');

// Сортировка
Route::post('/photos_sort', 'PhotoController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/photos_system_item', 'PhotoController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/photos_display', 'PhotoController@ajax_display')->middleware('auth');


// --------------------------------------- Помещения -----------------------------------------------

Route::resource('/places', 'PlaceController')->middleware('auth');
// Сортировка
Route::post('/places_sort', 'PlaceController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/places_system_item', 'PlaceController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/places_display', 'PlaceController@ajax_display')->middleware('auth');


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
// Сортировка
Route::post('/raws_categories_sort', 'RawsCategoryController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/raws_categories_system_item', 'RawsCategoryController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/raws_categories_display', 'RawsCategoryController@ajax_display')->middleware('auth');


// --------------------------------- Продукция сырья --------------------------------------------

// Основные методы
Route::resource('/raws_products', 'RawsProductController')->middleware('auth');
// Сортировка
Route::post('/raws_products_sort', 'RawsProductController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/raws_products_system_item', 'RawsProductController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/raws_products_display', 'RawsProductController@ajax_display')->middleware('auth');

Route::any('/ajax_raws_count', 'RawsProductController@ajax_count')->middleware('auth');
Route::any('/ajax_raws_modes', 'RawsProductController@ajax_modes')->middleware('auth');


// ---------------------------------- Сырьё (Артикулы) -------------------------------------------

// Основные методы
Route::resource('/raws', 'RawController')->middleware('auth');
// Route::get('/raws/search/{text_fragment}', 'RawController@search')->middleware('auth');
Route::post('/raws/search/{text_fragment}', 'RawController@search')->middleware('auth');
// Сортировка
Route::post('/raws_sort', 'RawController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/raws_system_item', 'RawController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/raws_display', 'RawController@ajax_display')->middleware('auth');
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
// Сортировка
Route::post('/goods_categories_sort', 'GoodsCategoryController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/goods_categories_system_item', 'GoodsCategoryController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/goods_categories_display', 'GoodsCategoryController@ajax_display')->middleware('auth');


// --------------------------------- Группы товаров --------------------------------------------

// Основные методы
Route::resource('/goods_products', 'GoodsProductController')->middleware('auth');
// Сортировка
Route::post('/goods_products_sort', 'GoodsProductController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/goods_products_system_item', 'GoodsProductController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/goods_products_display', 'GoodsProductController@ajax_display')->middleware('auth');

Route::any('/ajax_goods_count', 'GoodsProductController@ajax_count')->middleware('auth');
Route::any('/ajax_goods_modes', 'GoodsProductController@ajax_modes')->middleware('auth');


// ---------------------------------- Товары (Артикулы) -------------------------------------------
Route::any('/goods/create', 'GoodsController@create')->middleware('auth');

// Основные методы
Route::resource('/goods', 'GoodsController')->middleware('auth');
Route::post('/goods/search/{text_fragment}', 'GoodsController@search')->middleware('auth');
// Архивация
Route::post('/goods/archive/{id}', 'GoodsController@archive')->middleware('auth');
// Сортировка
Route::post('/goods_sort', 'GoodsController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/goods_system_item', 'GoodsController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/goods_display', 'GoodsController@ajax_display')->middleware('auth');

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
// Сортировка
Route::post('/services_categories_sort', 'ServicesCategoryController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/services_categories_system_item', 'ServicesCategoryController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/services_categories_display', 'ServicesCategoryController@ajax_display')->middleware('auth');


// --------------------------------- Продукция услуг --------------------------------------------

// Основные методы
Route::resource('/services_products', 'ServicesProductController')->middleware('auth');
// Сортировка
Route::post('/services_products_sort', 'ServicesProductController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/services_products_system_item', 'ServicesProductController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/services_products_display', 'ServicesProductController@ajax_display')->middleware('auth');

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
// Сортировка
Route::post('/services_sort', 'ServiceController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/services_system_item', 'ServiceController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/services_display', 'ServiceController@ajax_display')->middleware('auth');
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
// Сортировка
Route::post('/sectors_sort', 'SectorController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/sectors_system_item', 'SectorController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/sectors_display', 'SectorController@ajax_display')->middleware('auth');


// --------------------------------------- Компании -----------------------------------------------

// Основные методы
Route::resource('/companies', 'CompanyController')->middleware('auth');
// Проверка существования компании в базе по ИНН
Route::post('/companies/check_company', 'CompanyController@checkcompany')->middleware('auth')->name('companies.checkcompany');
// Сортировка
Route::post('/companies_sort', 'CompanyController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/companies_system_item', 'CompanyController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/companies_display', 'CompanyController@ajax_display')->middleware('auth');


// --------------------------------------- Лиды -----------------------------------------------

// Основные методы
// Route::get('/lead/calls', 'LeadController@index')->middleware('auth');
Route::resource('/leads', 'LeadController')->middleware('auth');
// Route::resource('/leads_calls', 'LeadController@leads_calls')->middleware('auth');
// Сортировка
Route::post('/leads_sort', 'LeadController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/leads_system_item', 'LeadController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/leads_display', 'LeadController@ajax_display')->middleware('auth');

// Поиск
Route::post('/leads/search', 'LeadController@search')->middleware('auth');

// Назначение лида
Route::any('/lead_direction_check', 'LeadController@ajax_lead_direction_check')->middleware('auth');
Route::any('/lead_appointed', 'LeadController@ajax_lead_appointed')->middleware('auth');
Route::any('/lead_distribute', 'LeadController@ajax_distribute')->middleware('auth');
Route::any('/lead_take', 'LeadController@ajax_lead_take')->middleware('auth');

// Освобождаем лида
Route::post('/lead_free', 'LeadController@ajax_lead_free')->middleware('auth');

// Добавление комментария
Route::post('/leads_add_note', 'LeadController@ajax_add_note')->middleware('auth');
// Поиск лида по номеру телефона
Route::post('/leads/autofind/{phone}', 'LeadController@ajax_autofind_phone')->middleware('auth');

// ------------------------------ Внутренние комментарии -----------------------------------------------

// Основные методы
Route::resource('/notes', 'NoteController')->middleware('auth');


// --------------------------------------- Задачи -----------------------------------------------

// Route::any('/challenges/{id}', 'ChallengeController@update')->middleware('auth');
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
// Сортировка
Route::post('/stages_sort', 'StageController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/stages_system_item', 'StageController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/stages_display', 'StageController@ajax_display')->middleware('auth');



	// ---------------------------------------- Посты --------------------------------------------

	// Основные методы
Route::resource('/posts', 'PostController')->middleware('auth');
// Сортировка
Route::post('/posts_sort', 'PostController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/posts_system_item', 'PostController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/posts_display', 'PostController@ajax_display')->middleware('auth');

	// ---------------------------------------- Аккаунты --------------------------------------------

	// Основные методы
Route::resource('/accounts', 'AccountController')->middleware('auth');
	// Проверка на существование аккаунта
Route::post('/accounts_check', 'AccountController@ajax_check')->middleware('auth');
	

// --------------------------------------- Рекламные кампании -----------------------------------------------

// Основные методы
Route::resource('/campaigns', 'CampaignController')->middleware('auth');
// Сортировка
Route::post('/campaigns_sort', 'CampaignController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/campaigns_system_item', 'CampaignController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/campaigns_display', 'CampaignController@ajax_display')->middleware('auth');


// --------------------------------------- Расходы -----------------------------------------------

// Основные методы
Route::resource('/expenses', 'ExpenseController')->middleware('auth');
// Сортировка
Route::post('/expenses_sort', 'ExpenseController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/expenses_system_item', 'ExpenseController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/expenses_display', 'ExpenseController@ajax_display')->middleware('auth');


// --------------------------------------- Зарплаты -----------------------------------------------

// Основные методы
Route::resource('/salaries', 'SalaryController')->middleware('auth');
// Сортировка
Route::post('/salaries_sort', 'SalaryController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/salaries_system_item', 'SalaryController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/salaries_display', 'SalaryController@ajax_display')->middleware('auth');


// --------------------------------------- Социальные сети -----------------------------------------------

// Основные методы
Route::resource('social_networks', 'SocialNetworkController')->middleware('auth');
// Сортировка
Route::post('/social_networks_sort', 'SocialNetworkController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/social_networks_system_item', 'SocialNetworkController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/social_networks_display', 'SocialNetworkController@ajax_display')->middleware('auth');


// -------------------------------------- Поставщики -----------------------------------------------

// Основные методы
Route::resource('/suppliers', 'SupplierController')->middleware('auth');
// Сортировка
Route::post('/suppliers_sort', 'SupplierController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/suppliers_system_item', 'SupplierController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/suppliers_display', 'SupplierController@ajax_display')->middleware('auth');


// ------------------------------------ Производители ----------------------------------------------------

// Основные методы
Route::resource('/manufacturers', 'ManufacturerController')->middleware('auth');
// Сортировка
Route::post('/manufacturers_sort', 'ManufacturerController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/manufacturers_system_item', 'ManufacturerController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/manufacturers_display', 'ManufacturerController@ajax_display')->middleware('auth');


// ------------------------------------- Правила доступа ----------------------------------------------------

// Основные методы
Route::resource('/rights', 'RightController')->middleware('auth');


//-------------------------------------- Группы доступа -----------------------------------------------------

// Основные методы
Route::resource('/roles', 'RoleController')->middleware('auth');
// Route::resource('rightrole', 'RightroleController')->middleware('auth');
// Сортировка
Route::post('/roles_sort', 'RoleController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/roles_system_item', 'RoleController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/roles_display', 'RoleController@ajax_display')->middleware('auth');

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
// Сортировка областей
Route::post('/regions_sort', 'RegionController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/regions_system_item', 'RegionController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/regions_display', 'RegionController@ajax_display')->middleware('auth');
// Получаем области из vk
Route::post('/region', 'RegionController@get_vk_region')->middleware('auth');


// ---------------------------------------- Районы --------------------------------------------------

// Основные методы
Route::resource('/areas', 'AreaController')->middleware('auth');
// Сортировка районов
Route::post('/areas_sort', 'AreaController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/areas_system_item', 'AreaController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/areas_display', 'AreaController@ajax_display')->middleware('auth');


// ----------------------------------- Населенные пункты -------------------------------------------

// Текущий добавленный/удаленный город
Route::any('/cities', 'CityController@index')->middleware('auth');
// Основные методы
Route::resource('/cities', 'CityController')->middleware('auth');
// Проверка на существование города
Route::post('/city_check', 'CityController@ajax_check')->middleware('auth');
// Сортировка
Route::post('/cities_sort', 'CityController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/cities_system_item', 'CityController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/cities_display', 'CityController@ajax_display')->middleware('auth');
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
// Сортировка
Route::post('/departments_sort', 'DepartmentController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/departments_system_item', 'DepartmentController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/departments_display', 'DepartmentController@ajax_display')->middleware('auth');


// ----------------------------------------- Должности --------------------------------------------

// Основные методы
Route::resource('/positions', 'PositionController')->middleware('auth');
// Список отделов филиала и доступных должностей
Route::post('/positions_list', 'PositionController@positions_list')->middleware('auth');
// Сортировка
Route::post('/positions_sort', 'PositionController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/positions_system_item', 'PositionController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/positions_display', 'PositionController@ajax_display')->middleware('auth');


// -------------------------------------- Штат компании ---------------------------------------------

// Основные методы
Route::resource('/staff', 'StafferController')->middleware('auth');
// Сортировка
Route::post('/staff_sort', 'StafferController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/staff_system_item', 'StafferController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/staff_display', 'StafferController@ajax_display')->middleware('auth');


// --------------------------------------- Сотрудники ---------------------------------------------

// Основные методы
Route::resource('/employees', 'EmployeeController')->middleware('auth');
// Сортировка
Route::post('/employees_sort', 'EmployeeController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/employees_system_item', 'EmployeeController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/employees_display', 'EmployeeController@ajax_display')->middleware('auth');


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
// Сортировка
Route::post('/sites_sort', 'SiteController@ajax_sort')->middleware('auth');
// Системная запись
Route::post('/sites_system_item', 'SiteController@ajax_system_item')->middleware('auth');
// Отображение на сайте
Route::post('/sites_display', 'SiteController@ajax_display')->middleware('auth');

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


// Сортировка
Route::post('/pages_sort', 'PageController@ajax_sort')->middleware('auth');
Route::post('/navigations_sort', 'NavigationController@ajax_sort')->middleware('auth');
Route::post('/menus_sort', 'MenuController@ajax_sort')->middleware('auth');
Route::post('/news_sort', 'NewsController@ajax_sort')->middleware('auth');
Route::post('/catalogs_sort', 'CatalogController@ajax_sort')->middleware('auth');
Route::post('/catalog_products_sort', 'CatalogProductController@ajax_sort')->middleware('auth');


// Системная запись
Route::post('/pages_system_item', 'PageController@ajax_system_item')->middleware('auth');
Route::post('/news_system_item', 'NewsController@ajax_system_item')->middleware('auth');
Route::post('/navigations_system_item', 'NavigationController@ajax_system_item')->middleware('auth');
Route::post('/menus_system_item', 'MenuController@ajax_system_item')->middleware('auth');
Route::post('/catalogs_system_item', 'CatalogController@ajax_system_item')->middleware('auth');
Route::post('/catalog_products_system_item', 'CatalogProductController@ajax_system_item')->middleware('auth');


// Отображение на сайте
Route::post('/pages_display', 'PageController@ajax_display')->middleware('auth');
Route::post('/news_display', 'NewsController@ajax_display')->middleware('auth');
Route::post('/navigations_display', 'NavigationController@ajax_display')->middleware('auth');
Route::post('/menus_display', 'MenuController@ajax_display')->middleware('auth');
Route::post('/catalogs_display', 'CatalogController@ajax_display')->middleware('auth');
Route::post('/catalog_products_display', 'CatalogProductController@ajax_display')->middleware('auth');


// ------------------------------------- Отображение сессии -----------------------------------------
Route::get('/show_session', 'HelpController@show_session')->middleware('auth')->name('help.show_session');