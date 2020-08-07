<?php

namespace App\Http\Controllers;

use App\AttachmentsStock;
use Illuminate\Http\Request;

class AttachmentsStockController extends Controller
{

    // Настройки сконтроллера
    public function __construct(AttachmentsStock $attachments_stock)
    {
        $this->middleware('auth');
        $this->attachments_stock = $attachments_stock;
        $this->class = AttachmentsStock::class;
        $this->model = 'App\AttachmentsStock';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = true;
    }

    /**
     * Отображение списка ресурсов.
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
        $page_info = pageInfo($this->entity_alias);
//        dd($page_info);

        return view('system.common.stocks.index', compact('stocks', 'page_info', 'filter'));
    }

    /**
     * Показать форму для создания нового ресурса.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Сохранение созданного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Отображение указанного ресурса.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
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
     * Удаление указанного ресурса из хранилища.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
