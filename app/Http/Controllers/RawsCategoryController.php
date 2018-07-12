<?php

namespace App\Http\Controllers;

// Модели
use App\ProductsCategory;
use App\ProductsMode;
use App\Product;
// use App\Company;
// use App\Photo;
// use App\Album;
// use App\AlbumEntity;
// use App\Property;
// use App\Metric;
// use App\Article;
// use App\Value;
// use App\Booklist;
// use App\Entity;
// use App\List_item;
// use App\Unit;
// use App\UnitsCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ProductsCategoryRequest;

// Политика
use App\Policies\ProductsCategoryPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы 

// На удаление
use Illuminate\Support\Facades\Auth;

class RawsCategoryController extends Controller
{
    // Сущность над которой производит операции контроллер
    protected $entity_name = 'products_categories';
    protected $entity_dependence = false;
    protected $type = 'goods';

    public function index(Request $request)
    {
        // dd($alias);
        // Подключение политики
        $this->authorize('index', ProductsCategory::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------
        $products_categories = ProductsCategory::with('products')
        ->withCount('products')
        ->where('type', $this->type)
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get();
        // dd($products_categories);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем массив с вложенными элементами дял отображения дерева с правами, отдаем обьекты сущности и авторизованного пользователя
        $products_categories_tree = get_index_tree_with_rights($products_categories, $user);
        // dd($products_categories_tree);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('products_categories.category-list', ['products_categories_tree' => $products_categories_tree, 'id' => $request->id]);
        }

        // Инфо о странице
        $page_info = pageInfo($this->type.'_categories');
        // dd($page_info);

        return view('products_categories.index', ['products_categories_tree' => $products_categories_tree, 'page_info' => $page_info, 'type' => $this->type]);
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
