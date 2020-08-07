<?php

namespace App\Http\Controllers;

// Модели
use App\CatalogsService;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\CatalogsServiceRequest;

// Транслитерация
use Illuminate\Support\Str;

class CatalogsServiceController extends Controller
{

    /**
     * CatalogsServiceController constructor.
     * @param CatalogsService $catalogs_service
     */
    public function __construct(CatalogsService $catalogs_service)
    {
        $this->middleware('auth');
        $this->catalogs_service = $catalogs_service;
        $this->entity_alias = with(new CatalogsService)->getTable();;
        $this->entity_dependence = false;
        $this->class = CatalogsService::class;
        $this->model = 'App\CatalogsService';
    }

    /**
     * Отображение списка ресурсов.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs_services = CatalogsService::with([
            'price_services.service.process',
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

    /**
     * Показать форму для создания нового ресурса.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('catalogs_services.create', [
            'catalogs_service' => CatalogsService::make(),
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Сохранение созданного ресурса в хранилище.
     *
     * @param CatalogsServiceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CatalogsServiceRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $catalog_service = CatalogsService::create($data);

        if ($catalog_service) {

            $departments = session('access.all_rights.index-departments-allow');
            if ($departments) {
                $catalog_service->filials()->sync($request->filials);
            }

            return redirect()->route('catalogs_services.index');

        } else {
            abort(403, 'Ошибка при записи каталога!');
        }
    }

    /**
     * Отображение указанного ресурса.
     *
     * @param $id
     */
    public function show($id)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs_service = CatalogsService::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_service);

        $catalogs_service->load('filials');

        // dd($catalogs_service);
        return view('catalogs_services.edit', [
            'catalogs_service' => $catalogs_service,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param CatalogsServiceRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(CatalogsServiceRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs_service = CatalogsService::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_service);

        $data = $request->input();
        $result = $catalogs_service->update($data);

        if ($result) {

            $departments = session('access.all_rights.index-departments-allow');
            if ($departments) {
                $catalogs_service->filials()->sync($request->filials);
            }

            return redirect()->route('catalogs_services.index');

        } else {
            abort(403, 'Ошибка при обновлении каталога!');
        }
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogs_service = CatalogsService::with([
            'items'
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_service);

        $catalogs_service->delete();

        if ($catalogs_service) {

            return redirect()->route('catalogs_services.index');

        } else {
            abort(403, 'Ошибка при удалении каталога!');
        }
    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    /**
     * Проверка наличия в базе
     *
     * @param Request $request
     * @param $alias
     * @return \Illuminate\Http\JsonResponse
     */
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
