<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Receiptable;
use Illuminate\Support\Facades\Schema;
use App\CostsHistory;
use App\Receipt;
use Illuminate\Support\Facades\Log;
use App\Consignment;
use App\ConsignmentsItem;
use App\Cost;
use App\Entity;
use App\Http\Requests\ConsignmentUpdateRequest;
use Illuminate\Http\Request;

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

    use Receiptable;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $consignments = Consignment::with([
        	'author',
	        'items'
        
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer)
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

        return view('system.pages.consignments.index', compact('consignments', 'page_info', 'filter'));
    }
    
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod('store'), $this->class);

        $consignment = Consignment::create();
        // dd($consignment);

	    return redirect()->route('consignments.edit', $consignment->id);
    }


    public function store(Request $request)
    {
        //
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function edit($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $consignment = Consignment::with([
            'items' => function ($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'article.unit'
                        ]);
                    },
                    'entity:id,name,alias',
                    'manufacturer.company'
                ]);
            },
        ])
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);
//        dd($consignment);

        $this->authorize(getmethod(__FUNCTION__), $consignment);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('system.pages.consignments.edit', compact('consignment', 'page_info'));
    }


    public function update(ConsignmentUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $consignment = Consignment::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $consignment);

        $data = $request->input();
        $consignment->update($data);

        $consignment->amount = $this->getAmount($consignment);
        $consignment->save();

        return redirect()->route('consignments.index');
    }


    public function destroy($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $consignment = Consignment::with('items')
	    ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $consignment);

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
            ->with([
                $entity_alias => function ($q) {
                    $q->with([
                        'article.unit'
                    ])
                        ->where('archive', false)
                        ->whereHas('article', function ($q) {
                            $q->where('draft', false)
                            ->whereNotNull('manufacturer_id');
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
    
	public function posting(ConsignmentUpdateRequest $request, $id)
	{
		
		// Получаем из сессии необходимые данные (Функция находиться в Helpers)
		$answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('update'));
		
		// ГЛАВНЫЙ ЗАПРОС:
		$consignment = Consignment::moderatorLimit($answer)
			->authors($answer)
			->systemItem($answer)
			->findOrFail($id);
		
		// Подключение политики
		$this->authorize(getmethod('update'), $consignment);
		
		$data = $request->input();
		$consignment->update($data);
		
		$consignment->load([
			'items' => function($q) {
				$q->with([
					'cmv' => function ($q) {
						$q->with([
							'article',
                            'stocks',
                            'cost'
						]);
					},
					'entity',
				]);
			},
		]);
//		dd($consignment);
		
		if ($consignment->items->isNotEmpty()) {
			
			Log::channel('documents')
				->info('========================================== НАЧАЛО ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ, ID: ' . $consignment->id . ' ==============================================');

//            $grouped_items = $document->items->groupBy('entity.alias');
//			dd($grouped_items);
//
//            foreach ($grouped_items as $alias => $items) {
            //            }

            foreach ($consignment->items as $item) {
                $this->receipt($item);
            }


			$consignment->update([
				'is_posted' => true,
				'amount' => $this->getAmount($consignment)
			]);
			
			Log::channel('documents')
				->info('Оприходована накладная c id: ' . $consignment->id);
			Log::channel('documents')
				->info('========================================== КОНЕЦ ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ ==============================================
				
				');
			
			return redirect()->route('consignments.index');
		} else {
			abort(403, 'Накладная пуста');
		}
	}
	
	public function unpost($id)
	{
		
		// Получаем из сессии необходимые данные (Функция находиться в Helpers)
		$answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('update'));
		
		// ГЛАВНЫЙ ЗАПРОС:
		$consignment = Consignment::moderatorLimit($answer)
			->authors($answer)
			->systemItem($answer)
			->findOrFail($id);
		
		// Подключение политики
		$this->authorize(getmethod('update'), $consignment);
		
		$consignment->load([
			'items' => function($q) {
				$q->with([
					'cmv' => function ($q) {
						$q->with([
							'article'
						]);
					},
					'entity'
				]);
			},
		]);
//		dd($consignment);
		
		if ($consignment->items->isNotEmpty()) {
			
			Log::channel('documents')
				->info('========================================== НАЧАЛО ОТМЕНЫ ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ ==============================================');
			
			$grouped_items = $consignment->items->groupBy('entity.alias');
//			dd($grouped_items);
			
			foreach ($grouped_items as $alias => $items) {
				$entity = Entity::where('alias', $alias.'_stocks')->first();
				$model = 'App\\'.$entity->model;
				
				foreach ($items as $item) {
                    Log::channel('documents')
                        ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() .' ' . $item->id . ' ===');
					
					// Склад
					$stock = $item->cmv->stock;
					
					Log::channel('documents')
						->info('Существует склад ' . $stock->getTable() . ' c id: ' . $stock->id);
					
					$stock_count = $stock->count;
					
					Log::channel('documents')
						->info('Значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);
					
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
					
					$min = ConsignmentsItem::moderatorLimit($answer)
						->companiesLimit($answer)
					->where([
						'cmv_id' => $item->cmv_id,
						'cmv_type' => $item->cmv_type,
					])
						->whereHas('consignment', function ($q) use ($consignment) {
							$q->where('is_posted', true)
								->where('id', '!=', $consignment->id);
						})
						->min('price');
//					dd($min);

                    $max = ConsignmentsItem::moderatorLimit($answer)
	                    ->companiesLimit($answer)
					->where([
                        'cmv_id' => $item->cmv_id,
                        'cmv_type' => $item->cmv_type,
                    ])
                        ->whereHas('consignment', function ($q) use ($consignment) {
                            $q->where('is_posted', true)
                                ->where('id', '!=', $consignment->id);
                        })
                        ->min('price');
//					dd($max);

                    if (is_null($min) || is_null($max)) {
	                    $average = 0;
                    } else {
                        $average = (($stock_count * $cost->average) - ($item->count * $item->price)) / $stock->count;
                    }
					$cost->min = $min;
					$cost->max = $max;
					$cost->average = $average;
					$cost->save();
//					dd($cost);
					
					Log::channel('documents')
						->info('Обновлены значения min: ' . $cost->min . ', max: ' . $cost->max . ', average: ' . $cost->average);

                    Log::channel('documents')
                        ->info('=== КОНЕЦ ПЕРЕБОРА ПУНКТА ===
                        ');
					
				}
			}
			
			$consignment->update([
				'is_posted' => false,
				'amount' => $this->getAmount($consignment)
			]);
			
			Log::channel('documents')
				->info('Откат оприходования накладной c id: ' . $consignment->id);
			Log::channel('documents')
				->info('========================================== КОНЕЦ ОТМЕНЫ ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ ==============================================
				
				');
			
			return redirect()->route('consignments.index');
		} else {
			abort(403, 'Накладная пуста');
		}
	}
	
	public function reposting(Request $request)
	{
		// Подключение политики
		$this->authorize(getmethod('index'), $this->class);
		
		// Получаем из сессии необходимые данные (Функция находиться в Helpers)
		$answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('index'));
		
		// ГЛАВНЫЙ ЗАПРОС:
		$consignments = Consignment::with([
			'items' => function($q) {
				$q->with([
					'cmv' => function ($q) {
						$q->with([
							'article',
							'cost',
							'stock'
						]);
					},
					'entity'
				]);
			},
		])
		->moderatorLimit($answer)
			->authors($answer)
			->systemItem($answer)
			->where('is_posted', true)
			->get();
//		dd($consignments);


		Schema::disableForeignKeyConstraints();

//        Log::channel('documents')
//            ->info('=== Очищаем таблицы receipts, cost, cost_histories ===');
//		CostsHistory::truncate();
//		Cost::truncate();
//		Receipt::truncate();

//		dd('очистка');
		
		foreach($consignments as $consignment) {
            if ($consignment->items->isNotEmpty()) {

//                $grouped_items = $consignment->items->groupBy('entity.alias');
////                dd($grouped_items);
//
//                foreach ($grouped_items as $alias => $items) {
//                    $entity_stock = Entity::where('alias', $alias . '_stocks')->first();
//                    $model_stock = 'App\\' . $entity_stock->model;
//                    $model_stock::truncate();
//
//                    Log::channel('documents')
//                        ->info('=== Очищаем таблицу склада ' . $entity_stock->alias . ' ===');
//                }

                Log::channel('documents')
                    ->info('========================================== НАЧАЛО ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ, ID: ' . $consignment->id . ' ==============================================');

                foreach ($consignment->items as $item) {
                    $this->receipt($item);
                }

                $consignment->update([
                    'is_posted' => true,
                    'amount' => $this->getAmount($consignment)
                ]);

                Log::channel('documents')
                    ->info('Оприходована накладная c id: ' . $consignment->id);
                Log::channel('documents')
                    ->info('========================================== КОНЕЦ ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ ==============================================
				
				');

                return redirect()->route('consignments.index');
            } else {
                abort(403, 'Накладная пуста');
            }
		}
		
		Schema::enableForeignKeyConstraints();
		
		return redirect()->route('consignments.index');
	}

	public function getAmount($consignment)
    {
        $amount = 0;
        $consignment->load('items');
        if ($consignment->items->isNotEmpty()) {
            $amount = $consignment->items->sum('amount');
        }
        return $amount;
    }
}
