<?php

namespace App\Http\Controllers;

// Модели
use App\Catalog;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\CatalogRequest;

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
    }

    public function index(Request $request)
    {


        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs = Catalog::with([
            'author',
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($catalogs);

        return view('catalogs.index',[
            'catalogs' => $catalogs,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('catalogs.create', [
            'catalog' => new $this->class,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(CatalogRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $catalog = new Catalog;
        $catalog->name = $request->name;
        $catalog->description = $request->description;


        // Алиас
        $catalog->alias = empty($request->alias) ? Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]) : $request->alias;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        if($answer['automoderate'] == false){
            $catalog->moderation = 1;
        }

        // Cистемная запись
        $catalog->system_item = $request->system_item;
        $catalog->display = $request->display;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $catalog->company_id = $user->company_id;
        $catalog->author_id = hideGod($user);

        $catalog->save();

        if ($catalog) {

            return redirect()->route('catalogs.index');

        } else {
            abort(403, 'Ошибка при записи каталога!');
        }
    }

    public function show(Request $request)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalog = Catalog::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalog);

        // dd($catalog);
        return view('catalogs.edit', [
            'catalog' => $catalog,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(CatalogRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalog = Catalog::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalog);

        $catalog->name = $request->name;
        $catalog->description = $request->description;

        // Если ввели алиас руками
        if (isset($request->alias) && ($catalog->alias != $request->alias)) {
            $catalog->alias = $request->alias;
        }

        $catalog->system_item = $request->system_item;
        $catalog->moderation = $request->moderation;
        $catalog->display = $request->display;

        $catalog->save();

        if ($catalog) {

            return redirect()->route('catalogs.index');

        } else {
            abort(403, 'Ошибка при обновлении каталога!');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalog = Catalog::with(['items'])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalog);

        $catalog->editor_id = hideGod($request->user());
        $catalog->save();

        $catalog->delete();

        if ($catalog) {

            return redirect()->route('catalogs.index');

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
