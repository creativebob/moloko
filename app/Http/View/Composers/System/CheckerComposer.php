<?php

namespace App\Http\View\Composers\System;
use Illuminate\View\View;

class CheckerComposer
{
	public function compose(View $view)
	{

        // Запрос для чекбокса
        $model = $view->model;

        $items = $model::get();
        $name = $view->relation ?? str_plural(snake_case($view->model));
        $entity = $view->entity;
        $title = $view->title;

        // dd($name);

		return $view->with(compact('items', 'name', 'entity', 'title'));

	}

}
