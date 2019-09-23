<?php

namespace App\Http\ViewComposers\System;

use App\Entity;
use Illuminate\View\View;

class ArticlesCategoriesWithGroupsComposer
{
	public function compose(View $view)
	{

        $entity = Entity::whereAlias($view->category_entity)->first(['model']);
        $model = 'App\\'.$entity->model;

        // Получаем из сессии необходимые данные
        $answer = operator_right($entity, false, 'index');

        $categories = $model::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->has('groups')
        ->with([
            'groups:id,name'
        ])
            ->get([
                'id',
                'name',
                'parent_id',
                'level'
            ]);
       // dd($categories);

        $groups = [];
        foreach($categories as $category) {
            if (isset($category->groups)) {
                foreach ($category->groups as $group) {
                    $group->category_id = $category->id;
                    $groups[] = $group;
                }
            }
        }
//        dd($groups);

        $categories_tree = buildTree($categories);
//        dd($categories_tree);

        return $view->with([
            'categories_tree' => json_encode($categories_tree),
            'groups' => json_encode($groups)
        ]);
    }

}
