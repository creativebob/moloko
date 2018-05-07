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

        $companies = Company::with('author', 'director', 'city')
        ->moderatorLimit($answer)
        ->moderatorLimit($answer)
        ->cityFilter($request)
        ->sectorFilter($request)
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->paginate(30);

        $filter_query = Company::with('city', 'sector')->moderatorLimit($answer)->get();
        $filter['status'] = null;

        $filter = addFilter($filter, $filter_query, $request, 'Выберите город:', 'city', 'city_id');
        $filter = addFilter($filter, $filter_query, $request, 'Выберите сектор:', 'sector', 'sector_id');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addFilter($filter, $filter_query, $request, 'Мои списки:', 'booklist', 'booklist_id', $this->entity_name);

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
        ->get(['id','sector_name','industry_status','sector_parent_id'])
        ->keyBy('id')
        ->toArray();

        // Формируем дерево вложенности
        $sectors_cat = [];
        foreach ($sectors as $id => &$node) { 

          // Если нет вложений
          if (!$node['sector_parent_id']) {
            $sectors_cat[$id] = &$node;
          } else { 

          // Если есть потомки то перебераем массив
            $sectors[$node['sector_parent_id']]['children'][$id] = &$node;
          };

        };

        // dd($sectors_cat);

        // Функция отрисовки option'ов
        function tplMenu($sector, $padding) {

            if ($sector['industry_status'] == 1) {
              $menu = '<option value="'.$sector['id'].'" class="first" disabled>'.$sector['sector_name'].'</option>';
            } else {
              $menu = '<option value="'.$sector['id'].'">'.$padding.' '.$sector['sector_name'].'</option>';
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

        // Формируем пуcто
        $worktime = [];
        for ($n = 1; $n < 8; $n++){

            $worktime[$n]['begin'] = null;
            $worktime[$n]['end'] = null;
        }

        return view('companies.create', compact('company', 'sectors_list', 'page_info', 'worktime'));
    }

    public function store(CompanyRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем авторизованного пользователя
        $user = $request->user();

        $company = new Company;
        $company->company_name = $request->company_name;
        $company->company_alias = $request->company_alias;

        $company->phone = cleanPhone($request->phone);
        $company->email = $request->email;

        if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
            $company->extra_phone = cleanPhone($request->extra_phone);
        } else {$company->extra_phone = NULL;};

        $company->city_id = $request->city_id;
        $company->address = $request->address;

        $company->inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->account_settlement = $request->account_settlement;
        $company->account_correspondent = $request->account_correspondent;

        $company->sector_id = $request->sector_id;

        // $company->director_user_id = $user->company_id;
        $company->author_id = $user->id;

        $company->save();


        // Создать связь сотрудника, филиала и ролей должности
        // $mass = [];

        // foreach ($staffer->position->roles as $role) {
        //   $mass[] = [

        //     'company_id' => $request->
        //     'weekday' => $request->
        //     'worktime_begin' => $request->
        //     'worktime_interval' => $request->
        //     'timeout' => $request->
        //     'timeout_interval' => $request->
        //     'author_id' => $user->id,

        //   ];
        // }

        // DB::table('role_user')->insert($mass); 
        // 
        // 

        // $worktime = new Worktime;

        $mass_time = [
            'weekday' => '1',
            'worktime_begin' => timeToSec($request->mon_begin),
            'worktime_interval' => timeToSec($request->mon_end) - timeToSec($request->mon_begin)
        ];

        dd($mass_time);

        // $mon_begin = $request->mon_begin;
        // $mon_end = $request->mon_end;

        // $tue_begin = $request->tue_begin;
        // $tue_end = $request->tue_end;

        // $wed_begin = $request->wed_begin;
        // $wed_end = $request->wed_end;

        // $thu_begin = $request->thu_begin;
        // $thu_end = $request->thu_end;

        // $fri_begin = $request->fri_begin;
        // $fri_end = $request->fri_end;

        // $sat_begin = $request->sat_begin;
        // $sat_end = $request->sat_end;

        // $sun_begin = $request->sun_begin;
        // $sun_end = $request->sun_end;
        
        dd($mass_time);


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

        $company = Company::with('city', 'worktime')->moderatorLimit($answer)->findOrFail($id);
        $this->authorize(getmethod(__FUNCTION__), $company);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('sectors', false, 'index');

        // Главный запрос
        $sectors = Sector::moderatorLimit($answer)
        ->orderBy('sort', 'asc')
        ->get(['id','sector_name','industry_status','sector_parent_id'])
        ->keyBy('id')
        ->toArray();

        // Формируем дерево вложенности
        $sectors_cat = [];
        foreach ($sectors as $id => &$node) { 

          // Если нет вложений
          if (!$node['sector_parent_id']) {
            $sectors_cat[$id] = &$node;
          } else { 

          // Если есть потомки то перебераем массив
            $sectors[$node['sector_parent_id']]['children'][$id] = &$node;
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
          if ($sector['industry_status'] == 1) {
            $menu = '<option value="'.$sector['id'].'" class="first"'.$selected.' disabled>'.$sector['sector_name'].'</option>';
          } else {
            $menu = '<option value="'.$sector['id'].'"'.$selected.'>'.$padding.' '.$sector['sector_name'].'</option>';
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

        $worktime_mass = $company->worktime->keyBy('weekday');


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
                $str_worktime_interval = secToTime($worktime_begin + $worktime_interval);
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
        $company = Company::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

        $company->company_name = $request->company_name;
        $company->company_alias = $request->company_alias;

        // $old_link_for_folder = $company->company_alias;
        // $new_link_for_folder = 'public/companies/' . $request->company_alias;
        // Переименовываем папку в файловой системе
        // Storage::move($old_link_for_folder, $new_link_for_folder);

        $company->phone = cleanPhone($request->phone);
        $company->email = $request->email;

        if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
            $company->extra_phone = cleanPhone($request->extra_phone);
        } else {$company->extra_phone = NULL;};

        $company->city_id = $request->city_id;
        $company->address = $request->address;

        $company->inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->account_settlement = $request->account_settlement;
        $company->account_correspondent = $request->account_correspondent;
        $company->bank = $request->bank;

        if ($company->sector_id !== $request->sector_id) {
          $company->sector_id = $request->sector_id;
        }
        

        // $company->director_user_id = Auth::user()->company_id;
        $company->save();
        return redirect('companies');
    }


    public function destroy($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

        $company = Company::destroy($id);

        if($company) {
          return Redirect('/companies');
        } else {
          echo 'произошла ошибка';
        }; 

        Log::info('Удалили запись из таблицы Компании. ID: ' . $id);
    }


    public function checkcompany(Request $request)
    {
        $company = Company::where('inn', $request->inn)->first();

        if(!isset($company)){
            return 0;} else {
            return $company->company_name;};
    }

}
