<?php

namespace App\Http\Controllers;

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
        $this->authorize(getmethod('store'), $this->class);

        $consignment = (new Consignment())->create();
        // dd($consignment);

	    return redirect()->route('consignments.edit', ['id' => $consignment->id]);
    }


    public function store(Request $request)
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

        $consignment = Consignment::with([
            'items' => function ($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'article.unit'
                        ]);
                    },
                    'entity:id,name',
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


    public function destroy(Request $request, $id)
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
            ->with([
                $entity_alias => function ($q) {
                    $q->with([
                        'article.unit'
                    ])
                        ->where('archive', false)
                        ->whereHas('article', function ($q) {
                            $q->where('draft', false);
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
							'article'
						]);
					},
					'entity'
				]);
			},
		]);
//		dd($consignment);
		
		if ($consignment->items->isNotEmpty()) {
			
			$grouped_items = $consignment->items->groupBy('entity.alias');
//			dd($grouped_items);
			
			foreach ($grouped_items as $alias => $items) {
				$entity = Entity::where('alias', $alias.'_stocks')->first();
				$model = 'App\\'.$entity->model;
				
				foreach ($items as $item) {
					
					$stock = $model::firstOrNew([
						'cmv_id' => $item->cmv_id,
						'manufacturer_id' => $item->cmv->article->manufacturer_id,
						'stock_id' => $consignment->stock_id,
					]);
					
					$stock->filial_id = $consignment->filial_id;
					
					$stock_count = $stock->count;
					
					$stock->count += $item->count;
					$stock->weight += ($item->cmv->article->weight * $item->count);
					$stock->volume += ($item->cmv->article->volume * $item->count);
					$stock->save();
					
					$cost = Cost::firstOrNew([
						'cmv_id' => $item->cmv_id,
						'cmv_type' => $item->cmv_type,
						'manufacturer_id' => $item->cmv->article->manufacturer_id,
					]);
					
					if ($cost->id) {
						$cost->min = ($item->price < $cost->min) ? $item->price : $cost->min;
						$cost->max = ($item->price > $cost->max) ? $item->price : $cost->max;
						
						$cost_average = $cost->average;
						$average = (($stock_count * $cost_average) + ($item->count * $item->price)) / $stock->count;
						$cost->average = $average;
					} else {
						$cost->min = $item->price;
						$cost->max = $item->price;
						$cost->average = $item->price;
					}
					$cost->save();
//					dd($cost);
				}
			}
			
			$consignment->update([
				'is_posted' => true,
				'amount' => $this->getAmount($consignment)
			]);
			
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
			
			$grouped_items = $consignment->items->groupBy('entity.alias');
//			dd($grouped_items);
			
			foreach ($grouped_items as $alias => $items) {
				$entity = Entity::where('alias', $alias.'_stocks')->first();
				$model = 'App\\'.$entity->model;
				
				foreach ($items as $item) {
					
					$stock = $model::where([
						'cmv_id' => $item->cmv_id,
						'manufacturer_id' => $item->cmv->article->manufacturer_id,
						'stock_id' => $consignment->stock_id,
					])
						->first();
					
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
					dd(ConsignmentsItem::where([
						'cmv_id' => $item->cmv_id,
						'cmv_type' => $item->cmv_type,
						'manufacturer_id' => $item->cmv->article->manufacturer_id,
					])
						->whereHas('consignment', function ($q) {
							$q->where('is_posted', true);
						})
						->min('price'));
					
					$cost->min = ConsignmentsItem::where([
						'cmv_id' => $item->cmv_id,
						'cmv_type' => $item->cmv_type,
						'manufacturer_id' => $item->cmv->article->manufacturer_id,
					])
						->whereHas('consignment', function ($q) {
							$q->where('is_posted', true);
						})
						->min('price');
					
					$cost->max = ConsignmentsItem::where([
						'cmv_id' => $item->cmv_id,
						'cmv_type' => $item->cmv_type,
						'manufacturer_id' => $item->cmv->article->manufacturer_id,
					])
						->whereHas('consignment', function ($q) {
							$q->where('is_posted', true);
						})
						->max('price');
					
					$average = (($stock->count * $cost->average) - ($item->count * $item->price)) / ($stock->count - $item->count);
					$cost->average = $average;
					
					$cost->save();
//					dd($cost);
				}
			}
			
			$consignment->update([
				'is_posted' => false,
				'amount' => $this->getAmount($consignment)
			]);
			
			return redirect()->route('consignments.index');
		} else {
			abort(403, 'Накладная пуста');
		}
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
