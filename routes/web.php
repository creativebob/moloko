<?php

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

Route::resource('/site_api', 'ApiController');
Route::get('/medcosm', 'ApiController@medcosm');

// Вход в панель управления
Route::get('/', function () {
  return view('layouts.enter');
});

Route::get('/img/{path}', 'ImageController@show')->where('path', '.*');

Route::get('/lol', function () {
  return view('demo');
});

Route::any('getaccess', 'GetAccessController@set')->middleware('auth')->name('getaccess.set');

// Директории
Route::get('directories', 'DirectoryController@index')->middleware('auth')->name('directories.index');

// -------------------------------------- Пользователи ------------------------------------------------
Route::resource('/users', 'UserController')->middleware('auth');
Route::get('/myprofile', 'UserController@myprofile')->middleware('auth')->name('users.myprofile');
Route::patch('/updatemyprofile', 'UserController@updatemyprofile')->middleware('auth')->name('users.updatemyprofile');

// Сортировка пользователей
Route::post('/users_sort', 'UserController@users_sort')->middleware('auth');


// ---------------------------------- Категории альбомов -------------------------------------------
// Текущая добавленная/удаленная категория альбомов
Route::any('/albums_categories', 'AlbumsCategoryController@index')->middleware('auth');
// Основные методы
Route::resource('/albums_categories', 'AlbumsCategoryController')->middleware('auth');
// Проверка на существование категории альбомов
Route::post('/albums_category_check', 'AlbumsCategoryController@albums_category_check')->middleware('auth');
// Select категорий альбомов
Route::post('/albums_categories_list', 'AlbumsCategoryController@albums_categories_list')->middleware('auth');
// Сортировка категорий альбомов
Route::post('/albums_categories_sort', 'AlbumsCategoryController@albums_categories_sort')->middleware('auth');
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
// Сортировка альбомов
Route::post('/albums_sort', 'AlbumController@albums_sort')->middleware('auth');
// Отображение на сайте
Route::post('/albums_display', 'AlbumController@ajax_display')->middleware('auth');
// Проверка на существование
Route::post('/albums_check', 'AlbumController@albums_check')->middleware('auth');

// Route::get('/albums/{alias}', 'AlbumController@sections')->middleware('auth')->name('albums.photos');
// Группа с префиксом
Route::prefix('/albums/{alias}')->group(function () {

  // ----------------------------------- Фотографии -----------------------------------------------
  Route::resource('/photos', 'PhotoController')->middleware('auth');
  // Загрузка фоток через ajax через dropzone.js
});

Route::post('/ajax_get_photo', 'PhotoController@get_photo')->middleware('auth');
Route::patch('/ajax_update_photo/{id}', 'PhotoController@update_photo')->middleware('auth');

// Сортировка фоток
Route::post('/photos_sort', 'PhotoController@photos_sort')->middleware('auth');
// Отображение фоток на сайте
Route::post('/photos_display', 'PhotoController@ajax_display')->middleware('auth');

// ------------------------------------- Продукция -------------------------------------------------

Route::get('/products/{type}/create', 'ProductController@create')->middleware('auth');
Route::get('/products/{id}/edit', 'ProductController@edit')->middleware('auth');
Route::patch('/products/{id}', 'ProductController@update')->middleware('auth');
Route::delete('/products/{id}', 'ProductController@destroy')->middleware('auth');

Route::get('/products/{type}/{status?}', 'ProductController@types')->middleware('auth');
Route::post('/products/', 'ProductController@store')->middleware('auth');
// Основные методы
// Route::resource('/products', 'ProductController')->middleware('auth');
// Добавление фото для продукции
Route::get('/products/{id}/photos', 'ProductController@product_photos')->middleware('auth');
// Запись фото
Route::any('/product/add_photo', 'ProductController@add_photo')->middleware('auth');
Route::post('/product/photos', 'ProductController@photos')->middleware('auth');

// Проверка на существование продукции
Route::post('/product_check', 'ProductController@product_check')->middleware('auth');

// Сортировка продукции
Route::post('/products_sort', 'ProductController@products_sort')->middleware('auth');

// Route for export/download tabledata to .xls or .xlsx
Route::get('/products_download/{type}', 'ProductController@products_download')->middleware('auth');
// Route for import excel data to database.
Route::post('/products_import', 'ProductController@products_import');


Route::any('/ajax_products_count/', 'ProductController@ajax_count')->middleware('auth');
Route::any('/ajax_products_modes/', 'ProductController@ajax_modes')->middleware('auth');

