<?php

namespace App\Http\Controllers;

// Модели
use App\CatalogsGoods;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\CatalogsGoodsRequest;

// Транслитерация
use Illuminate\Support\Str;

class CatalogsGoodsController extends Controller
{

    // Настройки сконтроллера
    public function __construct(CatalogsGoods $catalogs_goods)
    {
        $this->middleware('auth');
        $this->catalogs_goods = $catalogs_goods;
        $this->entity_alias = with(new CatalogsGoods)->getTable();;
        $this->entity_dependence = false;
        $this->class = CatalogsGoods::class;
        $this->model = 'App\CatalogsGoods';
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs_goods = CatalogsGoods::with([
            'price_goods.goods.article',
            'author',
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
//         dd($catalogs_goods);

        return view('catalogs_goods.index',[
            'catalogs_goods' => $catalogs_goods,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('catalogs_goods.create', [
            'catalogs_goods' => new $this->class,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(CatalogsGoodsRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $catalogs_goods = CatalogsGoods::create($data);

        if ($catalogs_goods) {

            $departments = session('access.all_rights.index-departments-allow');
            if ($departments) {
                $catalogs_goods->filials()->sync($request->filials);
            }

            return redirect()->route('catalogs_goods.index');

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

        $catalogs_goods = CatalogsGoods::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_goods);

        $catalogs_goods->load('filials');

        // dd($catalogs_goods);
        return view('catalogs_goods.edit', [
            'catalogs_goods' => $catalogs_goods,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(CatalogsGoodsRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs_goods = CatalogsGoods::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_goods);

        $data = $request->input();
        $result = $catalogs_goods->update($data);

        if ($result) {

            $departments = session('access.all_rights.index-departments-allow');
            if ($departments) {
                $catalogs_goods->filials()->sync($request->filials);
            }

            return redirect()->route('catalogs_goods.index');

        } else {
            abort(403, 'Ошибка при обновлении каталога!');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogs_goods = CatalogsGoods::with(['items'])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_goods);


        $catalogs_goods->delete();

        if ($catalogs_goods) {

            return redirect()->route('catalogs_goods.index');

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
        $result_count = CatalogsGoods::whereHas('site', function ($query) use ($alias) {
            $query->whereAlias($alias);
        })
        ->whereCompany_id($request->user()->company_id)
        ->where($request->field, $request->value)
        ->where('id', '!=', $request->id)
        ->count();

        return response()->json($result_count);
    }

    public function get_catalog ($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_cg = operator_right('catalogs_goods', true, getmethod('index'));

        $сatalog_goods = CatalogsGoods::with([
            'items' => function ($q) {
                $q->with([
                    'prices' => function ($q) {
                        $q->with([
                            'product' => function($q) {
                                $q->with([
                                    'article' => function ($q) {
                                        $q->with([
                                            'photo',
                                            'manufacturer'
                                        ])
                                            ->where('draft', false);
                                    }
                                ])
                                    ->whereHas('article', function ($q) {
                                        $q->where('draft', false);
                                    })
                                    ->where('archive', false);
                            }
                        ])
                            ->whereHas('product', function ($q) {
                                $q->where('archive', false);
                            })
                            ->where('archive', false);
                    },
                    'childs'
                ]);
            }
        ])
            ->moderatorLimit($answer_cg)
            ->companiesLimit($answer_cg)
            ->authors($answer_cg)
            ->filials($answer_cg)
            ->whereHas('sites', function ($q) {
                $q->whereId(1);
            })
            ->findOrFail($id);
//         dd($сatalog_goods);

        return view('leads.catalogs.catalog_goods', compact('сatalog_goods'));
    }
}
