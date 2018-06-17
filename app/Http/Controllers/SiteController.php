<?php

namespace App\Http\Controllers;

// Модели
use App\Site;
use App\Page;
use App\Menu;
use App\MenuSite;
use App\Company;
use App\Department;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\SiteRequest;

// Политика
use App\Policies\SitePolicy;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

// Специфические классы 


// На удаление
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'sites';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Site::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $sites = Site::with('author', 'company')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        // ->filter($request, 'position_id')
        // ->filter($request, 'department_id')
        ->orderBy('moderation', 'desc')
        ->paginate(30);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $filter_query = Site::with('author', 'company')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get();

        $filter['status'] = null;

        // $filter = addFilter($filter, $filter_query, $request, 'Выберите должность:', 'position', 'position_id');
        // $filter = addFilter($filter, $filter_query, $request, 'Выберите отдел:', 'department', 'department_id');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // ---------------------------------------------------------------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('sites.index', compact('sites', 'page_info', 'filter'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Site::class);

        // Список меню для сайта
        $answer_menus = operator_right('menus', false, 'index');

        $menus = Menu::whereNavigation_id(1) // Только для сайтов, разделы сайта
        ->moderatorLimit($answer_menus)
        ->companiesLimit($answer_menus)
        ->authors($answer_menus)
        ->systemItem($answer_menus) // Фильтр по системным записям
        ->template($answer_menus) // Выводим шаблоны в список
        ->get();

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $departments = Department::where(['filial_status' => 1, 'company_id' => $user->company_id])->get();
        // dd($departments);

        $site = new Site;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('sites.create', compact('site', 'menus', 'page_info', 'departments'));  
    }


    public function store(SiteRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Site::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Наполняем сущность данными
        $site = new Site;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $site->moderation = 1;
        }

        // Cистемная запись
        $site->system_item = $request->system_item;

        $site->name = $request->name;
        $site->domain = $request->domain;

        // Пока отсекаем по точке
        $site_alias = explode('.', $request->domain);
        $site->alias = $site_alias[0];

        $site->api_token = str_random(60);
        $site->company_id = $user->company_id;
        $site->author_id = $user_id;
        $site->save();

        if ($site) {

            // Пришем список пришедших разделов сайта
            $site->menus()->attach($request->menus);

            // Пришем список пришедших филиалов сайта
            $site->departments()->attach($request->departments);

            return Redirect('/sites');
        } else {
            abort(403, 'Ошибка записи сайта');
        }
    }

    public function show(Request $request)
    {
        //
    }

    public function edit(Request $request, $alias)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $answer_menus = operator_right('menus', false, 'index');

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // ГЛАВНЫЙ ЗАПРОС:
        $site = Site::with(['departments', 'company' => function ($query) use ($user) {
            $query->with(['departments' => function ($query) {
                $query->where('filial_status', 1);
            }])->where('id', $user->company_id)->first();
        }])->moderatorLimit($answer)->whereAlias($alias)->first();
        // dd($site);

        $departments = $site->company->departments;
        // dd($departments);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $site);

        // Список меню для сайта
        $menus = Menu::whereNavigation_id(1) // Только для сайтов, разделы сайта
        ->moderatorLimit($answer_menus)
        ->companiesLimit($answer_menus)
        ->authors($answer_menus)
        ->systemItem($answer_menus) // Фильтр по системным записям
        ->template($answer_menus) // Выводим шаблоны в список
        ->get();

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('sites.edit', compact('site', 'menus', 'page_info', 'departments'));
    }

    public function update(SiteRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $site = Site::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('update', $site);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $site->name = $request->name;

        if ($site->domain != $request->domain) {
            $site_alias = explode('.', $request->domain);
            $site->alias = $site_alias[0];
        }
        
        $site->domain = $request->domain;
        $site->editor_id = $user_id;
        $site->save();

        if ($site) {

            // Смотрим пришедние для сайта разделы и синхронизируем с существующими
            if (isset($request->menus)) {

                // Синхронизируем с существующими
                $site->menus()->sync($request->menus);
            } else {

                // Если удалили последний раздел для сайта и пришел пустой массив
                $site->menus()->detach();
            }

            // Смотрим пришедние для сайта филиалы и синхронизируем с существующими
            if (isset($request->menus)) {

                // Синхронизируем с существующими
                $site->departments()->sync($request->departments);
            } else {

                // Если удалили последний филиал для сайта и пришел пустой массив
                $site->departments()->detach();
            }

            return Redirect('/sites');
        } else {
            abort(403, 'Ошибка обновления сайта');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $site = Site::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $site);

        if ($site) {

            // Получаем пользователя
            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            $site->editor_id = $user_id;
            $site->save();

            // Удаляем сайт с обновлением
            $site = Site::destroy($id);

            if ($site) {
                return Redirect('/sites');
            } else {
                abort(403, 'Ошибка при удалении сайта');
            }
        } else {
            abort(403, 'Сайт не найден');
        }
    }

    // Сортировка
    public function sites_sort(Request $request)
    {
        $result = '';
        $i = 1;
        foreach ($request->sites as $item) {

            $sites = Site::findOrFail($item);
            $sites->sort = $i;
            $sites->save();
            $i++;
        }
    }

    public function sections($alias)
    { 

        // ГЛАВНЫЙ ЗАПРОС:
        $answer_sites = operator_right($this->entity_name, $this->entity_dependence, 'update');

        $answer_menus = operator_right('menus', false, 'update');

        $site = Site::with(['menus', 'author'])
        ->moderatorLimit($answer_sites)
        ->companiesLimit($answer_sites)
        ->authors($answer_sites)
        ->systemItem($answer_sites) // Фильтр по системным записям
        ->template($answer_sites) // Выводим шаблоны в список
        ->whereAlias($alias)
        ->first();

        // Подключение политики
        $this->authorize('view', $site);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('sites.sections', compact('site', 'page_info'));
    }

    // Проверка наличия в базе
    public function site_check(Request $request)
    {

        // Проверка навигации по сайту в нашей базе данных
        $site = Site::whereDomain($request->domain)->first();

        // Если такой сайт существует
        if ($site) {
            $result = [
                'error_status' => 1,
            ];

        // Если нет
        } else {
            $result = [
                'error_status' => 0,
            ];
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }


    // ------------------------------------ API -----------------------------------------------------
    // Получаем сайт по api
    public function api_index (Request $request)
    {
        $site = Site::where('api_token', $request->token)->first();

        if ($site) {
            // return Cache::remember('site', 1, function() use ($domain) {
            return Site::with(['departments.location.city', 'company.location.city', 'company.schedules.worktimes', 'company.products_categories.products' => function ($query) {
                $query->whereDisplay(1);
            }, 'pages' => function ($query) {
                $query->whereDisplay(1);
            }, 'navigations.menus.page', 'navigations.navigations_category', 'navigations' => function ($query) {
                $query->whereDisplay(1);
            }, 'navigations.menus' => function ($query) {
                $query->whereDisplay(1)->orderBy('sort', 'asc');
            }])->whereDomain($request->domain)->orderBy('sort', 'asc')->first();
            // });
        } else {
            return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
        }
    }
}
