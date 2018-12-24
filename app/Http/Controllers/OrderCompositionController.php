<?php

namespace App\Http\Controllers;

// Модели
use App\Order;
use App\OrderComposition;
use App\Entity;

use Illuminate\Http\Request;

// Политика
// use App\Policies\NotePolicy;

class OrderCompositionController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'order_compositions';
    protected $entity_dependence = false;

    public function index(Request $request)
    {
        //
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

    public function ajax_edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('edit'));

        $order_composition = OrderComposition::with('order')
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // $this->authorize(getmethod(__FUNCTION__), $order_composition);

        return view('leads.pricing.pricing-modal', compact('order_composition'));

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


    }

}
