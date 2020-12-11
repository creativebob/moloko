<?php

namespace App\Http\Controllers;

use App\Models\System\Stocks\GoodsStock;
use Illuminate\Http\Request;

class GoodsStockController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * GoodsStockController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'goods_stocks';
        $this->entityDependence = true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), GoodsStock::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));
        // dd($answer);

        $stocks = GoodsStock::with([
            'cmv.article.unit',
            'cmv.category',
            'company',
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer)
            ->systemItem($answer)
            ->filter()
            ->paginate(30);
//         dd($stocks);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.common.stocks.index', compact('stocks', 'pageInfo', 'filter'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $stock = GoodsStock::with([
            'cmv.article',
            'receipts' => function ($q) {
                $q->with([
                    'document',
                    'author'
                ]);
            },
            'offs' => function ($q) {
                $q->with([
                    'document',
                    'author'
                ]);
            },
            'reserves' => function ($q) {
                $q->with([
                    'document',
                    'author'
                ]);
            }
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($raws_stock);

        $this->authorize(getmethod(__FUNCTION__), $stock);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);
//         dd($pageInfo);

        return view('system.common.stocks.edit', compact('stock', 'pageInfo'));
    }

    /**
     * Поиск
     *
     * @param $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($search)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('index'));

        $items = GoodsStock::with([
            'cmv.article.manufacturer.company'
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->whereHas('cmv', function ($q) use ($search) {
                $q->whereHas('article', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                });
            })
            ->get();

        return response()->json($items);
    }
}
