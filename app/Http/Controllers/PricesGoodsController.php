<?php

namespace App\Http\Controllers;

use App\CatalogsGoodsItem;
use App\Discount;
use App\Exports\PricesGoodsExport;
use App\Http\Controllers\System\Traits\Discountable;
use App\PricesGoods;
use App\CatalogsGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Maatwebsite\Excel\Facades\Excel;

class PricesGoodsController extends Controller
{
    /**
     * PricesGoodsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entity_alias = with(new PricesGoods)->getTable();
        $this->entity_dependence = true;
        $this->class = PricesGoods::class;
        $this->model = 'App\PricesGoods';
    }

    use Discountable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $catalogId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $catalogId)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);

        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
            return Redirect($filter_url);
        }

        $catalogGoods = CatalogsGoods::with([
            'filials'
        ])
            ->find($catalogId);

        if ($request->has('filial_id')) {
            $filialId = $request->filial_id;
        } else {
            if ($catalogGoods->filials->isNotEmpty()) {
                $filialId = $catalogGoods->filials->first()->id;
            } else {
                $user_filials = session('access.all_rights.index-prices_goods-allow.filials');

                if (!is_null($user_filials)) {
                    $filialId = key($user_filials);
                } else {
                    $filialId = null;
                }
            }
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $prices_goods = PricesGoods::with([
            'goods' => function ($q) {
                $q->with([
                    'article' => function ($q) {
                        $q->with([
                           'unit',
                           'group.unit'
                        ]);
                    }
                ]);
            },
            'catalog',
            'catalogs_item',
            'currency',
            'discount_price',
            'discount_catalogs_item'
        ])
            ->withCount('likes')
            // ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filter()

            ->booklistFilter($request)

//            ->whereHas('catalogs_item', function($q) use ($request){
//                $q->filter($request, 'author_id');
//            })
//
//            ->whereHas('catalogs_item', function($q) use ($request){
//                $q->filter($request, 'catalogs_goods_item_id');
//            })

            ->whereHas('goods', function($q){
                $q->whereHas('article', function ($q) {
                    $q->where('draft', false);
                })
                    ->where('archive', false);
            })

            // ->filials($answer)
            // ->authors($answer)
            // ->systemItem($answer)
            ->where([
                'archive' => false,
                'catalogs_goods_id' => $catalogId,
                'filial_id' => $filialId,
            ])
            ->orderBy('sort')
            ->paginate(300);
//         dd($prices_goods);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',                               // Автор записи
            'booklist',                             // Списки пользователя
            'catalogs_goods_items'                  // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);



        $pageInfo->title = 'Прайс: ' . $catalogGoods->name;
        $pageInfo->name = 'Прайс: ' . $catalogGoods->name;

        return view('system.pages.catalogs.goods.prices_goods.index', [
            'prices_goods' => $prices_goods,
            'pageInfo' => $pageInfo,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'filter' => $filter,
            'nested' => null,
            'filial_id' => $filialId,
            'catalogGoods' => $catalogGoods
        ]);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PricesGoods $pricesGoods)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param $catalogId
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Request $request, $catalogId, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $priceGoods = PricesGoods::with([
            'goods.article',
            'currency',
            'discounts',
            'discounts_actual'
        ])
        ->moderatorLimit($answer)
            ->find($id);
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $priceGoods);

        $catalogGoods = CatalogsGoods::find($catalogId);

        return view('system.pages.catalogs.goods.prices_goods.edit', [
            'priceGoods' => $priceGoods,
            'catalogId' => $catalogId,
            'pageInfo' => pageInfo($this->entity_alias),
            'catalogGoods' => $catalogGoods
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $catalogId
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, $catalogId, $id)
    {
        $priceGoods = PricesGoods::find($id);

        $price = $request->price;

        if ($priceGoods->price != $price) {

            $priceGoods->actual_price->update([
                'end_date' => now(),
            ]);

            $priceGoods->history()->create([
                'price' => $price,
            ]);

            $priceGoods->update([
                'price' => $price,
            ]);
        }

        $data = $request->input();

        if ($request->ajax()) {
            if ($priceGoods->is_discount == 1) {
                $discountPrice = $priceGoods->discounts_actual->first();
                $data['price_discount_id'] = $discountPrice->id ?? null;

                $discountCatalogsItem = $priceGoods->catalogs_item->discounts_actual->first();
                $data['catalogs_item_discount_id'] = $discountCatalogsItem->id ?? null;
            } else {
                $data['price_discount_id'] = null;
                $data['catalogs_item_discount_id'] = null;
            }
        } else {
            $priceGoods->discounts()->sync($request->discounts);

            if ($request->is_discount == 1) {
                $priceGoods->load([
                    'discounts_actual',
                    'catalogs_item.discounts_actual'
                ]);

                $discountPrice = $priceGoods->discounts_actual->first();
                $data['price_discount_id'] = $discountPrice->id ?? null;

                $discountCatalogsItem = $priceGoods->catalogs_item->discounts_actual->first();
                $data['catalogs_item_discount_id'] = $discountCatalogsItem->id ?? null;
            } else {
                $data['price_discount_id'] = null;
                $data['catalogs_item_discount_id'] = null;
            }
        }

        $priceGoods->update($data);

        // Отдаем Ajax
        if ($request->ajax()) {
            $priceGoods = PricesGoods::with([
                'catalog',
                'catalogs_item.parent.parent',
                'filial',
                'currency'
            ])
            ->find($id);

            return response()->json($priceGoods);
        }

        return redirect()->route('prices_goods.index', $catalogId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Архивирование
     *
     * @param Request $request
     * @param $catalogId
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(Request $request, $catalogId, $id)
    {
        $user = $request->user();

        $price_goods = PricesGoods::find($id);

        $filial_id = $price_goods->filial_id;

        $result = $price_goods->update([
            'archive' => true,
            'editor_id' => hideGod($user)
        ]);

        if ($result) {
            // Переадресовываем на index
            return redirect()->route('prices_goods.index', [
                'catalogId' => $catalogId,
                'filial_id' => $filial_id
            ]);
        } else {
            abort(403, __('errors.archive'));
        }
    }

    /**
     * Поиск
     *
     * @param $catalogId
     * @param $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($catalogId, $search)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('index'));

        $results = $this->class::with([
            'goods.article.manufacturer.company'
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->where('catalogs_goods_id', $catalogId)
            ->whereHas('goods', function ($q) use ($search) {
                $q->whereHas('article', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                });
            })
            ->where('archive', false)
            ->get();

        // Модифицируем данные
        $modified = $results->map(function($value, $key) {
            $value['total'] = num_format($value['total'], 0);
            // $value['created_at'] = Carbon::parse($value['created_at']);
            return $value;
        });

        $results = $modified->all();

        return response()->json($results);
    }

    /**
     * Выгрузка прайсов товаров в excel (с учетом фильтра)
     *
     * @param $catalogId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function excelExport($catalogId)
    {
        return Excel::download(new PricesGoodsExport($catalogId), 'Товары.xlsx');
    }


    // --------------------------------- Ajax ----------------------------------------

    public function sync(Request $request, $catalogId)
    {

        $prices_ids = array_keys($request->prices);

        $filial_id = $request->filial_id;

        $prices_services = PricesGoods::with(['follower' => function ($q) use ($filial_id) {
            $q->where('filial_id', $filial_id);
        }])
            ->find($prices_ids)
            ->keyBy('id');

        foreach ($request->prices as $id => $price) {
            $cur_price_goods = $prices_services[$id];

            // Если не пустая цена
            if (!is_null($price)) {

                // Если есть последователь
                if (!is_null($cur_price_goods->follower)) {

                    // Сравниваем цену
                    if ($price != $cur_price_goods->follower->price) {
                        $new_cur_price_goods = $cur_price_goods->follower->replicate();
                        $cur_price_goods->follower->update([
                            'archive' => true
                        ]);

                        $new_cur_price_goods->price = $price;
                        $new_cur_price_goods->save();
                    }
                } else {
                    // Если последователя нет, то создаем
                    $sync_cur_price_goods = $cur_price_goods->replicate();

                    $sync_cur_price_goods->ancestor_id = $cur_price_goods->id;
                    $sync_cur_price_goods->price = $price;
                    $sync_cur_price_goods->filial_id = $filial_id;
                    $sync_cur_price_goods->save();
                }
            } else {
                // Если цена пустая
                // Если есть последователь, то архивируем
                if (!is_null($cur_price_goods->follower)) {
                    $cur_price_goods->follower->update([
                        'archive' => true
                    ]);
                }
            }
        }

        // Переадресовываем на index
        return redirect()->route('prices_goods.index', [
            'catalogId' => $catalogId,
            'filial_id' => $filial_id
        ]);
    }

    public function ajax_get(Request $request, $catalogId, $id)
    {
        $cur_price_goods = PricesGoods::find($id);
        // dd($price);
        $price = $cur_price_goods->price;
        // dd($price);
        return view('products.articles.goods.prices.catalogs_item_price', compact('price'));
    }

    public function ajax_store(Request $request)
    {
//        $priceGoods = PricesGoods::firstOrNew([
//            'catalogs_goods_item_id' => $request->catalogs_goods_item_id,
//            'catalogs_goods_id' => $request->catalogs_goods_id,
//            'goods_id' => $request->goods_id,
//            'filial_id' => $request->filial_id,
//            'currency_id' => $request->currency_id,
//        ], [
//            'price' => $request->price,
//            'discount_mode' => 1,
//            'discount_percent' => 0,
//            'discount_currency' => 0,
//        ]);

        $priceGoods = PricesGoods::where([
            'catalogs_goods_item_id' => $request->catalogs_goods_item_id,
            'catalogs_goods_id' => $request->catalogs_goods_id,
            'goods_id' => $request->goods_id,
            'filial_id' => $request->filial_id,
            'currency_id' => $request->currency_id,
        ])
        ->first();

        $catalogsGoodsItem = CatalogsGoodsItem::find($request->catalogs_goods_item_id);

        $discountCatalogsItemId = null;
        if ($catalogsGoodsItem) {
            $discountCatalogsItem = $catalogsGoodsItem->discounts_actual->first();

            if ($discountCatalogsItem) {
                $discountCatalogsItemId = $discountCatalogsItem->id;
            }
        }

        $discountEstimate = Discount::where([
            'company_id' => auth()->user()->company_id,
            'archive' => false
        ])
            ->whereHas('entity', function ($q) {
                $q->where('alias', 'estimates');
            })
            ->where('begined_at', '<=', now())
            ->where(function ($q) {
                $q->where('ended_at', '>=', now())
                    ->orWhereNull('ended_at');
            })
            ->first();

        $discountEstimateId = $discountEstimate->id ?? null;

        if ($priceGoods) {

            if ($priceGoods->price != $request->price) {
                $priceGoods->actual_price->update([
                    'end_date' => now(),
                ]);
                $priceGoods->history()->create([
                    'price' => $request->price,
                    'currency_id' => $priceGoods->currency_id,
                ]);
            }

            $priceGoods->update([
                'price' => $request->price,
                'is_discount' => 1,
                'catalogs_item_discount_id' => $discountCatalogsItemId,
                'estimate_discount_id' => $discountEstimateId,
                'archive' => false
            ]);

        } else {
            $data = $request->input();
            $data['is_discount'] = 1;
            $data['catalogs_item_discount_id'] = $discountCatalogsItemId;
            $data['estimate_discount_id'] = $discountEstimateId;
//            return $data;
            $priceGoods = PricesGoods::create($data);
        }

        $priceGoods->load([
            'catalog',
            'catalogs_item.parent.parent',
            'filial',
            'currency'
        ]);

        return response()->json($priceGoods);
    }

    public function ajax_edit(Request $request, $catalogId)
    {
        $price = PricesGoods::find($request->id);
        // dd($price);
        return view('products.articles.goods.prices.catalogs_item_edit', compact('price'));
    }

    public function ajax_update(Request $request, $catalogId)
    {
        $cur_price_goods = PricesGoods::find($request->id);

        if ($cur_price_goods->price != $request->price) {

            $cur_price_goods->actual_price->update([
                'end_date' => Carbon::now(),
            ]);

            $cur_price_goods->history()->create([
                'price' => $request->price,
                'currency_id' => $cur_price_goods->currency_id,
            ]);

            $cur_price_goods->update([
                'price' => $request->price,
            ]);
        }

        return response()->json($cur_price_goods);
//        return view('products.articles.goods.prices.price', compact('cur_price_goods'));
    }

    public function ajax_archive(Request $request)
    {
        $user = $request->user();

        $result = PricesGoods::find($request->id)
            ->update([
                'archive' => true,
                'editor_id' => hideGod($user)
            ]);
        return response()->json($result);
    }

	public function ajax_status(Request $request)
	{
		$result = PricesGoods::find($request->id)->update([
			'status' => $request->status
		]);
		return response()->json($result);
	}

    public function ajax_hit(Request $request)
    {
        $result = PricesGoods::find($request->id)->update([
            'is_hit' => $request->is_hit
        ]);
        return response()->json($result);
    }

    public function ajax_new(Request $request)
    {
        $result = PricesGoods::find($request->id)->update([
            'is_new' => $request->is_new
        ]);
        return response()->json($result);
    }
}
