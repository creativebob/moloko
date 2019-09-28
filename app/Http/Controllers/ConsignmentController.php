<?php

namespace App\Http\Controllers;

// Модели
use App\Consignment;

// Валидация
use App\Entity;
use Illuminate\Http\Request;
use App\Http\Requests\ConsignmentRequest;

// Карбон
use Carbon\Carbon;

class ConsignmentController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Consignment $consignment)
    {
        $this->middleware('auth');
        $this->consignment = $consignment;
        $this->class = Consignment::class;
        $this->model = 'App\Consignment';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = true;
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $consignments = Consignment::with('author')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        // ->whereNull('draft')
        ->booklistFilter($request)
        ->filter($request, 'supplier_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($consignments);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'supplier',             // Поставщики
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
        
        $class = $this->class;

        return view('system.pages.consignments.index', compact('consignments', 'page_info', 'filter', 'class'));
    }
    
    public function create()
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

                // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('consignments', false, 'index');

        // Главный запрос
        $consignment = new Consignment;
        $consignment->receipt_date = Carbon::now();
	    $consignment->draft = true;

        $user = \Auth::user();
	    $consignment->company_id = $user->company_id;
	    $consignment->author_id = hideGod($user);

	    $consignment->filial_id = $user->filial_id;

	    $consignment->save();
        // dd($consignment);

	    return redirect()->route('consignments.edit', ['id' => $consignment->id]);
    }


    public function store(ConsignmentRequest $request)
    {
        //
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $consignment = Consignment::
        with(['items.cmv' => function ($q) {
            $q->with([
                'article'
            ]);
        }])
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);
//        dd($consignment);

        $this->authorize(getmethod(__FUNCTION__), $consignment);

        // dd($consignment->items);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
	
	    $entity = 'raws';

        return view('system.pages.consignments.edit', compact('consignment', 'page_info', 'entity'));
    }


    public function update(ConsignmentRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $consignment = ContainersConsignment::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $consignment);

        $consignment->supplier_id = $request->supplier_id;

        $consignment->description = $request->description;
        $consignment->number = $request->number;
        $consignment->amount = $request->amount;

        // Дата приема
        $consignment->receipt_date = $request->has('draft') ? null : Carbon::parse($request->receipt_date)->format('Y-m-d');

        $consignment->draft = $request->draft;

        $consignment->editor_id = hideGod($request->user());
        $consignment->save();

        return redirect()->route('consignments.index');
    }


    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $consignment = ContainersConsignment::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $consignment);

        $consignment->editor_id = hideGod($request->user());
        $consignment->save();

        $consignment->delete();

        if ($consignment) {
            return redirect()->route('consignments.index');
        } else {
            abort(403, 'Ошибка при удалении товарной накладной');
        }

    }


    public function categories(Request $request)
    {

        $entity = Entity::find($request->entity_id);

        $entity_alias = $entity->alias;
        $alias = $entity_alias.'_categories';

        $entity_categories = Entity::whereAlias($alias)->first(['model']);
        $model = 'App\\'.$entity_categories->model;

        // Получаем из сессии необходимые данные
        $answer = operator_right($entity_categories->alias, false, 'index');

        $categories = $model::moderatorLimit($answer)
            ->companiesLimit($answer)
//            ->with([
//                $entity_alias.'.article:id,name'
//            ])
            ->with([
                $entity_alias => function ($q) {
                    $q->where('archive', false)
                        ->whereHas('article', function ($q) {
                            $q->where('draft', false)
                                ->select([
                                    'id',
                                    'name'
                                ]);
                        });
                }
            ])
            ->get([
                'id',
                'name',
                'parent_id',
            ]);
//		dd($categories);

        $categories_tree = buildTree($categories);
//        dd($categories);

        $items = [];
        foreach($categories as $category) {
            $category->entity_id = $entity->id;

            if (isset($category->$entity_alias)) {
                foreach ($category->$entity_alias as $item) {
                    $item->category_id = $category->id;
                    $item->entity_id = $entity->id;
                    $item->name = $item->article->name;
                    $items[] = $item;
                }
            }

            if (isset($category->childCategories)) {
                if (isset($category->$entity_alias)) {
                    foreach ($category->childCategories as $childCategory) {
                        foreach ($childCategory->$entity_alias as $item) {
                            $item->category_id = $category->id;
                            $item->entity_id = $entity->id;
                            $item->name = $item->article->name;
                            $items[] = $item;
                        }
                    }
                }
            }
        }
//        dd($items);

        $articles_categories_with_items_data = [
            'categories' => $categories_tree,
            'items' => $items
        ];

//        dd($articles_categories_with_items_data);


        return response()->json($articles_categories_with_items_data);
    }



}
