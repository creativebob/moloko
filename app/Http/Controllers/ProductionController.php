<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Offable;
use App\Http\Controllers\Traits\Receiptable;
use App\Receipt;
use App\Stock;
use Illuminate\Support\Facades\Log;
use App\Cost;
use App\Entity;
use App\Http\Requests\System\ProductionUpdateRequest;
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
	        'items',
            'stock'
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            // ->whereNull('draft')
            ->booklistFilter($request)
            ->filter($request, 'supplier_id')
            ->orderBy('moderation', 'desc')
            ->latest('created_at')
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
        $pageInfo = pageInfo($this->entity_alias);

        return view('system.pages.productions.index', compact('productions', 'pageInfo', 'filter'));
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

        if (is_null(\Auth::user()->company->we_manufacturer)) {
            // Описание ошибки
//            $ajax_error = [];
//            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
//            $ajax_error['text'] = "Для начала необходимо стать производителем. А уже потом будем производить товары. Ок?";
//            $ajax_error['link'] = "/admin/companies"; // Ссылка на кнопке
//            $ajax_error['title_link'] = "Идем в раздел компаний"; // Текст на кнопке
//
//            return view('ajax_error', compact('ajax_error'));

            return back()
                ->withErrors(['msg' => 'Для начала необходимо стать производителем. А уже потом будем производить товары. Ок?']);
        }

        $production = Production::create();
        // dd($production);

        return redirect()->route('productions.edit', $production->id);
    }

    /**
     * Сохранение созданного ресурса в хранилище.
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
     * @param integer $id
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
                    'entity:id,name,alias'
                ]);
            },
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($production);

        $this->authorize(getmethod(__FUNCTION__), $production);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('system.pages.productions.edit', compact('production', 'pageInfo'));
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
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $production);

        $data = $request->input();
        $production->update($data);

        $production->amount = $this->getAmount($production);
        $production->save();

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
            ->find($id);

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

        $categories_tree = buildTreeArrayWithEntity($categories, $entity);
//        dd($categories_tree);

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

    /**
     * Производство наряда
     *
     * @param ProductionUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function produced(ProductionUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('update'));

        // ГЛАВНЫЙ ЗАПРОС:
        $production = Production::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);

        if ($production->is_produced == 0) {
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
                                        'raws' => function ($q) {
                                            $q->with([
                                                'cost'
                                            ]);
                                        },
                                        'containers' => function ($q) {
                                            $q->with([
                                                'cost'
                                            ]);
                                        },
                                        'attachments' => function ($q) {
                                            $q->with([
                                                'cost'
                                            ]);
                                        },
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

                foreach ($production->items as $item) {
                    if ($item->cmv->archive == 1) {
                        return back()
                            ->withErrors(['msg' => 'Наряд содержит архивные позиции, оприходование невозможно!']);
                    }
                }

                $stock_general = Stock::find($production->stock_id);

                set_time_limit(60*10);

                // Если нужна проверка остатка на складах
                if ($request->has('leftover')) {
                    $result = [];
                    $errors = [];

                    $number = 1;

                    foreach ($production->items as $item) {

                        $relations = [
                            'raws',
                            'containers',
                            'attachments'
                        ];

                        $entity_document = Entity::where('alias', $item->document->getTable())->first();
                        $model_document = 'App\\' . $entity_document->model;

                        $model_document_item = $model_document.'sItem';


                        foreach ($relations as $relation_name) {
                            if ($item->cmv->article->$relation_name->isNotEmpty()) {

                                $entity_composition = Entity::where('alias', $relation_name)->first();
                                $model_composition = 'App\\'.$entity_composition->model;

                                $entity_stock = Entity::where('alias', $relation_name.'_stocks')->first();
                                $model_stock = 'App\\'.$entity_stock->model;

                                foreach ($item->cmv->article->$relation_name as $composition) {

                                    // Списываем позицию состава
                                    $stock_composition = $composition->stocks->where('stock_id', $stock_general->id)->where('filial_id', $stock_general->filial_id)->where('manufacturer_id', $composition->article->manufacturer_id)->first();
//                          dd($stock_production);

                                    if ($stock_composition) {

                                        $count = $composition->pivot->value;
                                        $composition_count = $composition->portion * $count * $item->count;
                                        $total = $stock_composition->count - $composition_count;

                                        if ($total < 0) {
                                            $errors['msg'][] = 'Для позиции ' . $number . ' не хватает ' . $total . ' ' . $composition->article->unit->abbreviation .  ' "' . $composition->article->name . '" для производства';
                                        } else {
                                            $result[$item->id][] = [
                                                'entity' => $composition->getTable(),
                                                'id' => $composition->id,
                                                'model' => $model_composition,
                                                'stock_id' => $stock_composition->id,
                                                'stock_model' => $model_stock,
                                                'cost' => $composition->cost->average * $count * $composition->portion ,
                                                'amount' => $composition->portion * $count * $item->count * $composition->cost->average,
                                                'count' => $composition->portion * $count * $item->count,
                                                'weight' => $composition->weight * $count * $item->count,
                                                'volume' => $composition->volume * $count * $item->count
                                            ];
                                        }
                                    } else {
                                        $errors['msg'][] = 'Для позиции ' . $number . ' не существует склада для ' . $composition->article->name;
                                    }
                                }
                            }
                        }
                        $number++;
                    }
//	        dd($result);

                    if (!empty($errors)) {
//                dd($errors);
                        return back()
                            ->withErrors($errors)
                            ->withInput();
                    };
                }

                Log::channel('documents')
                    ->info('========================================== НАЧАЛО НАРЯДА ПРОИЗВОДСТВА C ID: ' . $production->id . ' ==============================================');
                Log::channel('documents')
                    ->info('Режим проверки остатка = ' . $request->has('leftover'));

                foreach ($production->items as $item) {

                    Log::channel('documents')
                        ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() . ' ' . $item->id . ' ===');

                    $cost = 0;
                    $amount = 0;
                    $is_wrong = 0;

                    // С проверкой остатка
                    if ($request->has('leftover')) {
                        $compositions = $result[$item->id];

                        foreach ($compositions as $composition) {

                            Log::channel('documents')
                                ->info('=== СПИСАНИЕ ' . $composition['entity'] . ' ' . $composition['id'] . ' ===');

                            $stock_composition = $composition['stock_model']::find($composition['stock_id']);

                            Log::channel('documents')
                                ->info('Существует склад ' . $stock_composition->getTable() . ' c id: ' . $stock_composition->id);


                            Log::channel('documents')
                                ->info('Значения count: ' . $stock_composition->count . ', weight: ' . $stock_composition->weight . ', volume: ' . $stock_composition->volume);

                            $stock_composition->count -= $composition['count'];
                            $stock_composition->weight -= $composition['weight'];
                            $stock_composition->volume -= $composition['volume'];
                            $stock_composition->save();

                            Log::channel('documents')
                                ->info('Обновлены значения count: ' . $stock_composition->count . ', weight: ' . $stock_composition->weight . ', volume: ' . $stock_composition->volume);

                            //                                dd($composition);
                            $off = Off::create([
                                'document_id' => $item->document->id,
                                'document_type' => $model_document,
                                'documents_item_id' => $item->id,
                                'documents_item_type' => $model_document_item,
                                'cmv_id' => $composition['id'],
                                'cmv_type' => $composition['model'],
                                'count' => $composition['count'],
                                'cost' => $composition['cost'],
                                'amount' => $composition['amount'],
                                'stock_id' => $item->document->stock_id,
                            ]);

                            $cost += $composition['cost'];
                            $amount += $composition['amount'];

                            Log::channel('documents')
                                ->info('Записали списание с id: ' . $off->id . ', count: ' . $off->count . ', cost: ' . $off->cost . ', amount: ' . $off->amount);

                            Log::channel('documents')
                                ->info('=== КОНЕЦ СПИСАНИЯ ===
                                    ');
                        }
                    } else {
                        // Без проверки остатка
                        $res = $this->production($item);
                        $cost = $res['cost'];
                        $is_wrong = $res['is_wrong'];
                        $amount = $cost * $item->count;
                    }

//                    $check = $this->check_production();
                    // Списание
//                    $cost = $this->production($item);

                    $item->update([
                        'cost' => $cost,
                        'amount' => $amount,
                    ]);

                    // Приходование
                    $this->receipt($item, $is_wrong);
                }

                $production->update([
                    'is_produced' => true,
                    'amount' => $this->getAmount($production)
                ]);

                Log::channel('documents')
                    ->info('Произведен наряд c id: ' . $production->id);
                Log::channel('documents')
                    ->info('========================================== КОНЕЦ ПРОИЗВОДСТВА НАРЯДА ==============================================
				
				');

            } else {
                abort(403, 'Наряд пуст');
            }
        }

        return redirect()->route('productions.index');
    }

    public function unproduced($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('update'));

        // ГЛАВНЫЙ ЗАПРОС:
        $production = Production::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);

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

    public function reproduced(Request $request, $num)
    {

        // Подключение политики
        $this->authorize(getmethod('index'), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('index'));

        if ($num == 1) {
            $ids = [1, 2, 3, 4, 5, 6, 7];
        } else {
            $ids = [8, 9, 10, 11, 12, 13, 14];
        };

        set_time_limit(60*5);
//        ini_set('memory_limit', '100М');

        // ГЛАВНЫЙ ЗАПРОС:
        $productions = Production::with([
            'items' => function($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'article' => function ($q) {
                                $q->with([
                                    'raws' => function ($q) {
                                        $q->with([
                                            'cost'
                                        ]);
                                    },
                                    'containers' => function ($q) {
                                        $q->with([
                                            'cost'
                                        ]);
                                    },
                                    'attachments' => function ($q) {
                                        $q->with([
                                            'cost'
                                        ]);
                                    },
                                ]);
                            }
                        ]);
                    },
                    'entity'
                ]);
            },
        ])
            ->companiesLimit($answer)
            ->where('is_produced', true)
            ->whereIn('id', $ids)
            ->chunk(1, function($productions) use ($request)
            {
                foreach ($productions as $production){
                    if ($production->is_produced == 1) {

//		dd($production);

                        if ($production->items->isNotEmpty()) {



                            $stock_general = Stock::find($production->stock_id);

                            // Если нужна проверка остатка на слкдах
                            if ($request->has('leftover')) {
                                $result = [];
                                $errors = [];

                                $number = 1;

                                foreach ($production->items as $item) {

                                    $relations = [
                                        'raws',
                                        'containers',
                                        'attachments'
                                    ];

                                    $entity_document = Entity::where('alias', $item->document->getTable())->first();
                                    $model_document = 'App\\' . $entity_document->model;

                                    $model_document_item = $model_document.'sItem';


                                    foreach ($relations as $relation_name) {
                                        if ($item->cmv->article->$relation_name->isNotEmpty()) {

                                            $entity_composition = Entity::where('alias', $relation_name)->first();
                                            $model_composition = 'App\\'.$entity_composition->model;

                                            $entity_stock = Entity::where('alias', $relation_name.'_stocks')->first();
                                            $model_stock = 'App\\'.$entity_stock->model;

                                            foreach ($item->cmv->article->$relation_name as $composition) {

                                                // Списываем позицию состава
                                                $stock_composition = $composition->stocks->where('stock_id', $stock_general->id)->where('filial_id', $stock_general->filial_id)->where('manufacturer_id', $composition->article->manufacturer_id)->first();
//                          dd($stock_production);

                                                if ($stock_composition) {

                                                    $count = $composition->pivot->value;
                                                    $composition_count = $composition->portion * $count * $item->count;
                                                    $total = $stock_composition->count - $composition_count;

                                                    if ($total < 0) {
                                                        $errors['msg'][] = 'Для позиции ' . $number . ' не хватает ' . $total . ' ' . $composition->article->unit->abbreviation .  ' "' . $composition->article->name . '" для производства';
                                                    } else {
                                                        $result[$item->id][] = [
                                                            'entity' => $composition->getTable(),
                                                            'id' => $composition->id,
                                                            'model' => $model_composition,
                                                            'stock_id' => $stock_composition->id,
                                                            'stock_model' => $model_stock,
                                                            'cost' => $composition->cost->average * $count * $composition->portion ,
                                                            'amount' => $composition->portion * $count * $item->count * $composition->cost->average,
                                                            'count' => $composition->portion * $count * $item->count,
                                                            'weight' => $composition->weight * $count * $item->count,
                                                            'volume' => $composition->volume * $count * $item->count
                                                        ];
                                                    }
                                                } else {
                                                    $errors['msg'][] = 'Для позиции ' . $number . ' не существует склада для ' . $composition->article->name;
                                                }
                                            }
                                        }
                                    }
                                    $number++;
                                }
//	        dd($result);

                                if (!empty($errors)) {
//                dd($errors);
                                    return back()
                                        ->withErrors($errors)
                                        ->withInput();
                                };
                            }

                            Log::channel('documents')
                                ->info('========================================== НАЧАЛО НАРЯДА ПРОИЗВОДСТВА C ID: ' . $production->id . ' ==============================================');
                            Log::channel('documents')
                                ->info('Режим проверки остатка = ' . $request->has('leftover'));

                            foreach ($production->items as $item) {

                                Log::channel('documents')
                                    ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() . ' ' . $item->id . ' ===');

                                $cost = 0;
                                $amount = 0;
                                $is_wrong = 0;

                                // С проверкой остатка
                                if ($request->has('leftover')) {
                                    $compositions = $result[$item->id];

                                    foreach ($compositions as $composition) {

                                        Log::channel('documents')
                                            ->info('=== СПИСАНИЕ ' . $composition['entity'] . ' ' . $composition['id'] . ' ===');

                                        $stock_composition = $composition['stock_model']::find($composition['stock_id']);

                                        Log::channel('documents')
                                            ->info('Существует склад ' . $stock_composition->getTable() . ' c id: ' . $stock_composition->id);


                                        Log::channel('documents')
                                            ->info('Значения count: ' . $stock_composition->count . ', weight: ' . $stock_composition->weight . ', volume: ' . $stock_composition->volume);

                                        $stock_composition->count -= $composition['count'];
                                        $stock_composition->weight -= $composition['weight'];
                                        $stock_composition->volume -= $composition['volume'];
                                        $stock_composition->save();

                                        Log::channel('documents')
                                            ->info('Обновлены значения count: ' . $stock_composition->count . ', weight: ' . $stock_composition->weight . ', volume: ' . $stock_composition->volume);

                                        //                                dd($composition);
                                        $off = Off::create([
                                            'document_id' => $item->document->id,
                                            'document_type' => $model_document,
                                            'documents_item_id' => $item->id,
                                            'documents_item_type' => $model_document_item,
                                            'cmv_id' => $composition['id'],
                                            'cmv_type' => $composition['model'],
                                            'count' => $composition['count'],
                                            'cost' => $composition['cost'],
                                            'amount' => $composition['amount'],
                                            'stock_id' => $item->document->stock_id,
                                        ]);

                                        $cost += $composition['cost'];
                                        $amount += $composition['amount'];

                                        Log::channel('documents')
                                            ->info('Записали списание с id: ' . $off->id . ', count: ' . $off->count . ', cost: ' . $off->cost . ', amount: ' . $off->amount);

                                        Log::channel('documents')
                                            ->info('=== КОНЕЦ СПИСАНИЯ ===
                                    ');
                                    }
                                } else {
                                    // Без проверки остатка
                                    $res = $this->production($item);
                                    $cost = $res['cost'];
                                    $is_wrong = $res['is_wrong'];
                                    $amount = $cost * $item->count;
                                }

//                    $check = $this->check_production();
                                // Списание
//                    $cost = $this->production($item);

                                $item->update([
                                    'cost' => $cost,
                                    'amount' => $amount,
                                ]);

                                // Приходование
                                $this->receipt($item, $is_wrong);
                            }

                            $production->update([
                                'is_produced' => true,
                                'amount' => $this->getAmount($production)
                            ]);

                            Log::channel('documents')
                                ->info('Произведен наряд c id: ' . $production->id);
                            Log::channel('documents')
                                ->info('========================================== КОНЕЦ ПРОИЗВОДСТВА НАРЯДА ==============================================
				
				');

                        }
                    }
                }
            });

//        dd($productions);




        return redirect()->route('productions.index');
    }

    public function getAmount($production)
    {
        $amount = 0;
        $production->load('items');
        if ($production->items->isNotEmpty()) {
            $amount = $production->items->sum('amount');
        }
        return $amount;
    }
}
