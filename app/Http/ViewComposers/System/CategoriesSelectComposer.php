<?php

namespace App\Http\ViewComposers\System;

use App\Entity;
use Illuminate\View\View;

class CategoriesSelectComposer
{
	public function compose(View $view)
	{
        $entity = Entity::whereAlias($view->entity)->first(['model']);
        $model = 'App\\'.$entity->model;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($view->entity, false, 'index');

        $columns = [
            'id',
            'name',
            'parent_id'
        ];

        // Главный запрос
        $items = $model::moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->toBase()
        ->get($columns);
        // dd($items);

        $items_list = getSelectTree($items, ($view->parent_id ?? null), null, ($view->id ?? null));

        return $view->with(compact('items_list'));
    }

}