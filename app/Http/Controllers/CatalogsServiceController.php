<?php

namespace App\Http\Controllers;

// Модели
use App\CatalogsService;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\CatalogsServiceRequest;

// Транслитерация
use Illuminate\Support\Str;

class CatalogsServiceController extends Controller
{

    // Настройки сконтроллера
    public function __construct(CatalogsService $catalogs_service)
    {
        $this->middleware('auth');
        $this->catalogs_service = $catalogs_service;
        $this->entity_alias = with(new CatalogsService)->getTable();;
        $this->entity_dependence = false;
        $this->class = CatalogsService::class;
        $this->model = 'App\CatalogsService';
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs_services = CatalogsService::with([
            'price_services.service.article',
            'author',
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($catalogs_services);

        return view('catalogs_services.index',[
            'catalogs_services' => $catalogs_services,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('catalogs_services.create', [
            'catalogs_service' => new $this->class,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(CatalogsServiceRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $catalogs_service = new CatalogsService;
        $catalogs_service->name = $request->name;
        $catalogs_service->description = $request->description;

        // Алиас
        $catalogs_service->alias = empty($request->alias) ? Str::slug($request->name) : $request->alias;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        if($answer['automoderate'] == false){
            $catalogs_service->moderation = true;
        }

        // Cистемная запись
        $catalogs_service->system = $request->has('system');
        $catalogs_service->display = $request->has('display');

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $catalogs_service->company_id = $user->company_id;
        $catalogs_service->author_id = hideGod($user);

        $catalogs_service->save();

        if ($catalogs_service) {

            // Сайты
            $catalogs_service->sites()->attach($request->sites);

            return redirect()->route('catalogs_services.index');

        } else {
            abort(403, 'Ошибка при записи каталога!');
        }
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs_service = CatalogsService::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_service);

        // dd($catalogs_service);
        return view('catalogs_services.edit', [
            'catalogs_service' => $catalogs_service,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(CatalogsServiceRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs_service = CatalogsService::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_service);

        $catalogs_service->name = $request->name;
        $catalogs_service->description = $request->description;

        // Если ввели алиас руками
        if (isset($request->alias) && ($catalogs_service->alias != $request->alias)) {
            $catalogs_service->alias = $request->alias;
        }

        $catalogs_service->system = $request->has('system');
        $catalogs_service->moderation = $request->has('moderation');
        $catalogs_service->display = $request->has('display');

        $catalogs_service->save();

        if ($catalogs_service) {

            // Обновляем сайты
            $catalogs_service->sites()->sync($request->sites);

            return redirect()->route('catalogs_services.index');

        } else {
            abort(403, 'Ошибка при обновлении каталога!');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogs_service = CatalogsService::with(['items'])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_service);

        $catalogs_service->editor_id = hideGod($request->user());
        $catalogs_service->save();

        $catalogs_service->delete();

        if ($catalogs_service) {

            return redirect()->route('catalogs_services.index');

        } else {
            abort(403, 'Ошибка при удалении каталога!');
        }
    }




    // ------------------------------------------------ Ajax -------------------------------------------------

    // Проверка наличия в базе
    public function ajax_check (Request $request, $alias)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Проверка каталога в нашей базе данных
        $result_count = CatalogsService::whereHas('site', function ($query) use ($alias) {
            $query->whereAlias($alias);
        })
        ->whereCompany_id($request->user()->company_id)
        ->where($request->field, $request->value)
        ->where('id', '!=', $request->id)
        ->count();

        return response()->json($result_count);
    }
}
