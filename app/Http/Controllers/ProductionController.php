<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Offable;
use App\Http\Controllers\Traits\Receiptable;
use App\Receipt;
use Illuminate\Support\Facades\Log;
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

    use Offable;
    use Receiptable;


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

        $productions = Production::with([
        	'author',
	        'items'
        ])
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
        $production = Production::with('items')
	    ->moderatorLimit($answer)
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
	
	        Log::channel('documents')
		        ->info('========================================== НАЧАЛО НАРЯДА ПРОИЗВОДСТВА C ID: ' . $production->id . ' ==============================================');
	        
	        $grouped_items = $production->items->groupBy('entity.alias');
//			dd($grouped_items);

            foreach ($grouped_items as $alias => $items) {
                $entity = Entity::where('alias', $alias.'_stocks')->first();
                $model = 'App\\'.$entity->model;

                foreach ($items as $item) {
                    Log::channel('documents')
                        ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() .' ' . $item->id . ' ===');

                    // Списание
                    $cost = $this->production($item);

                    $item->update([
                        'cost' => $cost,
                        'amount' => $item->count * $cost,
                    ]);

                    // Приходование
                    $this->receipt($item);
                }
            }

            $production->update([
                'is_produced' => true
            ]);
	
	        Log::channel('documents')
		        ->info('Произведен наряд c id: ' . $production->id);
	        Log::channel('documents')
		        ->info('========================================== КОНЕЦ ПРОИЗВОДСТВА НАРЯДА ==============================================
				
				');

            return redirect()->route('productions.index');
        } else {
            abort(403, 'Наряд пуст');
        }
    }
    
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
                                    'raws' => function ($q) {
		                                $q->with([
		                                    'cost',
		                                    'stock'
		                                ]);
		                            },
	                                'containers' => function ($q) {
		                                $q->with([
			                                'cost',
			                                'stock'
		                                ]);
	                                },
                                ]);
                            }
                        ]);
                    },
                    'entity'
                ]);
            },
	        'offs' => function ($q) {
			    $q->with([
				    'cmv' => function ($q) {
					    $q->with([
						    'cost',
						    'stock',
						    'article'
					    ]);
				    },
			    ]);
            },
        ]);
