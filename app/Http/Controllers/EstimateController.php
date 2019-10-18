<?php

namespace App\Http\Controllers;

// Модели
use App\Estimate;
use App\EstimatesGoodsItem;

use App\Lead;
use App\Entity;

use Illuminate\Http\Request;

// Политика
// use App\Policies\NotePolicy;

class EstimateController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Estimate $estimate)
    {
        $this->middleware('auth');
        $this->estimate = $estimate;
        $this->class = Estimate::class;
        $this->model = 'App\Estimate';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $estimates = Estimate::with([
            'client.clientable',
            'items',
            'author'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        // ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->where('draft', false)
        ->whereNotNull('client_id')
        ->booklistFilter($request)  // Фильтр по спискам
        ->filter($request, 'client_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($estimates);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            // 'client',               // Клиенты
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('estimates.index', compact('estimates', 'page_info', 'filter'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy(Request $request, $id)
    {
        //
    }

    public function ajax_create(Request $request)
    {

        $lead = Lead::findOrFail($request->lead_id);

        $estimate = Estimate::create([
            'lead_id' => $lead->id,
            'filial_id' => $lead->filial_id,
            'client_id' => $lead->client_id,
        ]);
        // dd($estimate);

        return response()->json($estimate->id);
    }


    public function ajax_delete(Request $request)
    {

        return response()->json(EstimatesGoodsItem::destroy($request->id));

    }
}
