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
        $price_model = 'App\\' . $entity->model;
        // dd($entity);

        $price = $price_model::findOrFail($request->id);
        $price->load('product');

        $entity = Entity::where('alias', $price->product->getTable())->first();
        $product_model = 'App\\' . $entity->model;

        // dd($price_model);

        $estimates_item = new EstimatesItem;

        $estimates_item->product_id = $price->product->id;
        $estimates_item->product_type = $product_model;

        $estimates_item->price_product_id = $price->id;
        $estimates_item->price_product_type = $price_model;

        $estimates_item->estimate_id = $estimate->id;

        $estimates_item->company_id = $company_id;
        $estimates_item->author_id = $user_id;

        $estimates_item->price = $price->price;
        $estimates_item->count = 1;

        $estimates_item->sum = $estimates_item->count * $price->price;

        $estimates_item->save();

        $estimates_item->load('product');

        if ($price_model == 'App\PricesService') {
            $estimates_item->load('product.process');
            return view('leads.estimates_item_service', compact('estimates_item'));
        } else {
            $estimates_item->load('product.article');
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
        $price_model = 'App\\' . $entity->model;

        // dd($entity);

        $price = $price_model::findOrFail($request->id);
        $price->load('product');

        $entity = Entity::where('alias', $price->product->getTable())->first();
        $product_model = 'App\\' . $entity->model;
        // dd($product_model);

        $estimates_item = EstimatesItem::firstOrNew([
            'estimate_id' => $estimate->id,
            'product_id' => $price->product->id,
            'product_type' => $product_model,
            'price_product_id' => $price->id,
            'price_product_type' => $price_model,
            'price' => $price->price,
            'company_id' => $company_id,
        ], [
            'author_id' => $user_id,
        ]);
        // dd($estimates_item);

        $estimates_item->count = $estimates_item->count + 1;
        $estimates_item->sum = $estimates_item->count * $price->price;

        // dd($estimates_item);

        $estimates_item->save();

        if ($price_model == 'App\PricesService') {
            $estimates_item->load('product.process');
            return view('leads.estimates_item_service', compact('estimates_item'));
        } else {
            $estimates_item->load('product.article');
            return view('leads.estimates_item_goods', compact('estimates_item'));
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
}
