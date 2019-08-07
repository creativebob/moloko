<?php

namespace App\Http\ViewComposers\System;
use Illuminate\View\View;
use App\Entity;

class CategoriesDrilldownComposer
{
	public function compose(View $view)
	{
        $entity = Entity::whereAlias($view->entity)->first();
        $model = 'App\\'.$entity->model;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($entity->alias, false, getmethod('index'));

        // Получаем каталог товаров
        $items = $model::with('products')
        ->withCount('products')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->whereDisplay(1)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        $categories = buildTree($items);

		return $view->with('categories', $categories);

	}

}