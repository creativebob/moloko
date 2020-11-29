<?php

namespace App\Http\Controllers;

use App\Models\System\Stocks\AttachmentsStock;
use Illuminate\Http\Request;

class AttachmentsStockController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * AttachmentsStockController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'attachments_stocks';
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
        $this->authorize(getmethod(__FUNCTION__), AttachmentsStock::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $stocks = AttachmentsStock::with([
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

        $stock = AttachmentsStock::with([
            'cmv.article'
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
}
