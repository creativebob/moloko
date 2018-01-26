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


Route::get('/', function () {
  return view('layouts.enter');
});

Auth::routes();

Route::get('/getaccess', 'GetAccessController@set')->middleware('auth')->name('getaccess.set');

Route::resource('/users', 'UserController')->middleware('auth');

Route::resource('/companies', 'CompanyController')->middleware('auth');

// Маршруты для правил доступа
Route::resource('/rights', 'RightController')->middleware('auth');

// Маршруты для групп доступа
Route::resource('/roles', 'RoleController')->middleware('auth');
// Route::resource('rightrole', 'RightroleController')->middleware('auth');

Route::get('/roles/{id}/setting', 'RoleController@setting')->middleware('auth')->name('roles.setting');
Route::post('/roles/setright', 'RoleController@setright')->middleware('auth')->name('roles.setright');

// Маршрут связи юзера с ролями и отделами
Route::resource('/roleuser', 'RoleUserController')->middleware('auth');
// Маршруты для сущностей
Route::resource('/entities', 'EntityController')->middleware('auth');

// Авторизуемся под выбранной компанией
Route::get('/getauthcompany/{company_id}', 'UserController@getauthcompany')->middleware('auth')->name('users.getauthcompany');

// Авторизуемся под выбранным пользователем
Route::get('/getauthuser/{user_id}', 'UserController@getauthuser')->middleware('auth')->name('users.getauthuser');

// Сбрасываем для бога company_id
Route::get('/getgod', 'UserController@getgod')->middleware('auth')->name('users.getgod');

// Получаем доступ бога
Route::get('/returngod', 'UserController@returngod')->middleware('auth')->name('users.returngod');

// Контроллеры для отображения населенных пунктов, районов и областей
Route::resource('/cities', 'CityController')->middleware('auth');
Route::resource('/areas', 'AreaController')->middleware('auth');
Route::resource('/regions', 'RegionController')->middleware('auth');
// Получаем области и города из vk
Route::post('/city', 'CityController@get_vk_city')->middleware('auth');
Route::post('/region', 'RegionController@get_vk_region')->middleware('auth');
// Текущий добавленный/удаленный город
Route::get('/current_city/{region}/{area}', 'CityController@current_city')->middleware('auth');
// Контроллеры для отображения филиалов, отделов и должностей
Route::resource('/departments', 'DepartmentController')->middleware('auth');
// Текущий добавленный/удаленный отдел
Route::get('/current_department/{parent}/{department}', 'DepartmentController@current_department')->middleware('auth');
// Должности
Route::resource('/positions', 'PositionController')->middleware('auth');
// Контроллер штата компании
Route::resource('/staff', 'StafferController')->middleware('auth');
// Контроллер сотрудников
Route::resource('/employees', 'EmployeeController')->middleware('auth');

// Контроллер отображения сайтов 
Route::resource('/sites', 'SiteController')->middleware('auth');
Route::get('/sites/{site_alias}', 'SiteController@sections')->middleware('auth');


// Route::resource('/menusite', 'MenuSiteController')->middleware('auth');
// Route::resource('/pages', 'PageController')->middleware('auth');



// Навигация и меню
Route::resource('/navigations', 'NavigationController')->middleware('auth');
Route::resource('/menus', 'MenuController')->middleware('auth');
// Текущий добавленный/удаленный пункт меню
Route::get('/current_menu/{section}/{menu}', 'MenuController@current_menu')->middleware('auth');
// Отображение сессии
Route::get('/show_session', 'HelpController@show_session')->middleware('auth')->name('help.show_session');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
