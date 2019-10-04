<?php

namespace App\Http\Controllers;

use App\ContainersStock;
use Illuminate\Http\Request;

class ContainersStockController extends Controller
{

    // Настройки сконтроллера
    public function __construct(ContainersStock $containers_stock)
    {
        $this->middleware('auth');
        $this->containers_stock = $containers_stock;
        $this->class = ContainersStock::class;
        $this->model = 'App\ContainersStock';
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
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
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
            ->systemItem($answer)
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc')
            ->paginate(30);
//         dd($stocks);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
//        dd($page_info);

        return view('system.common.stocks.index', compact('stocks', 'page_info', 'filter'));
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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