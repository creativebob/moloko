<?php

namespace App\Http\Controllers;

// Модели
use App\Navigation;

// Валидация
use App\Site;
use Illuminate\Http\Request;
use App\Http\Requests\System\NavigationRequest;

class NavigationController extends Controller
{

    // Настройки контроллера
    public function __construct(Navigation $navigation)
    {
        $this->middleware('auth');
        $this->navigation = $navigation;
        $this->class = Navigation::class;
        $this->model = 'App\Navigation';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request, $site_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence,  getmethod(__FUNCTION__));

        $navigations = Navigation::with('align')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->where('site_id', $site_id)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($navigations);

        $site = Site::findOrFail($site_id);

        return view('navigations.index', [
            'navigations' => $navigations,
            'pageInfo' => pageInfo($this->entity_alias),
            'site_id' => $site_id,
            'site' => $site
        ]);
    }


    public function create(Request $request, $site_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $site = Site::findOrFail($site_id);

        return view('navigations.create', [
            'navigation' => new $this->class,
            'pageInfo' => pageInfo($this->entity_alias),
            'site_id' => $site_id,
            'site' => $site
        ]);
    }


    public function store(NavigationRequest $request, $site_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Наполняем сущность данными
        $navigation = new Navigation;

        // Делаем заглавной первую букву
        $navigation->name = get_first_letter($request->name);

        $navigation->site_id = $site_id;

        $navigation->align_id = $request->align_id;

        // $navigation->navigations_category_id = $request->navigations_category_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $navigation->moderation = true;
        }

        // Системная запись
        $navigation->system = $request->system;
        $navigation->display = $request->display;


        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $navigation->company_id = $user->company_id;
        $navigation->author_id = hideGod($user);

        $navigation->save();

        if ($navigation) {

            // Переадресовываем на index
            return redirect()->route('navigations.index', ['site_id' => $site_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи навигации!'
            ];
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $site_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $navigation);

        $site = Site::findOrFail($site_id);

        return view('navigations.edit', [
            'navigation' => $navigation,
            'pageInfo' => pageInfo($this->entity_alias),
            'site_id' => $site_id,
            'site' => $site
        ]);
    }

    public function update(NavigationRequest $request, $site_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $navigation);

        // Делаем заглавной первую букву
        $navigation->name = get_first_letter($request->name);

        $navigation->align_id = $request->align_id;

        // $navigation->navigations_category_id = $request->navigations_category_id;

        // Модерация и системная запись
        $navigation->system = $request->system;
        $navigation->moderation = $request->moderation;
        $navigation->display = $request->display;

        $navigation->editor_id = hideGod($request->user());
        $navigation->save();

        if ($navigation) {

            // Переадресовываем на index
            return redirect()->route('navigations.index', ['site_id' => $site_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи навигации!'
            ];
        }
    }

    public function destroy(Request $request, $site_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::with('menus')
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $navigation);

        // Если нет, мягко удаляем
        $navigation->editor_id = hideGod($request->user());
        $navigation->save();

        // Если нет, мягко удаляем
        $navigation->delete();

        if ($navigation) {

            // Переадресовываем на index
            return redirect()->route('navigations.index', ['site_id' => $site_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении навигации!'
            ];
        }
    }

    // ------------------------------------------- Ajax ---------------------------------------------

    // Проверка наличия в базе
    public function ajax_check (Request $request, $site_id)
    {

        // Проверка навигации по сайту в нашей базе данных
        $result_count = Navigation::where([
            'site_id' => $site_id,
            'name' => $request_name
        ])->count();

        return response()->json($result_count);


        $site = Site::withCount(['pages' => function($query) use ($page_alias) {
            $query->whereAlias($page_alias);
        }])->whereAlias($alias)->first();

        // Если такая навигация есть
        if ($site->pages_count > 0) {
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
}
