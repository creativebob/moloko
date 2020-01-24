<?php

namespace App\Http\ViewComposers\System;

use App\Entity;
use Illuminate\View\View;

class ProcessesCategoriesWithGroupsComposer
{
	public function compose(View $view)
	{

        $entity = Entity::whereAlias($view->category_entity)->first(['model']);
        $model = 'App\\'.$entity->model;

        // Получаем из сессии необходимые данные
        $answer = operator_right($entity->alias, false, 'index');

        $categories = $model::moderatorLimit($answer)
        ->companiesLimit($answer)
//        ->has('groups')
        ->with([
            'groups:id,name'
        ])
            ->get([
                'id',
                'name',
                'parent_id',
                'level'
            ]);
//        dd($categories);

        $categories_tree = buildTreeArray($categories);
//        dd($categories_tree);

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

        return $view->with(compact('categories_tree', 'groups'));
    }

}
