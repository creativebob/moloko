<?php

namespace App\Http\View\Composers\System;

use App\Entity;
use Illuminate\View\View;

class CategoriesSelectComposer
{
	public function compose(View $view)
	{
        $entity_model = Entity::whereAlias($view->entity)->value('model');
        $model = 'App\\' . $entity_model;

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
        ->get();
        // dd($items);

        $items_list = getSelectTree($items, ($view->parent_id ?? null), null, ($view->id ?? null));

        return $view->with(compact('items_list'));
    }

}
