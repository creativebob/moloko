<?php

namespace App\Http\ViewComposers;

use App\Entity;
use Illuminate\View\View;

class CategoriesSelectComposer
{
	public function compose(View $view)
	{
                $entity = Entity::whereAlias($view->entity)->first();
                $model = 'App\\'.$entity->model;
                // Получаем из сессии необходимые данные (Функция находиться в Helpers)
                $answer = operator_right($view->entity, false, 'index');

                // Главный запрос
                $items = $model::moderatorLimit($answer)
                ->systemItem($answer)
                ->orderBy('sort', 'asc')
                ->get(['id','name','parent_id']);

                // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
                $items_list = getSelectTree($items, ($view->parent_id ?? null), ($view->disable ?? null), ($view->id ?? null));

                return $view->with(compact('items_list'));
        }

}