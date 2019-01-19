<?php

namespace App\Http\Controllers;

// Модели
use App\Estimate;
use App\Workflow;
use App\Entity;

use Illuminate\Http\Request;

// Политика
// use App\Policies\NotePolicy;

class WorkflowController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'workflows';
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

        $workflow = Workflow::with('estimate')
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // $this->authorize(getmethod(__FUNCTION__), $order_composition);

        return view('leads.pricing.pricing-modal', compact('workflow'));

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