//		dd($production);

        if ($production->items->isNotEmpty()) {
	
	        Log::channel('documents')
		        ->info('========================================== ОТМЕНА НАРЯДА ПРОИЗВОДСТВА ==============================================');

            $grouped_items = $production->items->groupBy('entity.alias');
//			dd($grouped_items);

            foreach ($grouped_items as $alias => $items) {
                $entity = Entity::where('alias', $alias.'_stocks')->first();
                $model = 'App\\'.$entity->model;
	
	            foreach ($items as $item) {
                    Log::channel('documents')
                        ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() .' ' . $item->id . ' ===');

                    Log::channel('documents')
                        ->info('=== ПЕРЕБИРАЕМ СПИСАНИЯ И ПРИХОДУЕМ СОСТАВ ===');

                    foreach ($item->offs as $off) {

                        $cmv = $off->cmv;

                        Log::channel('documents')
                            ->info('=== ПРИХОДОВАНИЕ ' . $cmv->getTable() . ' ' . $cmv->id . ' ===');

                        // Склад
                        $stock = $cmv->stock;

                        Log::channel('documents')
                            ->info('Существует склад ' . $stock->getTable() . ' c id: ' . $stock->id);

                        Log::channel('documents')
                            ->info('Значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

                        $stock_count = $stock->count;

                        $stock->count += $off->count;
                        $stock->weight += ($cmv->article->weight * $off->count);
                        $stock->volume += ($cmv->article->volume * $off->count);
                        $stock->save();

                        Log::channel('documents')
                            ->info('Обновлены значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

                        // Себестоимость
                        $cost = $cmv->cost;

                        Log::channel('documents')
                            ->info('Существует себестоимость c id: ' . $cost->id);
                        Log::channel('documents')
                            ->info('Значения min: ' . $cost->min . ', max: ' . $cost->max . ', average: ' . $cost->average);

                        $cost_average = $cost->average;
                        if ($stock->count > 0) {
                            $average = (($stock_count * $cost_average) + ($off->count * $off->average)) / $stock->count;
                        } else {
                            $average = (($stock_count * $cost_average) + ($off->count * $off->average));
                        };
                        $cost->average = $average;
                        $cost->save();

                        Log::channel('documents')
                            ->info('Обновлены значения min: ' . $cost->min . ', max: ' . $cost->max . ', average: ' . $cost->average);

                        $off->delete();

                        Log::channel('documents')
                            ->info('Удалено списание с id:' . $off->id);

                        Log::channel('documents')
                            ->info('=== КОНЕЦ ПРИХОДОВАНИЯ ===');
                    }

                    Log::channel('documents')
                        ->info('=== КОНЕЦ ПЕРЕБОРА СПИСАНИЯ И ПРИХОДОВАНИЯ СОСТАВА ===');
		
		            Log::channel('documents')
			            ->info('=== СПИСАНИЕ ' . $item->cmv->getTable() . ' ' . $item->cmv->id . ' ===');
		            
		            // Склад
		            $stock = $item->cmv->stock;
		
		            Log::channel('documents')
			            ->info('Существует склад ' . $stock->getTable() . ' c id: ' . $stock->id);
		            Log::channel('documents')
			            ->info('Значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);
		
		            $stock_count = $stock->count;
		
		            $stock->count -= $item->count;
		            $stock->weight -= ($item->cmv->article->weight * $item->count);
		            $stock->volume -= ($item->cmv->article->volume * $item->count);
		            $stock->save();
		
		            Log::channel('documents')
			            ->info('Обновлены значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);
		
		            // Себестоимость
		            $cost = $item->cmv->cost;
		
		            Log::channel('documents')
			            ->info('Существует себестоимость c id: ' . $cost->id);
		            Log::channel('documents')
			            ->info('Значения min: ' . $cost->min . ', max: ' . $cost->max . ', average: ' . $cost->average);

                    // Получаем из сессии необходимые данные
                    $answer = operator_right('consignments_items', true, 'index');

                    $min = ProductionsItem::moderatorLimit($answer)
                        ->companiesLimit($answer)
                        ->where([
                            'cmv_id' => $item->cmv_id,
                            'cmv_type' => $item->cmv_type,
                        ])
                        ->whereHas('production', function ($q) use ($production) {
                            $q->where('is_produced', true)
                                ->where('id', '!=', $production->id);
                        })
                        ->min('cost');
//					dd($min);

                    $max = ProductionsItem::moderatorLimit($answer)
                        ->companiesLimit($answer)
                        ->where([
                            'cmv_id' => $item->cmv_id,
                            'cmv_type' => $item->cmv_type,
                        ])
                        ->whereHas('production', function ($q) use ($production) {
                            $q->where('is_produced', true)
                                ->where('id', '!=', $production->id);
                        })
                        ->min('cost');
//					dd($max);

                    if (is_null($min) || is_null($max)) {
                        $data_cost = [
                            'min' => 0,
                            'max' => 0,
                            'average' => 0,
                        ];
                    } else {
                        $average = (($stock_count * $cost->average) - ($item->count * $item->cost)) / $stock->count;
                        $data_cost = [
                            'min' => $min,
                            'max' => $max,
                            'average' => $average,
                        ];
                    }

		            $cost->update($data_cost);
//					dd($cost);
		
		            Log::channel('documents')
			            ->info('Обновлены значения min: ' . $cost->min . ', max: ' . $cost->max . ', average: ' . $cost->average);
		            Log::channel('documents')
			            ->info('=== КОНЕЦ СПИСАНИЯ ===');

                    $item->update([
                        'cost' => 0
                    ]);

                    Log::channel('documents')
                        ->info('Обновляем себестоимость за еденицу в пункте наряда: 0');

                    Log::channel('documents')
                        ->info('=== КОНЕЦ ПЕРЕБОРА ПУНКТА ===
                        ');

	            }
            }

            $production->update([
                'is_produced' => false
            ]);
	
	        Log::channel('documents')
		        ->info('Отменен наряд c id: ' . $production->id);
	        Log::channel('documents')
		        ->info('========================================== КОНЕЦ ОТМЕНЫ НАРЯДА ==============================================
				
				');

            return redirect()->route('productions.index');
        } else {
            abort(403, 'Наряд пуст');
        }
    }
}