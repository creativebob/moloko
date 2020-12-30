<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\GoodsCategoryStoreRequest;
use App\GoodsCategory;
use App\Http\Requests\System\GoodsCategoryUpdateRequest;
use Illuminate\Http\Request;

class GoodsCategoryController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * GoodsCategoryController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->class = GoodsCategory::class;
        $this->model = 'App\GoodsCategory';
        $this->entityAlias = 'goods_categories';
        $this->entityDependence = false;
        $this->type = 'page';
    }

    use Photable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), GoodsCategory::class);

        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $goods_categories = GoodsCategory::with([
            'goods',
            'childs',
            'groups',
            'direction'
        ])
        ->withCount('childs')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        // ->withCount('products')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();
        // dd($goods_categories);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('system.common.categories.index.categories_list',
                [
                    'items' => $goods_categories,
                    'entity' => $this->entityAlias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $goods_categories->count(),
                    'id' => $request->id,
                    // 'nested' => 'goods_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.categories.index.index',
            [
                'items' => $goods_categories,
                'pageInfo' => pageInfo($this->entityAlias),
                'entity' => $this->entityAlias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'childs_count',
                'filter' => setFilter($this->entityAlias, $request, [
                    'booklist'
                ]),
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), GoodsCategory::class);

        return view('system.common.categories.create.modal.create', [
            'item' => GoodsCategory::make(),
            'entity' => $this->entityAlias,
            'title' => 'Добавление категории товаров',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param GoodsCategoryStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(GoodsCategoryStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), GoodsCategory::class);

        $data = $request->input();
        $goods_category = GoodsCategory::create($data);

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $goods_category = GoodsCategory::with([
            // 'mode',
            // 'one_metrics' => function ($q) {
            //     $q->with('unit', 'values');
            // },
            // 'set_metrics' => function ($q) {
            //     $q->with('unit', 'values');
            // },
            'metrics' => function ($q) {
                $q->with([
                    'unit',
                    'values'
                ]);
            },
            'raws' => function ($q) {
                $q->with([
                    'category',
                    'article.unit'
                ]);
            },
            'related' => function ($q) {
                $q->with([
                    'category',
                    'article.unit'
                ]);
            },
            // 'compositions.product.unit',
            // 'compositions',
            'manufacturers',
            'direction'
        ])
        ->withCount([
            // 'one_metrics',
            // 'set_metrics',
            'metrics',
            // 'compositions'
        ])
        ->moderatorLimit($answer)
        ->find($id);
//         dd($goods_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_category);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        $settings = getPhotoSettings($this->entityAlias);

        // При добавлении метрики отдаем ajax новый список свойст и метрик
        if ($request->ajax()) {
            return view('products.common.metrics.properties_list', [
                'category' => $goods_category,
                'pageInfo' => $pageInfo,
            ]);
        }

        // dd($goods_category->direction);
        return view('products.articles_categories.common.edit.edit', [
            'title' => 'Редактирование категории товаров',
            'category' => $goods_category,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entityAlias,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GoodsCategoryUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(GoodsCategoryUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $goods_category = GoodsCategory::moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_category);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($goods_category);
        $result = $goods_category->update($data);

        if ($result) {

            $goods_category->manufacturers()->sync($request->manufacturers);
            $metrics = session('access.all_rights.index-metrics-allow');
            if ($metrics) {
                $goods_category->metrics()->sync($request->metrics);
            }

            $raws = session('access.all_rights.index-raws-allow');
            if ($raws) {
                $goods_category->raws()->sync($request->raws);
            }

            // TODO - 12.05.12 - В дальнейшем может понадобиться проверка на архивность товаров (если в процессе работы кто то внес их в архив)
            $raws = session('access.all_rights.index-goods-allow');
            if ($raws) {
                $goods_category->related()->sync($request->related);
            }

            // Переадресовываем на index
            return redirect()->route('goods_categories.index', ['id' => $goods_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории продукции!'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $goods_category = GoodsCategory::with([
            'childs',
            'goods'
        ])
        ->moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_category);

        // Скрываем бога
        $goods_category->editor_id = hideGod($request->user());
        $goods_category->save();

        // Удаляем папку категории и все ее отношения
        // if (isset($goods_category->photo_id)) {
        //     $goods_category->photo()->delete();
        // }

        // $goods_category->compositions()->detach();
        // $goods_category->one_metrics()->detach();
        // $goods_category->set_metrics()->detach();

        // // Удаляем папку
        // $directory = $goods_category->company_id . '/media/' . $this->entityAlias . '/' . $goods_category->id;
        // $del_dir = Storage::disk('public')->deleteDirectory($directory);

        $parent_id = $goods_category->parent_id;

        $goods_category->delete();

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
}
