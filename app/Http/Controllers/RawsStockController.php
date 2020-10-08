<?php

namespace App\Http\Controllers;

use App\RawsStock;
use Illuminate\Http\Request;

class RawsStockController extends Controller
{

    // Настройки сконтроллера
    public function __construct(RawsStock $raws_stock)
    {
        $this->middleware('auth');
        $this->raws_stock = $raws_stock;
        $this->class = RawsStock::class;
        $this->model = 'App\RawsStock';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $stocks = RawsStock::with([
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
        // dd($stocks);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);
//        dd($pageInfo);

        return view('system.common.stocks.index', compact('stocks', 'pageInfo', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

    }

    public function edit($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $stock = RawsStock::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->find($id);
//        dd($raws_stock);

        $this->authorize(getmethod(__FUNCTION__), $stock);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);
        // dd($pageInfo);

        return view('system.common.stocks.edit', compact('stock', 'pageInfo', 'filter'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
