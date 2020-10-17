<?php

namespace App\Http\Controllers;

// Модели
use App\Order;
use App\OrderComposition;
use App\Entity;

use Illuminate\Http\Request;

// Политика
// use App\Policies\NotePolicy;

class OrderController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Order $order)
    {
        $this->middleware('auth');
        $this->order = $order;
        $this->class = Order::class;
        $this->model = 'App\Order';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Order::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $orders = Order::with('author')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->whereNull('draft')
        ->booklistFilter($request)  // Фильтр по спискам
        ->filter($request, 'client_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($orders);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            // 'client',               // Клиенты
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('orders.index', compact('orders', 'pageInfo', 'filter'));
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

    public function ajax_check(Request $request)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Получаем компанию
        $company_id = $user->company_id;

        // Находим или создаем заказ для лида
        $order = Order::firstOrCreate([
            'lead_id' => $request->lead_id,
            // 'draft' => 1,
            'company_id' => $company_id
        ], [
            'author_id' => $user_id
        ]);
        // $order = Order::firstOrCreate(['lead_id' => 9443, 'draft' => null, 'company_id' => $company_id], ['author_id' => $user_id]);

        // Находим сущность
        $entity = Entity::where('alias', $request->entity)->first();
        // $entity = Entity::where('alias', 'goods')->first();

        // Формируем позицию заказа
        $composition = new OrderComposition;

        $composition->product_id = $request->item_id;
        // $composition->order_compositions_id = 1;
        $composition->product_type = $entity->model;

        $composition->order_id = $order->id;
        $composition->company_id = $company_id;
        $composition->author_id = $user_id;
        $composition->count = 1;
        $composition->save();

        // dd($composition->product);

        // $composition->notes()->save($note);

        // $order->compositions()->associate($composition)->save();

        return view('leads.' . $entity->alias, compact('composition'));

    }

    public function ajax_destroy_composition(Request $request, $id)
    {

        return response()->json(OrderComposition::destroy($id));

    }
}