// --------------------------------------- Помещения -----------------------------------------------
Route::resource('places', 'PlaceController')->middleware('auth');


// ------------------------------------- Метрики -------------------------------------------------
// Основные методы
Route::resource('/metrics', 'MetricController')->middleware('auth');

// Пишем метрику через ajax
Route::post('/ajax_store_metric', 'MetricController@ajax_store')->middleware('auth');
// Добавляем / удаляем связь сущности с метрикой
Route::match(['get', 'post'], '/ajax_add_relation_metric', 'MetricController@ajax_add_relation')->middleware('auth');
Route::post('/ajax_delete_relation_metric', 'MetricController@ajax_delete_relation')->middleware('auth');


Route::post('/ajax_add_metric_value', 'MetricController@add_metric_value')->middleware('auth');


// ------------------------------------- Состав -------------------------------------------------
Route::post('/ajax_add_relation_composition', 'CompositionController@ajax_add_relation')->middleware('auth');
Route::post('/ajax_delete_relation_composition', 'CompositionController@ajax_delete_relation')->middleware('auth');

Route::post('/ajax_add_page_composition', 'CompositionController@ajax_add')->middleware('auth');


Route::any('/get_units_list', 'UnitController@get_units_list')->middleware('auth');
Route::post('/ajax_get_article_inputs', 'ArticleController@get_inputs')->middleware('auth');

// -------------------------------------- Артикулы ---------------------------------------------------
Route::get('/articles/{type}/create', 'ArticleController@create')->middleware('auth');
Route::get('/articles/{id}/edit', 'ArticleController@edit')->middleware('auth');
Route::patch('/articles/{id}', 'ArticleController@update')->middleware('auth');
Route::delete('/articles/{id}', 'ArticleController@destroy')->middleware('auth');

Route::get('/articles/{type}/{status?}', 'ArticleController@types')->middleware('auth');
Route::post('/articles/', 'ArticleController@store')->middleware('auth');
// Основые методы
Route::resource('/articles', 'ArticleController')->middleware('auth');
// Отображение страниц на сайте
Route::post('/articles_display', 'ArticleController@ajax_display')->middleware('auth');

// Запись фото
Route::any('/article/add_photo', 'ArticleController@add_photo')->middleware('auth');
Route::post('/article/photos', 'ArticleController@photos')->middleware('auth');


// ------------------------------------ Категории продукции --------------------------------------


// -------------------------------- Категории товаров -----------------------------------------
// Текущая добавленная/удаленная категория
Route::any('/goods_categories', 'GoodsCategoryController@index')->middleware('auth');
// Основные методы
Route::resource('/goods_categories', 'GoodsCategoryController')->middleware('auth');

// -------------------------------- Категории сырья -------------------------------------------
// Текущая добавленная/удаленная категория
Route::any('/raws_categories', 'RawsCategoryController@index')->middleware('auth');
// Основные методы
Route::resource('/raws_categories', 'RawsCategoryController')->middleware('auth');



// -------------------------------- Категории услуг -------------------------------------------
// Route::get('/services_categories/create', 'ServicesCategoryController@create')->middleware('auth');

// Текущая добавленная/удаленная категория
Route::any('/services_categories', 'ServicesCategoryController@index')->middleware('auth');

// Основные методы
Route::resource('/services_categories', 'ServicesCategoryController')->middleware('auth');

// Проверка на существование категории продукции
Route::post('/services_category_check', 'ServicesCategoryController@services_category_check')->middleware('auth');

// Отображение страниц на сайте
Route::post('/services_categories_display', 'ServicesCategoryController@ajax_display')->middleware('auth');
// Сортировка
Route::post('/services_categories_sort', 'ServicesCategoryController@services_categories_sort')->middleware('auth');

// --------------------------------- Продукция услуг --------------------------------------------
// Основные методы
Route::resource('/services_products', 'ServicesProductController')->middleware('auth');


Route::any('/ajax_services_count', 'ServicesProductController@ajax_count')->middleware('auth');
Route::any('/ajax_services_modes', 'ServicesProductController@ajax_modes')->middleware('auth');

// ---------------------------------- Услуги (Артикулы) -------------------------------------------
Route::any('/services/create', 'ServiceController@create')->middleware('auth');
// Основные методы
Route::resource('/services', 'ServiceController')->middleware('auth');

