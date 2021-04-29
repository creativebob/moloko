<?php

namespace App\Http\View\Composers\System;

use App\Entity;
use Illuminate\View\View;

class ProcessesCategoriesWithItemsComposer
{
	public function compose(View $view)
	{
	    $alias = $view->processAlias;

        $autoInitiated = null;
	    if (isset($view->autoInitiated)) {
	        $autoInitiated = $view->autoInitiated;
        }

	    $entity = Entity::where('alias', "{$view->processAlias}_categories")
            ->first([
                'id',
                'name',
                'alias',
                'model'
            ]);

        // Получаем из сессии необходимые данные
        $answer = operator_right($entity->alias, false, 'index');

        $categories = $entity->model::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->with([
                $alias => function ($q) use ($autoInitiated) {
                    $q->with([
                            'process.unit'
                        ])
                        ->where('archive', false)
                        ->whereHas('process', function ($q) use ($autoInitiated) {
	        	            $q->where('draft', false)
                                ->when(isset($autoInitiated), function ($q) use ($autoInitiated) {
                                    $q->where('is_auto_initiated', $autoInitiated);
                                })
//                            ->whereNotNull('manufacturer_id')
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

        $items = [];
        foreach($categories as $category) {
            $items = array_merge($items, $category->$alias->toArray());
        }

        $categoriesTree = buildTree($categories);
//        dd($categoriesTree);

        return $view->with(compact('categoriesTree', 'items'));
    }

}
