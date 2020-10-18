<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Offable;
use App\Http\Controllers\Traits\Receiptable;
use App\Stock;
use App\Entity;
use App\Http\Requests\System\ProductionUpdateRequest;
use App\Off;
use App\Models\System\Documents\Production;
use App\Models\System\Documents\ProductionsItem;
use Illuminate\Http\Request;

class ProductionController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * ProductionController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'productions';
        $this->entityDependence = true;
    }

    use Offable;
    use Receiptable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Production::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

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
//            ->filter($request, 'supplier_id')
            ->orderBy('moderation', 'desc')
            ->latest('created_at')
            ->paginate(30);
//         dd($productions);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'supplier',             // Поставщики
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.documents.productions.index', compact('productions', 'pageInfo', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod('store'), Production::class);

        if (is_null(auth()->user()->company->we_manufacturer)) {
            return back()
                ->withErrors(['msg' => 'Для начала необходимо стать производителем. А уже потом будем производить товары. Ок?']);
        }

        $production = Production::create();

        return redirect()->route('productions.edit', $production->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

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

        if (empty($production)) {
            abort(403, __('errors.not_found'));
        }

        $this->authorize(getmethod(__FUNCTION__), $production);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.documents.productions.edit', compact('production', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductionUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(ProductionUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $production = Production::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($production);

        if (empty($production)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $production);

        $data = $request->input();
        $production->update($data);

        return redirect()->route('productions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $production = Production::with('items')
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($production);

        if (empty($production)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $production);

        $production->delete();

        if ($production) {
            return redirect()->route('productions.index');
        } else {
            abort(403, __('errors.destroy'));
        }
    }

    public function categories(Request $request)
    {
        $entity = Entity::find($request->entity_id);

        $entityAlias = $entity->alias;
        $alias = $entityAlias . '_categories';

        $entity_categories = Entity::whereAlias($alias)->first(['model']);
        $model = $entity_categories->model;

        // Получаем из сессии необходимые данные
        $answer = operator_right($entity_categories->alias, false, 'index');

        $categories = $model::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->with([
                $entityAlias => function ($q) {
                    $q->with([
                        'article' => function ($q) {
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
                                });
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
        foreach ($categories as $category) {
            $category->entity_id = $entity->id;

            if (isset($category->$entityAlias)) {
                foreach ($category->$entityAlias as $item) {
                    $item->category_id = $category->id;
                    $item->entity_id = $entity->id;
                    $items[] = $item;
                }
            }

            if (isset($category->childCategories)) {
                if (isset($category->$entityAlias)) {
                    foreach ($category->childCategories as $childCategory) {
                        foreach ($childCategory->$entityAlias as $item) {
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
     * Производство
     *
     * @param ProductionUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function producing(ProductionUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('update'));

        // ГЛАВНЫЙ ЗАПРОС:
        $production = Production::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($production);

        if (empty($production)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('update'), $production);

        if (empty($production->produced_at)) {
            $data = $request->input();
            $production->update($data);

            $production->load([
                'items' => function ($q) {
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

                $draft = $production->items->firstWhere('cmv.article.draft', 1);
                if ($draft) {
                    return back()
                        ->withErrors(['msg' => 'Наряд содержит черновые позиции, производство невозможно!']);
                }

                $archive = $production->items->firstWhere('cmv.archive', 1);
                if ($archive) {
                    return back()
                        ->withErrors(['msg' => 'Наряд содержит архивные позиции, производство невозможно!']);
                }

                set_time_limit(0);

                $stockGeneral = Stock::find($production->stock_id);

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
                        $model_document = $entity_document->model;

                        $model_document_item = $model_document . 'sItem';


                        foreach ($relations as $relation_name) {
                            if ($item->cmv->article->$relation_name->isNotEmpty()) {

                                $entity_composition = Entity::where('alias', $relation_name)->first();
                                $model_composition = $entity_composition->model;

                                $entity_stock = Entity::where('alias', $relation_name . '_stocks')->first();
                                $model_stock = $entity_stock->model;

                                foreach ($item->cmv->article->$relation_name as $composition) {

                                    // Списываем позицию состава
                                    $stock_composition = $composition->stocks->where('stock_id', $stockGeneral->id)->where('filial_id', $stockGeneral->filial_id)->where('manufacturer_id', $composition->article->manufacturer_id)->first();
//                          dd($stock_production);

                                    if ($stock_composition) {

                                        $count = $composition->pivot->value;
                                        $composition_count = $composition->portion * $count * $item->count;
                                        $total = $stock_composition->count - $composition_count;

                                        if ($total < 0) {
                                            $errors['msg'][] = 'Для позиции ' . $number . ' не хватает ' . $total . ' ' . $composition->article->unit->abbreviation . ' "' . $composition->article->name . '" для производства';
                                        } else {
                                            $result[$item->id][] = [
                                                'entity' => $composition->getTable(),
                                                'id' => $composition->id,
                                                'model' => $model_composition,
                                                'stock_id' => $stock_composition->id,
                                                'stock_model' => $model_stock,
                                                'cost' => $composition->cost->average * $count * $composition->portion,
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

                logs('documents')
                    ->info('========================================== НАЧАЛО НАРЯДА ПРОИЗВОДСТВА C ID: ' . $production->id . ' ==============================================');
                logs('documents')
                    ->info('Режим проверки остатка = ' . $request->has('leftover'));

                foreach ($production->items as $item) {

                    logs('documents')
                        ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() . ' ' . $item->id . ' ===');

                    $cost = 0;
                    $amount = 0;
                    $isWrong = 0;

                    // С проверкой остатка
                    if ($request->has('leftover')) {
                        $compositions = $result[$item->id];

                        foreach ($compositions as $composition) {

                            logs('documents')
                                ->info('=== СПИСАНИЕ ' . $composition['entity'] . ' ' . $composition['id'] . ' ===');

                            $stock_composition = $composition['stock_model']::find($composition['stock_id']);

                            logs('documents')
                                ->info('Существует склад ' . $stock_composition->getTable() . ' c id: ' . $stock_composition->id);


                            logs('documents')
                                ->info('Значения count: ' . $stock_composition->count . ', weight: ' . $stock_composition->weight . ', volume: ' . $stock_composition->volume);

                            $stock_composition->count -= $composition['count'];
                            $stock_composition->weight -= $composition['weight'];
                            $stock_composition->volume -= $composition['volume'];
                            $stock_composition->save();

                            logs('documents')
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

                            logs('documents')
                                ->info('Записали списание с id: ' . $off->id . ', count: ' . $off->count . ', cost: ' . $off->cost . ', amount: ' . $off->amount);

                            logs('documents')
                                ->info('=== КОНЕЦ СПИСАНИЯ ===
                                    ');
                        }
                    } else {
                        // Без проверки остатка
                        $res = $this->production($item);
                        $cost = $res['cost'];
                        $isWrong = $res['is_wrong'];
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
                    $this->receipt($item, $isWrong);
                }

                $production->update([
                    'produced_at' => now(),
                ]);

                logs('documents')
                    ->info('Произведен наряд c id: ' . $production->id);
                logs('documents')
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
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('update'));

        // ГЛАВНЫЙ ЗАПРОС:
        $production = Production::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod('update'), $production);

        $production->load([
            'items' => function ($q) {
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

            logs('documents')
                ->info('========================================== ОТМЕНА НАРЯДА ПРОИЗВОДСТВА ==============================================');

            $grouped_items = $production->items->groupBy('entity.alias');
//			dd($grouped_items);

            foreach ($grouped_items as $alias => $items) {
                $entity = Entity::where('alias', $alias . '_stocks')->first();
                $model = $entity->model;

                foreach ($items as $item) {
                    logs('documents')
                        ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() . ' ' . $item->id . ' ===');

                    logs('documents')
                        ->info('=== ПЕРЕБИРАЕМ СПИСАНИЯ И ПРИХОДУЕМ СОСТАВ ===');

                    foreach ($item->offs as $off) {

                        $cmv = $off->cmv;

                        logs('documents')
                            ->info('=== ПРИХОДОВАНИЕ ' . $cmv->getTable() . ' ' . $cmv->id . ' ===');

                        // Склад
                        $stock = $cmv->stock;

                        logs('documents')
                            ->info('Существует склад ' . $stock->getTable() . ' c id: ' . $stock->id);

                        logs('documents')
                            ->info('Значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

                        $stock_count = $stock->count;

                        $stock->count += $off->count;
                        $stock->weight += ($cmv->article->weight * $off->count);
                        $stock->volume += ($cmv->article->volume * $off->count);
                        $stock->save();

                        logs('documents')
                            ->info('Обновлены значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

                        // Себестоимость
                        $cost = $cmv->cost;

                        logs('documents')
                            ->info('Существует себестоимость c id: ' . $cost->id);
                        logs('documents')
                            ->info('Значения min: ' . $cost->min . ', max: ' . $cost->max . ', average: ' . $cost->average);

                        $cost_average = $cost->average;
                        if ($stock->count > 0) {
                            $average = (($stock_count * $cost_average) + ($off->count * $off->average)) / $stock->count;
                        } else {
                            $average = (($stock_count * $cost_average) + ($off->count * $off->average));
                        };
                        $cost->average = $average;
                        $cost->save();

                        logs('documents')
                            ->info('Обновлены значения min: ' . $cost->min . ', max: ' . $cost->max . ', average: ' . $cost->average);

                        $off->delete();

                        logs('documents')
                            ->info('Удалено списание с id:' . $off->id);

                        logs('documents')
                            ->info('=== КОНЕЦ ПРИХОДОВАНИЯ ===');
                    }

                    logs('documents')
                        ->info('=== КОНЕЦ ПЕРЕБОРА СПИСАНИЯ И ПРИХОДОВАНИЯ СОСТАВА ===');

                    logs('documents')
                        ->info('=== СПИСАНИЕ ' . $item->cmv->getTable() . ' ' . $item->cmv->id . ' ===');

                    // Склад
                    $stock = $item->cmv->stock;

                    logs('documents')
                        ->info('Существует склад ' . $stock->getTable() . ' c id: ' . $stock->id);
                    logs('documents')
                        ->info('Значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

                    $stock_count = $stock->count;

                    $stock->count -= $item->count;
                    $stock->weight -= ($item->cmv->article->weight * $item->count);
                    $stock->volume -= ($item->cmv->article->volume * $item->count);
                    $stock->save();

                    logs('documents')
                        ->info('Обновлены значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

                    // Себестоимость
                    $cost = $item->cmv->cost;

                    logs('documents')
                        ->info('Существует себестоимость c id: ' . $cost->id);
                    logs('documents')
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

                    logs('documents')
                        ->info('Обновлены значения min: ' . $cost->min . ', max: ' . $cost->max . ', average: ' . $cost->average);
                    logs('documents')
                        ->info('=== КОНЕЦ СПИСАНИЯ ===');

                    $item->update([
                        'cost' => 0
                    ]);

                    logs('documents')
                        ->info('Обновляем себестоимость за еденицу в пункте наряда: 0');

                    logs('documents')
                        ->info('=== КОНЕЦ ПЕРЕБОРА ПУНКТА ===
                        ');

                }
            }

            $production->update([
                'is_produced' => false
            ]);

            logs('documents')
                ->info('Отменен наряд c id: ' . $production->id);
            logs('documents')
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
        $this->authorize(getmethod('index'), Production::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('index'));

        if ($num == 1) {
            $ids = [1, 2, 3, 4, 5, 6, 7];
        } else {
            $ids = [8, 9, 10, 11, 12, 13, 14];
        };

        set_time_limit(0);
//        ini_set('memory_limit', '100М');

        // ГЛАВНЫЙ ЗАПРОС:
        $productions = Production::with([
            'items' => function ($q) {
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
            ->chunk(1, function ($productions) use ($request) {
                foreach ($productions as $production) {
                    if ($production->is_produced == 1) {

//		dd($production);

                        if ($production->items->isNotEmpty()) {


                            $stockGeneral = Stock::find($production->stock_id);

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
                                    $model_document = $entity_document->model;

                                    $model_document_item = $model_document . 'sItem';


                                    foreach ($relations as $relation_name) {
                                        if ($item->cmv->article->$relation_name->isNotEmpty()) {

                                            $entity_composition = Entity::where('alias', $relation_name)->first();
                                            $model_composition = $entity_composition->model;

                                            $entity_stock = Entity::where('alias', $relation_name . '_stocks')->first();
                                            $model_stock = $entity_stock->model;

                                            foreach ($item->cmv->article->$relation_name as $composition) {

                                                // Списываем позицию состава
                                                $stock_composition = $composition->stocks->where('stock_id', $stockGeneral->id)->where('filial_id', $stockGeneral->filial_id)->where('manufacturer_id', $composition->article->manufacturer_id)->first();
//                          dd($stock_production);

                                                if ($stock_composition) {

                                                    $count = $composition->pivot->value;
                                                    $composition_count = $composition->portion * $count * $item->count;
                                                    $total = $stock_composition->count - $composition_count;

                                                    if ($total < 0) {
                                                        $errors['msg'][] = 'Для позиции ' . $number . ' не хватает ' . $total . ' ' . $composition->article->unit->abbreviation . ' "' . $composition->article->name . '" для производства';
                                                    } else {
                                                        $result[$item->id][] = [
                                                            'entity' => $composition->getTable(),
                                                            'id' => $composition->id,
                                                            'model' => $model_composition,
                                                            'stock_id' => $stock_composition->id,
                                                            'stock_model' => $model_stock,
                                                            'cost' => $composition->cost->average * $count * $composition->portion,
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

                            logs('documents')
                                ->info('========================================== НАЧАЛО НАРЯДА ПРОИЗВОДСТВА C ID: ' . $production->id . ' ==============================================');
                            logs('documents')
                                ->info('Режим проверки остатка = ' . $request->has('leftover'));

                            foreach ($production->items as $item) {

                                logs('documents')
                                    ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() . ' ' . $item->id . ' ===');

                                $cost = 0;
                                $amount = 0;
                                $isWrong = 0;

                                // С проверкой остатка
                                if ($request->has('leftover')) {
                                    $compositions = $result[$item->id];

                                    foreach ($compositions as $composition) {

                                        logs('documents')
                                            ->info('=== СПИСАНИЕ ' . $composition['entity'] . ' ' . $composition['id'] . ' ===');

                                        $stock_composition = $composition['stock_model']::find($composition['stock_id']);

                                        logs('documents')
                                            ->info('Существует склад ' . $stock_composition->getTable() . ' c id: ' . $stock_composition->id);


                                        logs('documents')
                                            ->info('Значения count: ' . $stock_composition->count . ', weight: ' . $stock_composition->weight . ', volume: ' . $stock_composition->volume);

                                        $stock_composition->count -= $composition['count'];
                                        $stock_composition->weight -= $composition['weight'];
                                        $stock_composition->volume -= $composition['volume'];
                                        $stock_composition->save();

                                        logs('documents')
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

                                        logs('documents')
                                            ->info('Записали списание с id: ' . $off->id . ', count: ' . $off->count . ', cost: ' . $off->cost . ', amount: ' . $off->amount);

                                        logs('documents')
                                            ->info('=== КОНЕЦ СПИСАНИЯ ===
                                    ');
                                    }
                                } else {
                                    // Без проверки остатка
                                    $res = $this->production($item);
                                    $cost = $res['cost'];
                                    $isWrong = $res['is_wrong'];
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
                                $this->receipt($item, $isWrong);
                            }

                            $production->update([
                                'produced_at' => now(),
                            ]);

                            logs('documents')
                                ->info('Произведен наряд c id: ' . $production->id);
                            logs('documents')
                                ->info('========================================== КОНЕЦ ПРОИЗВОДСТВА НАРЯДА ==============================================
				
				');

                        }
                    }
                }
            });

//        dd($productions);


        return redirect()->route('productions.index');
    }
}
