<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\CatalogsGoodsItemUpdateRequest;
use App\Http\Requests\CatalogsGoodsItemStoreRequest;
use App\CatalogsGoodsItem;
use Illuminate\Http\Request;


class CatalogsGoodsItemController extends Controller
{

    // Настройки сконтроллера
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

        // Отдаем на шаблон
        return view('catalogs_goods_items.index', [
            'catalogs_goods_items' => $catalogs_goods_items,
            'page_info' => pageInfo($this->entity_alias),
            'catalog_id' => $catalog_id,
        ]);
    }

    public function create(Request $request, $catalog_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);
        // dd('lol');

        return view('system.common.accordions.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление пункта каталога',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'catalog_id' => $catalog_id,
        ]);
    }

    public function store(CatalogsGoodsItemStoreRequest $request, $catalog_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $data['catalogs_goods_id'] = $catalog_id;
        $catalogs_goods_item = CatalogsGoodsItem::create($data);

        if ($catalogs_goods_item) {

            // Переадресовываем на index
            return redirect()->route('catalogs_goods_items.index', [$catalog_id, 'id' => $catalogs_goods_item->id]);

        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи пункта каталога!'
            ];

        }
    }


    public function show($id)
    {
        //
    }

    public function edit(Request $request, $catalog_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $catalogs_goods_item = CatalogsGoodsItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_goods_item);

        return view('catalogs_goods_items.edit', [
            'catalogs_goods_item' => $catalogs_goods_item,
            'catalog_id' => $catalog_id,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

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

            // Переадресовываем на index
            return redirect()->route('catalogs_goods_items.index', [$catalog_id, 'id' => $catalogs_goods_item->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлени пункта меню!'
            ];
        }
    }

    public function destroy(Request $request, $catalog_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalogs_goods_item = CatalogsGoodsItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalogs_goods_item);

        // Скрываем бога
        $catalogs_goods_item->editor_id = hideGod($request->user());
        $catalogs_goods_item->save();

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
