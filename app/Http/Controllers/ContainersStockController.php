<?php

namespace App\Http\Controllers;

use App\Models\System\Stocks\ContainersStock;
use Illuminate\Http\Request;

class ContainersStockController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * ContainersStockController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'containers_stocks';
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
        $this->authorize(getmethod(__FUNCTION__), ContainersStock::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $stocks = ContainersStock::with([
            'cmv.article.unit',
            'author',
            'company',
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer)
            ->systemItem($answer)
            // ->orderBy('moderation', 'desc')
//            ->orderBy('sort', 'asc')
            ->paginate(30);
//         dd($stocks);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.common.stocks.index', compact('stocks', 'pageInfo', 'filter'));
    }
}
