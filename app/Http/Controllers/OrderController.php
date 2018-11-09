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

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'orders';
    protected $entity_dependence = false;

    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Order::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $orders = Order::with('author')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($orders);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('orders.index', compact('orders', 'page_info'));
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
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
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
        $order = Order::firstOrCreate(['lead_id' => $request->lead_id, 'draft' => null, 'company_id' => $company_id], ['author_id' => $user_id]);
        // $order = Order::firstOrCreate(['lead_id' => 9443, 'draft' => null, 'company_id' => $company_id], ['author_id' => $user_id]);

        // Находим сущность, чтоб опрелделить модель
        $entity = Entity::where('alias', $request->entity)->first();
        // $entity = Entity::where('alias', 'goods')->first();

        $type = $request->entity;
        // $type = 'goods';

        // Формируем позицию заказа
        $composition = new OrderComposition;

        $composition->product_id = $request->item_id;
        // $composition->order_compositions_id = 1;
        $composition->product_type = 'App\\' . $entity->model;

        $composition->order_id = $order->id;
        $composition->company_id = $company_id;
        $composition->author_id = $user_id;
        $composition->count = 1;
        $composition->save();

        // dd($composition->product);

        // $composition->notes()->save($note);

        // $order->compositions()->associate($composition)->save();

        return view('leads.' . $type, compact('composition'));

    }

    public function ajax_destroy_composition(Request $request, $id)
    {
        

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        // $note = Note::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), $note);

        $order_composition = OrderComposition::findOrFail($id);

        // Удаляем ajax
        $order_composition->delete();

        if ($order_composition) {
            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении состава заказа!',
            ];
        }   

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