Route::any('/service/add_photo', 'ServiceController@add_photo')->middleware('auth');
Route::post('/service/photos', 'ServiceController@photos')->middleware('auth');

// Архивация
Route::post('/services/archive/{id}', 'ServiceController@archive')->middleware('auth');

// Сортировка
Route::post('/services_sort', 'ServiceController@services_sort')->middleware('auth');

// Отображение страниц на сайте
Route::post('/services_display', 'ServiceController@ajax_display')->middleware('auth');



// -------------------------------- Категории товаров -------------------------------------------
// Route::any('/goods_categories/create', 'GoodsCategoryController@create')->middleware('auth');
// Текущая добавленная/удаленная категория
Route::any('/goods_categories', 'GoodsCategoryController@index')->middleware('auth');

Route::match(['get', 'post'], '/goods_categories/{id}/edit', 'GoodsCategoryController@edit')->middleware('auth');
// Основные методы
Route::resource('/goods_categories', 'GoodsCategoryController')->middleware('auth');

// Проверка на существование категории продукции
Route::post('/goods_category_check', 'GoodsCategoryController@goods_category_check')->middleware('auth');

// Отображение страниц на сайте
Route::post('/goods_categories_display', 'GoodsCategoryController@ajax_display')->middleware('auth');
// Сортировка
Route::post('/goods_categories_sort', 'GoodsCategoryController@goods_categories_sort')->middleware('auth');

// --------------------------------- Продукция товаров --------------------------------------------
// Основные методы
Route::resource('/goods_products', 'GoodsProductController')->middleware('auth');


Route::any('/ajax_goods_count', 'GoodsProductController@ajax_count')->middleware('auth');
Route::any('/ajax_goods_modes', 'GoodsProductController@ajax_modes')->middleware('auth');

// ---------------------------------- Товары (Артикулы) -------------------------------------------
Route::any('/goods/create', 'GoodsController@create')->middleware('auth');
// Основные методы
Route::resource('/goods', 'GoodsController')->middleware('auth');

Route::any('/cur_good/add_photo', 'GoodsController@add_photo')->middleware('auth');
Route::post('/cur_good/photos', 'GoodsController@photos')->middleware('auth');

// Архивация
Route::post('/goods/archive/{id}', 'GoodsController@archive')->middleware('auth');

// Сортировка
Route::post('/goods_sort', 'GoodsController@goods_sort')->middleware('auth');

// Отображение страниц на сайте
Route::post('/goods_display', 'GoodsController@ajax_display')->middleware('auth');


// Проверка на существование товара
Route::post('/sector_check', 'SectorController@sector_check')->middleware('auth');
// Select секторов
Route::post('/sectors_list', 'SectorController@sectors_list')->middleware('auth');
// Сортировка секторов
Route::post('/sectors_sort', 'SectorController@sectors_sort')->middleware('auth');

Route::any('/products_categories_ajax', 'ProductsCategoryController@index')->middleware('auth');

Route::get('/products_categories/{type}/create', 'ProductsCategoryController@create')->middleware('auth');

Route::match(['get', 'post'], '/products_categories/{id}/edit', 'ProductsCategoryController@edit')->middleware('auth');

Route::patch('/products_categories/{id}', 'ProductsCategoryController@update')->middleware('auth');

Route::delete('/products_categories/{id}', 'ProductsCategoryController@destroy')->middleware('auth');

Route::post('/products_categories/', 'ProductsCategoryController@store')->middleware('auth');

Route::get('/products_categories/{type}/{status?}', 'ProductsCategoryController@types')->middleware('auth');



// Route::any('/products_categories/{type}/', 'ProductsCategoryController@index')->middleware('auth');

// Метод для обновления фотографии, ajax не поддерживает PATCH
// Route::post('/products_categories/{id}/update', 'ProductsCategoryController@ajax_update');
// Текущая добавленная/удаленная категория продукции
// Route::any('/products_categories', 'ProductsCategoryController@index')->middleware('auth');
// Основые методы
// Route::resource('/products_categories', 'ProductsCategoryController')->middleware('auth');
// Проверка на существование категории продукции
Route::post('/products_category_check', 'ProductsCategoryController@products_category_check')->middleware('auth');
// Select категорий продукции
Route::post('/products_categories_list', 'ProductsCategoryController@products_categories_list')->middleware('auth');
// Сортировка категорий продукции
Route::post('/products_categories_sort', 'ProductsCategoryController@products_categories_sort')->middleware('auth');
// Отображение страниц на сайте
Route::post('/products_categories_display', 'ProductsCategoryController@ajax_display')->middleware('auth');

