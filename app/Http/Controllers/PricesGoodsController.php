<?php

namespace App\Http\Controllers;

use App\PricesGoods;
use App\CatalogsGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PricesGoodsController extends Controller
{
    /**
     * PricesGoodsController constructor.
     * @param PricesGoods $prices_goods
     */
    public function __construct(PricesGoods $prices_goods)
    {
        $this->middleware('auth');
        $this->prices_goods = $prices_goods;
        $this->entity_alias = with(new PricesGoods)->getTable();
        $this->entity_dependence = true;
        $this->class = PricesGoods::class;
        $this->model = 'App\PricesGoods';
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $catalog_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $catalog_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);

        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
            return Redirect($filter_url);
        }

        $user_filials = session('access.all_rights.index-prices_goods-allow.filials');
//        $user_filials = session('access.all_rights.index-leads-allow');

        // dd($request);

        if (isset($request->filial_id)) {
            $filial_id = $request->filial_id;
        } else {
            if (!is_null($user_filials)) {
                $filial_id = key($user_filials);
            } else {
                $filial_id = null;
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
            'catalogs_item'
        ])
//        ->whereHas('service', function ($q) {
//            $q->whereHas('process', function ($q) {
//                $q->where('draft', false);
//            })
//            ->where('archive', false);
//        })
            // ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->booklistFilter($request)

            ->whereHas('catalogs_item', function($q) use ($request){
                $q->filter($request, 'author_id');
            })

            ->whereHas('catalogs_item', function($q) use ($request){
                $q->filter($request, 'catalogs_goods_item_id');
            })

            ->whereHas('goods.article', function($q){
                $q->where('draft', false)
                    ->where('archive', false);
            })

            // ->filials($answer)
            // ->authors($answer)
            // ->systemItem($answer)
            ->where([
                'archive' => false,
                'catalogs_goods_id' => $catalog_id,
                'filial_id' => $filial_id,
            ])
            ->orderBy('sort', 'asc')
            ->paginate(300);
        // dd($prices_goods);


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
        $page_info = pageInfo($this->entity_alias);

        $catalog = CatalogsGoods::findOrFail($catalog_id);
        $page_info->title = 'Прайс: ' . $catalog->name;
        $page_info->name = 'Прайс: ' . $catalog->name;

        return view('prices_goods.index', [
            'prices_goods' => $prices_goods,
            'page_info' => $page_info,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'filter' => $filter,
            'nested' => null,
            'catalog_id' => $catalog_id,
            'catalog' => $catalog,
            'filial_id' => $filial_id,
            'catalog_goods' => $catalog
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
     * @param $catalog_id
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $catalog_id, $id)
    {
        $price = PricesGoods::findOrFail($id);
        return view('prices_goods.price_edit', ['price' => $price->price]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $catalog_id
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, $catalog_id, $id)
    {
        $cur_price_goods = PricesGoods::findOrFail($id);

        if ($request->price) {

            $price = $request->price;
            if ($cur_price_goods->price == $price) {
                return view('prices_goods.price', ['cur_prices_goods' => $cur_price_goods]);
            } else {
                if ($cur_price_goods->price != $price) {

                    $cur_price_goods->actual_price->update([
                        'end_date' => now(),
                    ]);

                    $cur_price_goods->history()->create([
                        'price' => $price,
                    ]);

                    $cur_price_goods->update([
                        'price' => $price,
                    ]);
                }

                // dd($price);
                return view('prices_goods.price', ['cur_prices_goods' => $cur_price_goods]);
            }
        }

        if ($request->point) {
            $point = $request->point;
            $cur_price_goods->update([
                'point' => $point,
            ]);
            return view('prices_goods.price', ['cur_prices_goods' => $cur_price_goods]);
        }

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
     * @param $catalog_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(Request $request, $catalog_id, $id)
    {
        $user = $request->user();

        $price_goods = PricesGoods::findOrFail($id);

        $filial_id = $price_goods->filial_id;

        $result = $price_goods->update([
            'archive' => true,
            'editor_id' => hideGod($user)
        ]);

        if ($result) {
            // Переадресовываем на index
            return redirect()->route('prices_goods.index', [
                'catalog_id' => $catalog_id,
                'filial_id' => $filial_id
            ]);
        } else {
            abort(403, 'Ошибка архивирования');
        }
    }


    // --------------------------------- Ajax ----------------------------------------

    public function sync(Request $request, $catalog_id)
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
            'catalog_id' => $catalog_id,
            'filial_id' => $filial_id
        ]);
    }

    public function ajax_get(Request $request, $catalog_id, $id)
    {
        $cur_price_goods = PricesGoods::findOrFail($id);
        // dd($price);
        $price = $cur_price_goods->price;
        // dd($price);
        return view('products.articles.goods.prices.catalogs_item_price', compact('price'));
    }

    public function ajax_store(Request $request)
    {
        $cur_price_goods = PricesGoods::firstOrNew([
            'catalogs_goods_item_id' => $request->catalogs_goods_item_id,
            'catalogs_goods_id' => $request->catalogs_goods_id,
            'goods_id' => $request->goods_id,
            'filial_id' => $request->filial_id,
            'currency_id' => $request->currency_id,
        ], [
            'price' => $request->price
        ]);

        if ($cur_price_goods->id) {
            $cur_price_goods->update([
               'archive' => false
            ]);

            if ($cur_price_goods->price != $request->price) {

                $cur_price_goods->actual_price->update([
                    'end_date' => now(),
                ]);

                $cur_price_goods->history()->create([
                    'price' => $request->price,
                    'currency_id' => $cur_price_goods->currency_id,
                ]);

                $cur_price_goods->update([
                    'price' => $request->price,
                ]);
            }

        } else {
            $cur_price_goods->save();
        }


        return view('products.articles.goods.prices.price', compact('cur_price_goods'));
    }

    public function ajax_edit(Request $request, $catalog_id)
    {
        $price = PricesGoods::findOrFail($request->id);
        // dd($price);
        return view('products.articles.goods.prices.catalogs_item_edit', compact('price'));
    }

    public function ajax_update(Request $request, $catalog_id)
    {
        $cur_price_goods = PricesGoods::findOrFail($request->id);

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
        return view('products.articles.goods.prices.price', compact('cur_price_goods'));
    }

    public function ajax_archive(Request $request)
    {
        $user = $request->user();

        $result = PricesGoods::findOrFail($request->id)
            ->update([
                'archive' => true,
                'editor_id' => hideGod($user)
            ]);
        return response()->json($result);
    }

	public function ajax_status(Request $request)
	{
		$result = PricesGoods::findOrFail($request->id)->update([
			'status' => $request->status
		]);
		return response()->json($result);
	}

    public function ajax_hit(Request $request)
    {
        $result = PricesGoods::findOrFail($request->id)->update([
            'is_hit' => $request->is_hit
        ]);
        return response()->json($result);
    }

    public function ajax_new(Request $request)
    {
        $result = PricesGoods::findOrFail($request->id)->update([
            'is_new' => $request->is_new
        ]);
        return response()->json($result);
    }
}
