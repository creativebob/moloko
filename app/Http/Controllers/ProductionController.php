<?php

namespace App\Http\Controllers;

use App\Cost;
use App\Entity;
use App\Http\Requests\ProductionUpdateRequest;
use App\Off;
use App\Production;
use App\ProductionsItem;
use Illuminate\Http\Request;

class ProductionController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Production $production)
    {
        $this->middleware('auth');
        $this->production = $production;
        $this->class = Production::class;
        $this->model = 'App\Production';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $productions = Production::with('author')
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
//         dd($productions);

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

        return view('system.pages.productions.index', compact('productions', 'page_info', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod('store'), $this->class);

        $production = (new Production())->create();
        // dd($production);

        return redirect()->route('productions.edit', ['id' => $production->id]);
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
     * @param  \App\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $production = Production::with([
            'items' => function ($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'article.unit'
                        ]);
                    },
                    'entity:id,name,alias',
                ]);
            },
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->findOrFail($id);
//        dd($production);

        $this->authorize(getmethod(__FUNCTION__), $production);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('system.pages.productions.edit', compact('production', 'page_info'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function update(ProductionUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $production = Production::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $production);

        $data = $request->input();
        $production->update($data);


        return redirect()->route('productions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $production = Production::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $production);

        $production->delete();

        if ($production) {
            return redirect()->route('productions.index');
        } else {
            abort(403, 'Ошибка при удалении наряда на производство');
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
            ->with([
                $entity_alias => function ($q) {
                    $q->with([
                        'article' => function($q) {
                            $q->with([
                                'unit',
                                'manufacturer'
                            ]);
                        }
                    ])
                        ->where('archive', false)
                        ->whereHas('article', function ($q) {
                            $q->where('draft', false)
                                ->whereHas('manufacturer', function ($q) {
                                    $q->where('manufacturer_id', \Auth::user()->company_id);
                                })
                            ;
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
                    $items[] = $item;
                }
            }

            if (isset($category->childCategories)) {
                if (isset($category->$entity_alias)) {
                    foreach ($category->childCategories as $childCategory) {
                        foreach ($childCategory->$entity_alias as $item) {
                            $item->category_id = $category->id;
                            $item->entity_id = $entity->id;
                            $items[] = $item;
                        }
                    }
                }
            }
        }
//        dd($items);

        $data = [
            'categories' => $categories_tree,
            'items' => $items
        ];
//        dd($data);

        return response()->json($data);
    }

    public function produced(ProductionUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('update'));

        // ГЛАВНЫЙ ЗАПРОС:
        $production = Production::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('update'), $production);

        $data = $request->input();
        $production->update($data);

        $production->load([
            'items' => function($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'article' => function ($q) {
                                $q->with([
                                    'raws.cost',
                                    'containers.cost'
                                ]);
                            }
                        ]);
                    },
                    'entity'
                ]);
            },
        ]);
