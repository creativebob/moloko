<?php

namespace App\Http\Controllers;

use App\CatalogsGoods;
use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\CatalogsGoodsItemUpdateRequest;
use App\Http\Requests\CatalogsGoodsItemStoreRequest;
use App\CatalogsGoodsItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogsGoodsItemController extends Controller
{

    /**
     * CatalogsGoodsItemController constructor.
     * @param CatalogsGoodsItem $catalogs_goods_item
     */
    public function __construct(CatalogsGoodsItem $catalogs_goods_item)
    {
        $this->middleware('auth');
        $this->catalogs_goods_item = $catalogs_goods_item;
        $this->class = CatalogsGoodsItem::class;
        $this->model = 'App\CatalogsGoodsItem';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

    use Photable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $catalog_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $catalog_id)
    {
        
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $columns = [
            'id',
            'catalogs_goods_id',
            'name',
            'parent_id',
            'company_id',
            'sort',
            'display',
            'system',
            'moderation',
            'author_id'
        ];

        $catalogs_goods_items = CatalogsGoodsItem::with('childs')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->where('catalogs_goods_id', $catalog_id)
            ->orderBy('sort')
        ->get();
        // dd($catalogs_goods_items);

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.accordions.categories_list',
                [
                    'items' => $catalogs_goods_items,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $catalogs_goods_items->count(),
                    'id' => $request->id,
                    'nested' => 'childs_count',
                ]
            );
        }

        $catalog_goods = CatalogsGoods::findOrFail($catalog_id);

        // Стандартный шаблон для отображения
        $view_name = 'catalogs_goods_items.index';

        // Если передан аттрибут seo, то отдаем на другой шаблон
        if($request->seo == 'true'){$view_name = 'catalogs_goods_items.seo';}

        // Отдаем на шаблон
        return view($view_name, [
            'catalogs_goods_items' => $catalogs_goods_items,
            'page_info' => pageInfo($this->entity_alias),
            'id' => $request->id,
            'catalog_id' => $catalog_id,
            'catalog_goods' => $catalog_goods,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param $catalog_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request, $catalog_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.common.accordions.create', [
            'item' => CatalogsGoods::make(),
            'entity' => $this->entity_alias,
            'title' => 'Добавление пункта каталога',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'catalog_id' => $catalog_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CatalogsGoodsItemStoreRequest $request
     * @param $catalog_id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CatalogsGoodsItemStoreRequest $request, $catalog_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $data['catalogs_goods_id'] = $catalog_id;
        $catalogs_goods_item = CatalogsGoodsItem::create($data);

        if ($catalogs_goods_item) {

            // Переадресовываем на index
            return redirect()->route('catalogs_goods_items.index', ['catalog_id' => $catalog_id, 'id' => $catalogs_goods_item->id]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи пункта каталога!'
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param $catalog_id
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Request $request, $catalog_id, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs_goods_item = CatalogsGoodsItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_goods_item);

        $catalog_goods = CatalogsGoods::findOrFail($catalog_id);

        return view('catalogs_goods_items.edit', [
            'catalogs_goods_item' => $catalogs_goods_item,
            'catalog_id' => $catalog_id,
            'page_info' => pageInfo($this->entity_alias),
            'catalog_goods' => $catalog_goods
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CatalogsGoodsItemUpdateRequest $request
     * @param $catalog_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(CatalogsGoodsItemUpdateRequest $request, $catalog_id, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogs_goods_item = CatalogsGoodsItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_goods_item);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($request, $catalogs_goods_item);
        $result = $catalogs_goods_item->update($data);

        if ($result) {

            $catalogs_goods_item->filters()->sync($request->filters);

            // Переадресовываем на index
            return redirect()->route('catalogs_goods_items.index', ['catalog_id' => $catalog_id, 'id' => $catalogs_goods_item->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлени пункта меню!'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $catalog_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, $catalog_id, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogs_goods_item = CatalogsGoodsItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_goods_item);

        $parent_id = $catalogs_goods_item->parent_id;

        $catalogs_goods_item->delete();

        if ($catalogs_goods_item) {

            // Переадресовываем на index
            return redirect()->route('catalogs_goods_items.index', ['catalog_id' => $catalog_id, 'id' => $parent_id]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении!'
            ];
        }

    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    public function get_prices(Request $request)
    {

        $filial_id = $request->user()->filial_id;

        $catalogs_goods_item = CatalogsGoodsItem::with([
            'prices_goods' => function ($q) use ($filial_id) {
                $q->where('archive', false)
                    ->where('filial_id', $filial_id)
                    ->whereHas('goods', function ($q) {
                        $q->where('archive', false)
                            ->whereHas('article', function ($q) {
                                $q->where('draft', false);
                            });
                    });
            }
        ])
            ->findOrFail($request->id);
//         dd($catalogs_goods_item);

        return view('leads.catalogs.prices_goods', compact('catalogs_goods_item'));
    }

    public function ajax_get(Request $request, $catalog_id)
    {
        return view('products.articles.goods.prices.catalogs_items', compact('catalog_id'));
    }
}
