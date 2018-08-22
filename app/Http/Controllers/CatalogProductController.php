<?php

namespace App\Http\Controllers;

// Модели
use App\Catalog;

// Политика
use App\Policies\CatalogProductPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы 

// На удаление
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class CatalogProductController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'catalog_products';
    protected $entity_dependence = false;

    public function index()
    {
        // dd($alias);
        // Подключение политики
        // $this->authorize('index', CatalogProduct::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_catalogs = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $catalogs = Catalog::with(['services' => function ($query) {
            $query->with('services_article', 'catalogs');
        }])
        ->whereSite_id(2)
        ->paginate(30);

        dd($catalogs);

        $entity = $this->entity_name;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('catalog_products.index', compact('catalogs', 'page_info'));
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
