<?php

namespace App\Http\Controllers;

// Модели для текущей работы
use App\User;
use App\Company;
use App\Page;
use App\Sector;
use App\Folder;
use App\Booklist;
use App\List_item;
use App\Schedule;
use App\Worktime;
use App\Location;
use App\ScheduleEntity;

// Модели которые отвечают за работу с правами + политики
use App\Policies\CompanyPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequest;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{

  // Сущность над которой производит операции контроллер
  protected $entity_name = 'companies';
  protected $entity_dependence = false;

  public function index(Request $request)
  {

        // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем авторизованного пользователя
    $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------------------------

    $companies = Company::with('author', 'director', 'location.city', 'sector')
    ->moderatorLimit($answer)
        // ->cityFilter($request)
    ->sectorFilter($request)
    ->booklistFilter($request)
    ->orderBy('moderation', 'desc')
    ->paginate(30);

        // dd($companies);

    $filter_query = Company::with('location.city', 'sector')->moderatorLimit($answer)->get();
        // $filter_query = $filter_cities->location;

        // dd($filter_cities);
    $filter['status'] = null;

    $filter = addCityFilter($filter, $filter_query, $request, 'Выберите город:', 'city', 'city_id');
    $filter = addFilter($filter, $filter_query, $request, 'Выберите сектор:', 'sector', 'sector_id');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
    $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);
        // dd($filter);
        // Инфо о странице
    $page_info = pageInfo($this->entity_name);

        // dd($filter);

    return view('companies.index', compact('companies', 'page_info', 'filter', 'user'));
  }

  public function create(Request $request)
  {

        //Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Подключение политики
    $company = new Company;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right('sectors', false, 'index');

        // Главный запрос
    $sectors = Sector::moderatorLimit($answer)
    ->orderBy('sort', 'asc')
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

        // Формируем дерево вложенности
    $sectors_cat = [];
    foreach ($sectors as $id => &$node) { 

          // Если нет вложений
      if (!$node['parent_id']) {
        $sectors_cat[$id] = &$node;
      } else { 

          // Если есть потомки то перебераем массив
        $sectors[$node['parent_id']]['children'][$id] = &$node;
      };

    };

        // dd($sectors_cat);

        // Функция отрисовки option'ов
    function tplMenu($sector, $padding) {

      if ($sector['category_status'] == 1) {
        $menu = '<option value="'.$sector['id'].'" class="first" disabled>'.$sector['name'].'</option>';
      } else {
        $menu = '<option value="'.$sector['id'].'">'.$padding.' '.$sector['name'].'</option>';
      }

            // Добавляем пробелы вложенному элементу
      if (isset($sector['children'])) {
        $i = 1;
        for($j = 0; $j < $i; $j++){
          $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }     
        $i++;

        $menu .= showCat($sector['children'], $padding);
      }
      return $menu;
    }
        // Рекурсивно считываем наш шаблон
    function showCat($data, $padding){
      $string = '';
      $padding = $padding;
      foreach($data as $item){
        $string .= tplMenu($item, $padding);
      }
      return $string;
    }

        // Получаем HTML разметку
    $sectors_list = showCat($sectors_cat, '');

        // dd($sectors_list);

        // Инфо о странице
    $page_info = pageInfo($this->entity_name);

        // Формируем пуcтой массив
    $worktime = [];
    for ($n = 1; $n < 8; $n++){$worktime[$n]['begin'] = null;$worktime[$n]['end'] = null;}

      return view('companies.create', compact('company', 'sectors_list', 'page_info', 'worktime'));
  }

  public function store(CompanyRequest $request)
  {

        // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Смотрим компанию пользователя
    $company_id = $user->company_id;
    if($company_id == null) {
      abort(403, 'Необходимо авторизоваться под компанией');
    }

    // Скрываем бога
    $user_id = hideGod($user);

    $schedule = new Schedule;
    $schedule->company_id = $company_id;
    $schedule->name = 'График работы для ' . $user->company->name;
    $schedule->description = null;
    $schedule->author_id = $user_id;
    $schedule->save();
    $schedule_id = $schedule->id;

        // Функция getWorktimes ловит все поля расписания из запроса и готовит к записи в worktimes
    $mass_time = getWorktimes($request, $schedule_id);

        // Записываем в базу все расписание.
    DB::table('worktimes')->insert($mass_time);


    $location = new Location;
    $location->city_id = $request->city_id;
    $location->address = $request->address;
    $location->save();


    if ($location) {
      $location_id = $location->id;
    } else {
      abort(403, 'Ошибка записи адреса');
    }

    $company = new Company;
    $company->name = $request->name;
    $company->alias = $request->alias;

    $company->phone = cleanPhone($request->phone);
    $company->email = $request->email;

    if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
      $company->extra_phone = cleanPhone($request->extra_phone);
    } else {$company->extra_phone = NULL;};

    $company->location_id = $location_id;

    $company->inn = $request->inn;
    $company->kpp = $request->kpp;
    $company->account_settlement = $request->account_settlement;
    $company->account_correspondent = $request->account_correspondent;

    $company->sector_id = $request->sector_id;
    $company->schedule_id = $schedule->id;

        // $company->director_user_id = $user->company_id;
    $company->author_id = $user->id;

    $company->save();

        // Создаем связь расписания с компанией
    $schedule_entity = new ScheduleEntity;
    $schedule_entity->schedule_id = $schedule->id;
    $schedule_entity->entity_id = $company->id;
    $schedule_entity->entity = 'companies';
    $schedule_entity->save();


        // $folder = new Folder;
        // $folder->folder_name = $company->company_name;
        // $link_for_folder = 'public/companies/' . $company->id;

        // if($company->company_alias != null){
        //     $link_for_folder = 'public/companies/' . $company->company_alias;
        // } else {
        //     $link_for_folder = 'public/companies/' . $company->id;
        // };

        // Создаем папку в файловой системе
    Storage::disk('public')->makeDirectory($company->id.'/media');

        // $folder->folder_url = $link_for_folder;
        // $folder->folder_alias = 'users';
        // $folder->folder_parent_id = 2;
        // $folder->save();

    return redirect('companies');
  }


  public function show($id)
  {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
    $company = Company::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
    $this->authorize('view', $company);
    return view('companies.show', compact('company'));
  }


  public function edit($id)
  {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    $company = Company::with('location.city', 'schedules.worktimes', 'sector')->moderatorLimit($answer)->findOrFail($id);
    $this->authorize(getmethod(__FUNCTION__), $company);

        // dd($company);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right('sectors', false, 'index');

        // Главный запрос
    $sectors = Sector::moderatorLimit($answer)
    ->orderBy('sort', 'asc')
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

        // Формируем дерево вложенности
    $sectors_cat = [];
    foreach ($sectors as $id => &$node) { 

          // Если нет вложений
      if (!$node['parent_id']) {
        $sectors_cat[$id] = &$node;
      } else { 

          // Если есть потомки то перебераем массив
        $sectors[$node['parent_id']]['children'][$id] = &$node;
      };
    };

        // dd($sectors_cat);

        // Функция отрисовки option'ов
    function tplMenu($sector, $padding, $id) {



      $selected = '';
      if ($sector['id'] == $id) {
            // dd($id);
        $selected = ' selected';
      }
      if ($sector['category_status'] == 1) {
        $menu = '<option value="'.$sector['id'].'" class="first"'.$selected.' disabled>'.$sector['name'].'</option>';
      } else {
        $menu = '<option value="'.$sector['id'].'"'.$selected.'>'.$padding.' '.$sector['name'].'</option>';
      }

      // Добавляем пробелы вложенному элементу
      if (isset($sector['children'])) {
        $i = 1;
        for($j = 0; $j < $i; $j++){
          $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }     
        $i++;

        $menu .= showCat($sector['children'], $padding, $id);
      }
      return $menu;
    }
        // Рекурсивно считываем наш шаблон
    function showCat($data, $padding, $id){

      $string = '';
      $padding = $padding;
      foreach($data as $item){
        $string .= tplMenu($item, $padding, $id);
      }
      return $string;
    }


    // Получаем HTML разметку
    $sectors_list = showCat($sectors_cat, '', $company->sector_id);

    if(isset($company->schedules->first()->worktimes)){
      $worktime_mass = $company->schedules->first()->worktimes->keyBy('weekday');
    }

    for($x = 1; $x<8; $x++){


      if(isset($worktime_mass[$x]->worktime_begin)){

        $worktime_begin = $worktime_mass[$x]->worktime_begin;
        $str_worktime_begin = secToTime($worktime_begin);
        $worktime[$x]['begin'] = $str_worktime_begin;
      } else {

        $worktime[$x]['begin'] = null;
      };

      if(isset($worktime_mass[$x]->worktime_interval)){

        $worktime_interval = $worktime_mass[$x]->worktime_interval;

        if(($worktime_begin + $worktime_interval) > 86400){

          $str_worktime_interval = secToTime($worktime_begin + $worktime_interval - 86400);
        }
        else {

          $str_worktime_interval = secToTime($worktime_begin + $worktime_interval);                       
        };

        $worktime[$x]['end'] = $str_worktime_interval;
      } else {

        $worktime[$x]['end'] = null;
      }

    };

            // dd($worktime);


        // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    return view('companies.edit', compact('company', 'sectors_list', 'page_info', 'worktime'));
  }


  public function update(CompanyRequest $request, $id)
  {

        // Получаем авторизованного пользователя
    $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
    $company = Company::with('location')->moderatorLimit($answer)->findOrFail($id);

    $location = $company->location;
    if($location->city_id != $request->city_id) {
      $location->city_id = $request->city_id;
      $location->save();
    }
    if($location->address = $request->address) {
      $location->address = $request->address;
      $location->save();
    }

        // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $company);

    $company->name = $request->name;
    $company->alias = $request->alias;

        // $old_link_for_folder = $company->company_alias;
        // $new_link_for_folder = 'public/companies/' . $request->company_alias;
        // Переименовываем папку в файловой системе
        // Storage::move($old_link_for_folder, $new_link_for_folder);

    $company->phone = cleanPhone($request->phone);
    $company->email = $request->email;

    if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
      $company->extra_phone = cleanPhone($request->extra_phone);
    } else {$company->extra_phone = NULL;};

    

    $company->inn = $request->inn;
    $company->kpp = $request->kpp;
    $company->account_settlement = $request->account_settlement;
    $company->account_correspondent = $request->account_correspondent;
    $company->bank = $request->bank;

    if ($company->sector_id != $request->sector_id) {
      $company->sector_id = $request->sector_id;
    }

        // $company->director_user_id = Auth::user()->company_id;
    $company->save();

        // Если не существует расписания для компании - создаем его
    if($company->schedules->count() < 1){

      $schedule = new Schedule;
      $schedule->company_id = $user->company_id;
      $schedule->name = 'График работы для ' . $company->name;
      $schedule->description = null;
      $schedule->save();

            // Создаем связь расписания с компанией
      $schedule_entity = new ScheduleEntity;
      $schedule_entity->schedule_id = $schedule->id;
      $schedule_entity->entity_id = $company->id;
      $schedule_entity->entity = 'companies';
      $schedule_entity->save();

      $schedule_id = $schedule->id;
    } else {

      $schedule_id = $company->schedules->first()->id;
    };

        // Функция getWorktimes ловит все поля расписания из запроса и готовит к записи в worktimes
    $mass_time = getWorktimes($request, $schedule_id);

        // Удаляем все записи времени в worktimes для этого расписания
    $worktimes = Worktime::where('schedule_id', $schedule_id)->forceDelete();

        // Вставляем новое время в расписание
    DB::table('worktimes')->insert($mass_time);

    return redirect('companies');
  }


  public function destroy(Request $request,$id)
  {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
    $company = Company::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $company);

    if ($company) {
      $user = $request->user();

        // Скрываем бога
      $user_id = hideGod($user);
      $company->editor_id = $user_id;
      $company->save();

        // Удаляем локацию
      $company->location()->delete();

      $company = Company::destroy($id);

        // Удаляем компанию с обновлением
      if($company) {
        return Redirect('/companies');
      } else {
        abort(403, 'Ошибка при удалении компании');
      }

    } else {
      abort(403, 'Компания не найдена');
    }
  }


  public function checkcompany(Request $request)
  {
    $company = Company::where('inn', $request->inn)->first();

    if(!isset($company)) {
      return 0;
    } else {
      return $company->name;};
    }

  }
