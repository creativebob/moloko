<?php

namespace App\Http\Controllers;

use App\Discount;
use App\Http\Controllers\System\Traits\Discountable;
use App\Http\Controllers\System\Traits\Timestampable;
use App\Http\Requests\System\DiscountRequest;
use App\Notifications\System\Notifications;
use Illuminate\Http\Request;

class DiscountController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * DiscountController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'discounts';
        $this->entityDependence = true;
    }

    use Timestampable;
    use Discountable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Discount::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

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
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.discounts.index', compact('discounts', 'pageInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Discount::class);

        $discount = Discount::make();

        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.discounts.create', compact('discount', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DiscountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(DiscountRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Discount::class);

        $data = $request->input();

        $beginedAt = $this->getTimestamp('begin', true);
        $data['begined_at'] = $beginedAt;

        $endedAt = $this->getTimestamp('end');
        $data['ended_at'] = $endedAt;

        $discount = Discount::create($data);

        if ($discount) {
            return redirect()->route('discounts.index');
        } else {
            abort(403, __('errors.store'));
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
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $discount = Discount::moderatorLimit($answer)
            ->find($id);
//        dd($discount);

        if (empty($discount)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $discount);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.discounts.edit', compact('discount', 'pageInfo'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DiscountRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(DiscountRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $discount = Discount::moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $discount);

        $data = $request->input();

//        $beginedAt = $this->getTimestamp('begin', true);
//        $data['begined_at'] = $beginedAt;
//        $endedAt = $this->getTimestamp('end');
//        $data['ended_at'] = $endedAt;

        $result = $discount->update($data);

        if ($result) {
            return redirect()->route('discounts.index');
        } else {
            abort(403, 'Ошибка обновления');
        }
    }

    /**
     * Remove the specified resource from storage.
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
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $discount = Discount::with('entity')
        ->moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $discount);

        $discount->update([
            'archive' => true,
            'is_actual' => false
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
                        'is_need_recalculate' => true
//                        'price_discount_id' => null
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
                        'is_need_recalculate' => true
//                        'catalogs_item_discount_id' => null
                    ]);
                }
                break;

            case('estimates'):
                $discount->load([
                    'estimates_prices_goods_actual'
                ]);

                $pricesGoods = $discount->estimates_prices_goods_actual;

                foreach($pricesGoods as $priceGoods) {
                    $priceGoods->update([
                        'is_need_recalculate' => true
//                        'estimate_discount_id' => null
                    ]);
                }
                break;
        }

        // Сообщение
        $message = "ИЗМЕНЕНИЯ НА СКИДКАХ\r\n\r\n";
        $message .= "{$discount->name} ";
        $message .= ($discount->mode == 1) ? "({$discount->percent}%)" : "({$discount->cyrrency} руб.)";
        $message .= ' ';
        $message .= ' - архивирована';
        $message .= "\r\n";

        $message .= "\r\n";
        $message .= "Затронуто позиций: " . $pricesGoods->count() .  " шт.";

        // отправляем мессагу подписанным
        Notifications::sendNotification(5, $message, $discount->company_id);

        if ($discount) {
            return redirect()->route('discounts.index');
        } else {
            abort(403, 'Ошибка при архивации');
        }
    }
}