//		dd($production);

        if ($production->items->isNotEmpty()) {

            $grouped_items = $production->items->groupBy('entity.alias');
//			dd($grouped_items);

            foreach ($grouped_items as $alias => $items) {
                $entity = Entity::where('alias', $alias.'_stocks')->first();
                $model = 'App\\'.$entity->model;

                foreach ($items as $item) {

                    $price = 0;
                    $relations = [
                        'raws',
                        'containers'
                    ];

                    foreach ($relations as $relation_name) {
                        if ($item->cmv->article->$relation_name->isNotEmpty()) {

                            $entity_composition = Entity::where('alias', $relation_name)->first();
                            $model_composition = 'App\\'.$entity_composition->model;

                            foreach ($item->cmv->article->$relation_name as $composition) {
                                // Получаем себестоимость
                                $count = $composition->pivot->value;
                                $price += ($count * isset($composition->cost)? $composition->cost->average : 0);

                                // Списываем позицию состава
                                $entity_composition_stock = Entity::where('alias', $relation_name.'_stocks')->first();
                                $model_composition_stock = 'App\\'.$entity_composition_stock->model;

                                $stock_composition = $model_composition_stock::firstOrNew([
                                    'cmv_id' => $composition->id,
                                    'manufacturer_id' => $composition->article->manufacturer_id,
                                    'stock_id' => $production->stock_id,
                                ]);

                                $stock_composition->count -= $count * $item->count;
                                $stock_composition->weight -= ($composition->article->weight * $count * $item->count);
                                $stock_composition->volume -= ($composition->article->volume * $count * $item->count);
                                $stock_composition->save();

//                                dd($composition);
                                $off = Off::create([
                                    'document_id' => $production->id,
                                    'document_type' => 'App\Production',
                                    'cmv_id' => $composition->id,
                                    'cmv_type' => $model_composition,
                                    'count' => $count * $item->count,
                                    'average' => isset($composition->cost)? $composition->cost->average : 0,
                                ]);
                            }
                        }
                    }
//                    dd($price);

                    // Приходуем на склад позицию наряда
                    $stock = $model::firstOrNew([
                        'cmv_id' => $item->cmv_id,
                        'manufacturer_id' => $item->cmv->article->manufacturer_id,
                        'stock_id' => $production->stock_id,
                    ]);

                    $stock->filial_id = $production->filial_id;

                    $stock_count = isset($stock->count) ? $stock->count : 0;

                    $stock->count += $item->count;
                    $stock->weight += ($item->cmv->article->weight * $item->count);
                    $stock->volume += ($item->cmv->article->volume * $item->count);
                    $stock->save();

                    // Если себестоимость не 0, то меняем себестоимость
                    if ($price > 0) {
                        $cost = Cost::firstOrNew([
                            'cmv_id' => $item->cmv_id,
                            'cmv_type' => $item->cmv_type,
                            'manufacturer_id' => $item->cmv->article->manufacturer_id,
                        ]);

                        if ($cost->id) {
                            $cost->min = ($price < $cost->min) ? $price : $cost->min;
                            $cost->max = ($price > $cost->max) ? $price : $cost->max;

                            $cost_average = $cost->average;
                            $average = (($stock_count * $cost_average) + ($item->count * $price)) / $stock->count;
                            $cost->average = $average;
                        } else {
                            $cost->min = $price;
                            $cost->max = $price;
                            $cost->average = $price;
                        }
                        $cost->save();
//					dd($cost);
                    }
                }
            }

            $production->update([
                'is_produced' => true
            ]);

            return redirect()->route('productions.index');
        } else {
            abort(403, 'Наряд пуст');
        }
    }

    // TODO - 02.10.19 - Откат производста будем делать 03.10.19
    public function unproduced($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('update'));

        // ГЛАВНЫЙ ЗАПРОС:
        $production = Production::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('update'), $production);

        $production->load([
            'items' => function($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'article' => function ($q) {
                                $q->with([
                                    'raws.cost',
                                    'containers.cost'
                                ]);
                            }
                        ]);
                    },
                    'entity'
                ]);
            },
        ]);
