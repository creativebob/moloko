<?php

namespace App\Http\Controllers;

// Модели
use App\GoodsCategory;
use App\RawsArticle;

use App\Goods;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\GoodsCategoryRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

class GoodsCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(GoodsCategory $goods_category)
    {
        $this->middleware('auth');
        $this->goods_category = $goods_category;
        $this->class = GoodsCategory::class;
        $this->model = 'App\GoodsCategory';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

    // Используем трейт записи и обновления категорий
    use CategoryControllerTrait;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $goods_categories = GoodsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->withCount('goods_products')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('includes.menu_views.category_list',
                [
                    'items' => $goods_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $goods_categories->count(),
                    'id' => $request->id,
                    'nested' => 'goods_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('includes.menu_views.index',
            [
                'items' => $goods_categories,
                'page_info' => pageInfo($this->entity_alias),
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'goods_products_count',
            ]
        );
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('includes.menu_views.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории товаров',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(GoodsCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Заполнение и проверка основных полей в трейте
        $goods_category = $this->storeCategory($request);

        // Режим товаров
        $goods_category->goods_mode_id = $request->goods_mode_id;

        $goods_category->save();

        if ($goods_category) {
            // Переадресовываем на index
            return redirect()->route('goods_categories.index', ['id' => $goods_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории товаров!',
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
        $answer_goods_categories = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $goods_category = GoodsCategory::with([
            'goods_mode',
            'one_metrics' => function ($q) {
                $q->with('unit', 'values');
            },
            'set_metrics' => function ($q) {
                $q->with('unit', 'values');
            },
            'compositions.raws_product.unit',
            'compositions',
            'manufacturers'
        ])
        ->withCount('one_metrics', 'set_metrics', 'compositions')
        ->moderatorLimit($answer_goods_categories)
        ->findOrFail($id);
        // dd($goods_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_category);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('includes.metrics_category.properties_form', [
                'set_status' => $request->set_status,
                'category' => $goods_category
            ]);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_categories = operator_right('raws_categories', false, 'index');
        $answer_raws = operator_right('raws', false, 'index');

        $raws_articles = RawsArticle::with(['raws_product' => function ($q) {
            $q->with(['raws_category' => function ($q) {
                $q->select('id', 'name');
            }])->select('id', 'name', 'raws_category_id');
        }])
        ->select('id', 'name', 'raws_product_id')
        ->whereHas('raws', function ($query) {
            $query->whereNull('draft');
        })

        // ->withCount('raws_products')
        ->moderatorLimit($answer_raws_categories)
        ->companiesLimit($answer_raws_categories)
        // ->authors($answer_raws_categories)
        // ->systemItem($answer_raws_categories) // Фильтр по системным записям
        ->get()
        ->keyBy('id')
        ->groupBy('raws_product.raws_category.name');
        // ->toArray();
        // dd($raws_articles);

        $composition_list = [
            'name' => 'Сырье',
            'alias' => 'raws',
            'composition_categories' => $raws_articles,
        ];
        // dd($composition_list);


        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('goods_categories.edit', compact('goods_category', 'page_info', 'properties', 'properties_list', 'composition_list'));
    }

    public function update(GoodsCategoryRequest $request, $id)
    {

        // TODO -- На 15.06.18 нет нормального решения отправки фотографий по ajax с методом "PATCH"

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $goods_category = GoodsCategory::moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_category);

        // Заполнение и проверка основных полей в трейте
        $goods_category = $this->updateCategory($request, $goods_category);

        // Если сменили тип категории продукции, то меняем его и всем вложенным элементам
        if (($goods_category->parent_id == null) && ($goods_category->goods_type_id != $request->goods_type_id)) {
            $goods_category->goods_type_id = $request->goods_type_id;

            $goods_categories = GoodsCategory::whereCategory_id($id)
            ->update(['goods_mode_id' => $request->goods_mode_id]);
        }

        // Производители
        if (isset($request->manufacturers)) {
            $goods_category->manufacturers()->sync($request->manufacturers);
        } else {
            $goods_category->manufacturers()->detach();
        }

        $goods_category->save();

        if ($goods_category) {

            // Переадресовываем на index
            return redirect()->route('goods_categories.index', ['id' => $goods_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории продукции!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $goods_category = GoodsCategory::with('childs', 'goods_products')
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_category);

        // Скрываем бога
        $goods_category->editor_id = hideGod($request->user());
        $goods_category->save();

        $parent_id = $goods_category->parent_id;

        $goods_category = GoodsCategory::destroy($id);

        if ($goods_category) {

            // Переадресовываем на index
            return redirect()->route('goods_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи сектора!'
            ];
        }
    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    // Для заказа
    public function ajax_get_products(Request $request)
    {

        $user = $request->user();
        $id = $request->id;
        // $id = 12;

        $goods_list = Goods::with('goods_article', 'photo')
        ->whereHas('goods_article', function ($query) use ($id, $user) {
            $query->whereNull('draft')
            ->whereNull('archive')
            ->whereHas('goods_product', function ($query) use ($id, $user) {
                $query->whereHas('goods_category', function ($query) use ($id, $user) {
                    $query->where(['company_id' => $user->company_id, 'id' => $id]);
                });
            });
        })
        ->get();
        // dd($goods_list);
        $entity = 'goods';

        return view('leads.items', compact('goods_list', 'entity'));
    }

    // public function ajax_get_metrics(Request $request)
    // {

    //     $item = GoodsCategory::with('metrics.property')->findOrFail($request->goods_category_id);
    //     return view('goods.metrics.metric_enter', compact('item'));
    // }

    // public function ajax_get_compositions(Request $request)
    // {

    //     $item = GoodsCategory::with('compositions.raws_product.unit')->findOrFail($request->goods_category_id);
    //     return view('goods.compositions.composition_enter', compact('item'));
    // }

}
