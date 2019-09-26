<?php

namespace App\Http\ViewComposers\System;

use App\Entity;
use Illuminate\View\View;

class ArticlesCategoriesWithItemsComposer
{
	public function compose(View $view)
	{
        $entity_alias = 'raws';
        $alias = $entity_alias.'_categories';

        $entity = Entity::whereAlias($alias)->first(['model']);
        $model = 'App\\'.$entity->model;

        // Получаем из сессии необходимые данные
        $answer = operator_right($entity->alias, false, 'index');

        $categories = $model::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->with([
                $entity_alias.'.article:id,name'
            ])
	        ->whereHas($entity_alias.'.article', function ($q) {
	        	$q->where('draft', false);
	        })
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

            if (isset($category->$entity_alias)) {
                foreach ($category->$entity_alias as $item) {
                    $item->article->category_id = $category->id;
                    $items[] = $item->article;
                }
            }

            if (isset($category->childCategories)) {
                if (isset($category->$entity_alias)) {
                    foreach ($category->childCategories as $childCategory) {
                        foreach ($childCategory->$entity_alias as $item) {
                            $item->article->category_id = $childCategory->id;
                            $items[] = $item->article;
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

        return $view->with(compact('articles_categories_with_items_data'));
    }

}