// Метод для обновления фотографии, ajax не поддерживает PATCH
// Route::post('/products_categories/{id}/update', 'ProductsCategoryController@ajax_update');
// // Текущая добавленная/удаленная категория продукции
// Route::any('/products_categories', 'ProductsCategoryController@index')->middleware('auth');
// // Основые методы
// Route::resource('/products_categories', 'ProductsCategoryController')->middleware('auth');
// // Проверка на существование категории продукции
// Route::post('/products_category_check', 'ProductsCategoryController@products_category_check')->middleware('auth');
// // Select категорий продукции
// Route::post('/products_categories_list', 'ProductsCategoryController@products_categories_list')->middleware('auth');
// // Сортировка категорий продукции
// Route::post('/products_categories_sort', 'ProductsCategoryController@products_categories_sort')->middleware('auth');



// --------------------------------------- Свойства -----------------------------------------------
Route::post('/ajax_add_property', 'PropertyController@add_property')->middleware('auth');

// Route::any('/get_properties_with_metrics', 'PropertyController@get_properties_with_metrics')->middleware('auth');

// --------------------------------------- Метрики -----------------------------------------------
Route::resource('/metrics', 'MetricController')->middleware('auth');


// --------------------------------------- Компании -----------------------------------------------
Route::resource('companies', 'CompanyController')->middleware('auth');
// Проверка существования компании в базе по ИНН
Route::post('companies/check_company', 'CompanyController@checkcompany')->middleware('auth')->name('companies.checkcompany');
// Сортировка компаний
Route::post('companies_sort', 'CompanyController@companies_sort')->middleware('auth');

// Маршруты для правил доступа
Route::resource('/rights', 'RightController')->middleware('auth');

// Маршруты для групп доступа
Route::resource('/roles', 'RoleController')->middleware('auth');
// Route::resource('rightrole', 'RightroleController')->middleware('auth');

Route::get('/roles/{id}/setting', 'RoleController@setting')->middleware('auth')->name('roles.setting');
Route::post('/roles/setright', 'RoleController@setright')->middleware('auth')->name('roles.setright');

// Получение роли дял пользоователя
Route::any('/get_role', 'RoleController@get_role')->middleware('auth');

// Маршрут связи юзера с ролями и отделами
Route::resource('/roleuser', 'RoleUserController')->middleware('auth');

// Маршруты для сущностей
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
Route::resource('/regions', 'RegionController')->middleware('auth');
// Сортировка областей
Route::post('/regions_sort', 'RegionController@regions_sort')->middleware('auth');
// Получаем области из vk
Route::post('/region', 'RegionController@get_vk_region')->middleware('auth');

// ---------------------------------------- Районы --------------------------------------------------
Route::resource('/areas', 'AreaController')->middleware('auth');
// Сортировка районов
Route::post('/areas_sort', 'AreaController@areas_sort')->middleware('auth');

// ----------------------------------- Населенные пункты -------------------------------------------
// Текущий добавленный/удаленный город
Route::any('/cities', 'CityController@index')->middleware('auth');
// Основные методы
Route::resource('/cities', 'CityController')->middleware('auth');
// Проверка на существование города
Route::post('/city_check', 'CityController@city_check')->middleware('auth');
// Сортировка населенных пунктов
Route::post('/cities_sort', 'CityController@cities_sort')->middleware('auth');
// Таблица городов
Route::post('/cities_list', 'CityController@cities_list')->middleware('auth');
// Получаем города из vk
Route::post('/city_vk', 'CityController@get_vk_city')->middleware('auth');

// Тестовый маршрут проверки пришедших с вк данных
Route::get('/city_vk/{city}', 'CityController@get_vk_city')->middleware('auth');

// Отображение на сайте
Route::post('/cities_display', 'CityController@ajax_display')->middleware('auth');
Route::post('/areas_display', 'AreaController@ajax_display')->middleware('auth');
Route::post('/regions_display', 'RegionController@ajax_display')->middleware('auth');

