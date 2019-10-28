<?php

namespace App\Http\ViewComposers\System;

use App\Entity;
use Illuminate\View\View;

class ArticlesCategoriesWithItemsComposerForManufacturer
{
	public function compose(View $view)
	{

	    $entities = Entity::whereIn('alias', [
            'raws',
            'containers',
            'goods',
            'attachments'
        ])
            ->get([
                'id',
                'name',
                'alias'
            ]);

	    $entity = $entities->first();
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

        $articles_categories_with_items_data = [
            'entities' => $entities,
            'categories' => $categories_tree,
            'items' => $items
        ];

        return $view->with(compact('articles_categories_with_items_data'));
    }

}
