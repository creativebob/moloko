<?php

namespace App\Http\Controllers;

// Модели
use App\Catalog;
use App\Site;
use App\EntitySetting;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\CatalogRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

// Транслитерация
use Transliterate;

class CatalogController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Catalog $catalog)
    {
        $this->middleware('auth');
        $this->catalog = $catalog;
        $this->entity_alias = with(new Catalog)->getTable();;
        $this->entity_dependence = false;
        $this->class = Catalog::class;
        $this->model = 'App\Catalog';
        $this->type = 'edit';
    }

    // Используем трейт записи и обновления категорий
    use CategoryControllerTrait;

    public function index(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs = Catalog::with([
            'site',
            'raws',
            'goods',
            'services'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->whereHas('site', function ($q) use ($alias) {
            $q->where('alias', $alias);
        })
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('includes.menu_views.category_list',
                [
                    'items' => $catalogs,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $catalogs->count(),
                    'id' => $request->id,
                    'alias' => $alias
                ]
            );

        }

        return view('catalogs.index',[
            'catalogs' => $catalogs,
            'page_info' => pageInfo($this->entity_alias),
            'parent_page_info' => pageInfo('sites'),
            'site' => Site::moderatorLimit(operator_right('sites', false, getmethod(__FUNCTION__)))
            ->whereAlias($alias)
            ->first(),
            'entity' => $this->entity_alias,
            'class' => $this->model,
            'type' => $this->type,
            'id' => $request->id,
        ]);
    }

    public function create(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('includes.menu_views.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление каталога',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(CatalogRequest $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Заполнение и проверка основных полей в трейте
        $catalog = $this->storeCategory($request);

        $site = Site::where(['alias' => $alias, 'company_id' => $request->user()->company_id])->first();
        $catalog->site_id = $site->id;

        // Алиас
        $catalog->alias = empty($request->alias) ? Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]) : $request->alias;

        $catalog->save();

        if ($catalog) {

            return redirect()->route('catalogs.index', ['alias' => $alias, 'id' => $catalog->id]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи каталога!',
            ];
        }
    }

    public function show(Request $request, $alias, $catalog_alias)
    {
        //
    }

    public function edit(Request $request, $alias, $id)
    {

        $catalog = Catalog::moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalog);

        // dd($catalog);
        return view('catalogs.edit', [
            'catalog' => $catalog,
            'page_info' => pageInfo($this->entity_alias),
            'parent_page_info' => pageInfo('sites'),
            'site' => Site::moderatorLimit(operator_right('sites', false, getmethod(__FUNCTION__)))
            ->whereAlias($alias)
            ->first(),
        ]);
    }

    public function update(CatalogRequest $request, $alias, $id)
    {

        $catalog = Catalog::moderatorLimit(operator_right($this->entity_alias, $this->entity_alias, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalog);

        // Заполнение и проверка основных полей в трейте
        $sector = $this->updateCategory($request, $catalog);

        // Если ввели алиас руками
        if (isset($request->alias) && ($catalog->alias != $request->alias)) {
            $catalog->alias = $request->alias;
        } else {
            // Иначе переводим заголовок в транслитерацию
            $catalog->alias = Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]);
        }

        $catalog->save();

        if ($catalog) {
            // Переадресовываем на index
            return redirect()->route('catalogs.index', ['alias' => $alias, 'id' => $catalog->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении каталога!'
            ];
        }
    }

    public function destroy(Request $request, $alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalog = Catalog::with('childs', 'services', 'goods', 'raws')
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalog);

        $catalog->editor_id = hideGod($request->user());
        $catalog->save();

        $parent_id = $catalog->parent_id;

        $catalog = Catalog::destroy($id);

        if ($catalog) {
            // Переадресовываем на index
            return redirect()->route('catalogs.index', ['alias' => $alias, 'id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении каталога!'
            ];
        }
    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    // Проверка наличия в базе
    public function ajax_check (Request $request, $alias)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Проверка каталога в нашей базе данных
        $result_count = Catalog::whereHas('site', function ($query) use ($alias) {
            $query->whereAlias($alias);
        })
        ->whereCompany_id($request->user()->company_id)
        ->where($request->field, $request->value)
        ->where('id', '!=', $request->id)
        ->count();

        return response()->json($result_count);
    }
}
