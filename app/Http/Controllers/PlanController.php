<?php

namespace App\Http\Controllers;

// Модели
use App\Plan;
use App\Entity;
use App\Indicator;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\PlanRequest;


class PlanController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Plan $plan)
    {
        $this->middleware('auth');
        $this->plan = $plan;
        $this->class = Plan::class;
        $this->model = 'App\Plan';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $entities = Entity::where('statistic', true)
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('plans.index', compact('entities', 'page_info', 'filter'));
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
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $alias)
    {

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), $this->class);

        $indicators = Indicator::with(['plans' => function ($q) {
            $q->where('year', 2019);
        }])
        ->whereHas('entity', function($q) use ($alias) {
            $q->whereAlias($alias);
        })->get();

        // dd($indicators);

        return view('plans.show', [
            'indicators' => $indicators,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit(Plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        //
    }

}
