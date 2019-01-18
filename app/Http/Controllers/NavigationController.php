<?php

namespace App\Http\Controllers;

// Модели
use App\Navigation;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\NavigationRequest;

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

        $navigations = Navigation::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->where('site_id', $site_id)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        return view('navigations.index',[
            'navigations' => $navigations,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }


    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('navigations.create', [
            'navigation' => new $this->class,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }


    public function store(NavigationRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Наполняем сущность данными
        $navigation = new Navigation;

        // Делаем заглавной первую букву
        $navigation->name = get_first_letter($request->name);
        $navigation->navigations_category_id = $request->navigations_category_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $navigation->moderation = 1;
        }

        // Системная запись
        $navigation->system_item = $request->system_item;
        $navigation->display = $request->display;


        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $navigation->company_id = $user->company_id;
        $navigation->author_id = hideGod($user);

        $navigation->save();

        if ($navigation) {

            // Переадресовываем на index
            return redirect()->route('navigations.index');
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

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $navigation);

        return view('navigations.edit', [
            'navigation' => $navigation,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(NavigationRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $navigation);

        // Делаем заглавной первую букву
        $navigation->name = get_first_letter($request->name);
        $navigation->navigations_category_id = $request->navigations_category_id;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false) {
            $navigation->moderation = 1;
        } else {
            $navigation->moderation = $request->moderation;
        }

        // Модерация и системная запись
        $navigation->system_item = $request->system_item;
        $navigation->moderation = $request->moderation;
        $navigation->display = $request->display;

        $navigation->editor_id = hideGod($request->user());
        $navigation->save();

        if ($navigation) {

            // Переадресовываем на index
            return redirect()->route('navigations.index');
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи навигации!'
            ];
        }
    }

    public function destroy(Request $request, $id)
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
            return redirect()->route('navigations.index');
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении навигации!'
            ];
        }
    }
}
