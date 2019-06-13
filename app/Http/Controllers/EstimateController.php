<?php

namespace App\Http\Controllers;

// Модели
use App\Estimate;
use App\EstimatesItem;

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

        // Получаем авторизованного пользователя
        $user = $request->user();

        $user_id = hideGod($user);
        $company_id = $user->company_id;

        $lead = Lead::findOrFail($request->lead_id);

        // Находим или создаем заказ для лида
        $estimate = Estimate::firstOrCreate([
            'lead_id' => $lead->id,
            'company_id' => $company_id
        ], [
            'client_id' => $lead->client_id,
            'author_id' => $user_id
        ]);
        // dd($estimate);

        // Находим сущность
        $entity = Entity::where('alias', $request->entity)->first();
        // dd($entity);

        $estimates_item = new EstimatesItem;
        $estimates_item->price_id = $request->id;
        $estimates_item->price_type = 'App\\' . $entity->model;
        $estimates_item->estimate_id = $estimate->id;
        $estimates_item->company_id = $company_id;
        $estimates_item->author_id = $user_id;
        $estimates_item->count = 1;
        $estimates_item->save();

        if ($entity->model == 'PricesService') {
            $estimates_item->load('price.service.process');
            return view('leads.estimates_item_service', compact('estimates_item'));
        } else {
            $estimates_item->load('price.goods.article');
            return view('leads.estimates_item_goods', compact('estimates_item'));
        }
    }

    public function ajax_update(Request $request)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        $user_id = hideGod($user);
        $company_id = $user->company_id;

        // Находим или создаем заказ для лида
        $estimate = Estimate::firstOrCreate([
            'lead_id' => $request->lead_id,
            'company_id' => $company_id
        ], [
            'author_id' => $user_id
        ]);
        // dd($estimate);

        // Находим сущность
        $entity = Entity::where('alias', $request->entity)->first();
        // dd($entity);

        $estimates_item = EstimatesItem::firstOrNew([
            'estimate_id' => $estimate->id,
            'price_id' => $request->id,
            'price_type' => 'App\\' . $entity->model,
            'company_id' => $company_id,
        ], [
            'author_id' => $user_id,
        ]);

        $estimates_item->count = $estimates_item->count + 1;

        // dd($estimates_item);

        if (isset($estimates_item->id)) {
            $estimates_item->save();
            // dd('число');

            $estimates_item->load('price.service.process');
            return view('leads.estimates_item_service', compact('estimates_item'));
        } else {
            $estimates_item->save();
            // dd('html');
            if ($entity->model == 'PricesService') {
                $estimates_item->load('price.service.process');
                return view('leads.estimates_item_service', compact('estimates_item'));
            } else {
                $estimates_item->load('price.goods.article');
                return view('leads.estimates_item_goods', compact('estimates_item'));
            }

        }
        
        // $estimates_item->count->increment(1);

        
    }

    public function ajax_delete(Request $request)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        $user_id = hideGod($user);
        $company_id = $user->company_id;

        // $estimates_item = EstimatesItem::findOrFail($request->id);

        return response()->json(EstimatesItem::destroy($request->id));
        
    }

    // public function ajax_destroy_composition(Request $request, $id)
    // {

    //     return response()->json(Workflow::destroy($id));

    // }

    public function ajax_add(Request $request)
    {

        return response()->json(Workflow::destroy($id));

    }
}