//		dd($production);

        if ($production->items->isNotEmpty()) {

            $grouped_items = $production->items->groupBy('entity.alias');
//			dd($grouped_items);

            foreach ($grouped_items as $alias => $items) {
                $entity = Entity::where('alias', $alias.'_stocks')->first();
                $model = 'App\\'.$entity->model;

                foreach ($items as $item) {

                    $price = 0;
                    $relations = [
                        'raws',
                        'containers'
                    ];
                    foreach ($relations as $relation_name) {
                        if ($item->cmv->article->$relation_name->isNotEmpty()) {
                            foreach ($item->cmv->article->$relation_name as $composition) {
                                // Получаем себестоимость
                                $count = $composition->pivot->value;
                                $price += ($count * isset($composition->cost)? $composition->cost->average : 0);

                                // Списываем позицию состава
                                $entity_composition = Entity::where('alias', $relation_name.'_stocks')->first();
                                $model_composition = 'App\\'.$entity_composition->model;

                                $stock_composition = $model_composition::firstOrNew([
                                    'cmv_id' => $composition->id,
                                    'manufacturer_id' => $composition->article->manufacturer_id,
                                    'stock_id' => $production->stock_id,
                                ]);

                                $stock_composition->count -= $count * $item->count;
                                $stock_composition->weight -= ($composition->article->weight * $count * $item->count);
                                $stock_composition->volume -= ($composition->article->volume * $count * $item->count);
                                $stock_composition->save();
                            }
                        }
                    }
//                    dd($price);

                    // Приходуем на склад позицию наряда
                    $stock = $model::firstOrNew([
                        'cmv_id' => $item->cmv_id,
                        'manufacturer_id' => $item->cmv->article->manufacturer_id,
                        'stock_id' => $production->stock_id,
                    ]);

                    $stock->filial_id = $production->filial_id;

                    $stock_count = isset($stock->count) ? $stock->count : 0;

                    $stock->count += $item->count;
                    $stock->weight += ($item->cmv->article->weight * $item->count);
                    $stock->volume += ($item->cmv->article->volume * $item->count);
                    $stock->save();

                    // Если себестоимость не 0, то меняем себестоимость
                    if ($price > 0) {
                        $cost = Cost::firstOrNew([
                            'cmv_id' => $item->cmv_id,
                            'cmv_type' => $item->cmv_type,
                            'manufacturer_id' => $item->cmv->article->manufacturer_id,
                        ]);

                        if ($cost->id) {
                            $cost->min = ($price < $cost->min) ? $price : $cost->min;
                            $cost->max = ($price > $cost->max) ? $price : $cost->max;

                            $cost_average = $cost->average;
                            $average = (($stock_count * $cost_average) + ($item->count * $price)) / $stock->count;
                            $cost->average = $average;
                        } else {
                            $cost->min = $price;
                            $cost->max = $price;
                            $cost->average = $price;
                        }
                        $cost->save();
//					dd($cost);
                    }
                }

                foreach ($items as $item) {

                    $stock = $model::where([
                        'cmv_id' => $item->cmv_id,
                        'manufacturer_id' => $item->cmv->article->manufacturer_id,
                        'stock_id' => $production->stock_id,
                    ])
                        ->first();

                    $stock_count = $stock->count;

                    $stock->count -= $item->count;
                    $stock->weight -= ($item->cmv->article->weight * $item->count);
                    $stock->volume -= ($item->cmv->article->volume * $item->count);
                    $stock->save();

                    $cost = Cost::where([
                        'cmv_id' => $item->cmv_id,
                        'cmv_type' => $item->cmv_type,
                        'manufacturer_id' => $item->cmv->article->manufacturer_id,
                    ])
                        ->first();

                    $min = ProductionsItem::where([
                        'cmv_id' => $item->cmv_id,
                        'cmv_type' => $item->cmv_type,
                    ])
                        ->whereHas('consignment', function ($q) use ($production) {
                            $q->where('is_posted', true)
                                ->where('id', '!=', $production->id);
                        })
                        ->min('price');
//					dd($min);

                    $max = ProductionsItem::where([
                        'cmv_id' => $item->cmv_id,
                        'cmv_type' => $item->cmv_type,
                    ])
                        ->whereHas('consignment', function ($q) use ($production) {
                            $q->where('is_posted', true)
                                ->where('id', '!=', $production->id);
                        })
                        ->min('price');
//					dd($max);

                    if (is_null($min) || is_null($max)) {
                        $cost->delete();
                    } else {
                        $cost->min = $min;
                        $cost->max = $max;
                        $average = (($stock_count * $cost->average) - ($item->count * $item->price)) / ($stock->count);
                        $cost->average = $average;
                        $cost->save();
                    }

//					dd($cost);
                }
            }

            $production->update([
                'is_posted' => false
            ]);

            return redirect()->route('productions.index');
        } else {
            abort(403, 'Наряд пуст');
        }
    }
}