// ----------------------------------------- Филиалы и отделы --------------------------------------
// Текущий добавленный/удаленный отдел/филиал
Route::any('/departments', 'DepartmentController@index')->middleware('auth');
// Основные методы
Route::resource('/departments', 'DepartmentController')->middleware('auth');
// Текущий добавленный/удаленный отдел
Route::get('/current_department/{section_id}/{item_id}', 'DepartmentController@current_department')->middleware('auth');
// Проверка на существование филиала/отдела
Route::post('/department_check', 'DepartmentController@department_check')->middleware('auth');
// Список отделов филиала
Route::post('/departments_list', 'DepartmentController@departments_list')->middleware('auth');
// Сортировка отделов
Route::post('/departments_sort', 'DepartmentController@departments_sort')->middleware('auth');
// Отображение страниц на сайте
Route::post('/departments_display', 'DepartmentController@ajax_display')->middleware('auth');

// ----------------------------------------- Должности --------------------------------------------
Route::resource('/positions', 'PositionController')->middleware('auth');
// Список отделов филиала и доступных должностей
Route::post('/positions_list', 'PositionController@positions_list')->middleware('auth');
// Сортировка должностей
Route::post('/positions_sort', 'PositionController@positions_sort')->middleware('auth');

// -------------------------------------- Штат компании ---------------------------------------------
Route::resource('/staff', 'StafferController')->middleware('auth');
// Отображение страниц на сайте
Route::post('/staff_display', 'StafferController@ajax_display')->middleware('auth');
// Сортировка штата
Route::post('/staff_sort', 'StafferController@staff_sort')->middleware('auth');

// --------------------------------------- Сотрудники ---------------------------------------------
Route::resource('/employees', 'EmployeeController')->middleware('auth');
// Сортировка сотрудников
Route::post('/employees_sort', 'EmployeeController@employees_sort')->middleware('auth');

// ----------------------------------------- Секторы -----------------------------------------------
// Текущий добавленный/удаленный сектор
Route::any('/sectors', 'SectorController@index')->middleware('auth');
// Основные методы
Route::resource('/sectors', 'SectorController')->middleware('auth');
// Проверка на существование сектора
Route::post('/sector_check', 'SectorController@sector_check')->middleware('auth');
// Select секторов
Route::post('/sectors_list', 'SectorController@sectors_list')->middleware('auth');
// Сортировка секторов
Route::post('/sectors_sort', 'SectorController@sectors_sort')->middleware('auth');

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
Route::post('/site_check', 'SiteController@site_check')->middleware('auth');
// Сортировка сайтов
Route::post('/sites_sort', 'SiteController@sites_sort')->middleware('auth');

// Разделы сайта
Route::prefix('/sites/{alias}')->group(function () {

	// --------------------------------------- Страницы ---------------------------------------------
  Route::resource('/pages', 'PageController')->middleware('auth');
  // Проверка на существование страницы
  Route::post('/page_check', 'PageController@page_check')->middleware('auth');

  // --------------------------------------- Навигации --------------------------------------------
  // Текущая добавленная/удаленная навигация
  Route::any('/navigations', 'NavigationController@index')->middleware('auth');
  // Основные методы
  Route::resource('/navigations', 'NavigationController')->middleware('auth');
	// Проверка на существование навигации
  Route::post('/navigation_check', 'NavigationController@navigation_check')->middleware('auth');

// Route::any('/menus/create', 'MenuController@create')->middleware('auth');
  // -------------------------------------------Меню ---------------------------------------------
  Route::resource('/menus', 'MenuController')->middleware('auth');

  // ---------------------------------------- Новости --------------------------------------------
  Route::resource('/news', 'NewsController')->middleware('auth');
  // Проверка на существование новости
  Route::post('/news_check', 'NewsController@news_check')->middleware('auth');
});


// Сортировка навигаций
Route::post('/navigations_sort', 'NavigationController@navigations_sort')->middleware('auth');
// Сортировка меню
Route::post('/menus_sort', 'MenuController@menus_sort')->middleware('auth');
// Сортировка новостей
Route::post('/news_sort', 'NewsController@news_sort')->middleware('auth');


// Отображение на сайте
Route::post('/pages_display', 'PageController@ajax_display')->middleware('auth');
Route::post('/news_display', 'NewsController@ajax_display')->middleware('auth');
Route::post('/navigations_display', 'NavigationController@ajax_display')->middleware('auth');
Route::post('/menus_display', 'MenuController@ajax_display')->middleware('auth');



// ------------------------------------- Отображение сессии -----------------------------------------
Route::get('/show_session', 'HelpController@show_session')->middleware('auth')->name('help.show_session');

Route::get('/home', 'HomeController@index')->name('home');


