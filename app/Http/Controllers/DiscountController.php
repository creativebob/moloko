<?php

namespace App\Http\Controllers;

use App\Discount;
use App\Http\Controllers\System\Traits\Discountable;
use App\Http\Controllers\System\Traits\Timestampable;
use App\Http\Requests\System\DiscountRequest;
use Illuminate\Http\Request;

class DiscountController extends Controller
{

    /**
     * DiscountController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->class = Discount::class;
        $this->model = 'App\Discount';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = true;
    }

    use Timestampable;
    use Discountable;

    /**
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $discounts = Discount::with([
            'author',
            'company',
            'entity'
        ])
            ->where('archive', false)
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
//        ->template($answer)
//            ->booklistFilter($request)
//            ->filter($request, 'company_id')
            ->orderByDesc('moderation')
            ->orderBy('sort')
            ->paginate(30);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('system.pages.marketings.discounts.index', compact('discounts', 'pageInfo'));
    }

    /**
     * Показать форму для создания нового ресурса.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $discount = Discount::make();

        $pageInfo = pageInfo($this->entity_alias);

        return view('system.pages.marketings.discounts.create', compact('discount', 'pageInfo'));
    }

    /**
     * Сохранение только что созданного ресурса в хранилище.
     *
     * @param DiscountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(DiscountRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();

        $beginedAt = $this->getTimestamp('begin', true);
        $data['begined_at'] = $beginedAt;

        $endedAt = $this->getTimestamp('end');
        $data['ended_at'] = $endedAt;

        $discount = Discount::create($data);

        if ($discount) {
            return redirect()->route('discounts.index');
        } else {
            abort(403, 'Ошибка записи');
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
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $discount = Discount::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $discount);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('system.pages.marketings.discounts.edit', compact('discount', 'pageInfo'));
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param DiscountRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(DiscountRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $discount = Discount::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $discount);

        $data = $request->input();

        $beginedAt = $this->getTimestamp('begin', true);
        $data['begined_at'] = $beginedAt;
        $endedAt = $this->getTimestamp('end');
        $data['ended_at'] = $endedAt;

        $result = $discount->update($data);

        if ($result) {
            return redirect()->route('discounts.index');
        } else {
            abort(403, 'Ошибка обновления');
        }
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Архивация указанного ресурса.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function archive($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $discount = Discount::with('entity')
        ->moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $discount);

        $discount->update([
            'archive' => true
        ]);

        switch($discount->entity->alias) {
            case('prices_goods'):
                $discount->load([
                    'prices_goods_actual'
                ]);

                $pricesGoods = $discount->prices_goods_actual;

                $discount->prices_goods()->detach();

                foreach($pricesGoods as $priceGoods) {
                    $priceGoods->update([
                        'price_discount_id' => null
                    ]);
                }
                break;

            case('catalogs_goods_items'):
                $discount->load([
                    'catalogs_goods_items_prices_goods_actual'
                ]);

                $pricesGoods = $discount->catalogs_goods_items_prices_goods_actual;

                $discount->catalogs_goods_items()->detach();

                foreach($pricesGoods as $priceGoods) {
                    $priceGoods->update([
                        'catalogs_item_discount_id' => null
                    ]);
                }
                break;

//                $discount->load([
//                    'catalogs_goods_items' => function ($q) {
//                        $q->with([
//                            'prices_goods_actual'
//                        ]);
//                    }
//                ]);
//                $catalogsGoodsItems = $discount->catalogs_goods_items;
//

//
//                foreach($catalogsGoodsItems as $catalogsGoodsItem) {
//                    foreach($catalogsGoodsItem->prices_goods_actual as $priceGoods) {
//                        $priceGoods->update([
//                            'catalogs_item_discount_id' => null
//                        ]);
//                    }
//                }
                break;

            case('estimates'):
                $discount->load([
                    'estimates_prices_goods_actual'
                ]);

                $pricesGoods = $discount->estimates_prices_goods_actual;

                foreach($pricesGoods as $priceGoods) {
                    $priceGoods->update([
                        'estimate_discount_id' => null
                    ]);
                }
                break;
        }

        if ($discount) {
            return redirect()->route('discounts.index');
        } else {
            abort(403, 'Ошибка при архивации');
        }
    }
}
