<?php

namespace App\Http\ViewComposers;

use App\Entity;

use Illuminate\View\View;

class CompositionsCategoriesComposer
{
	public function compose(View $view)
	{

        $item = $view->item;
        $alias = ($item->set_status == 1) ? $item->category->getTable() : $item->category->getTable();
        $entity = Entity::whereAlias($alias)->first(['model']);
        $model = 'App\\'.$entity->model;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($alias, false, 'index');

        // $columns = [
        //     'id',
        //     'name',
        //     'parent_id'
        // ];

        $categories = $model::with('articles')
        ->whereHas('articles')
        ->moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->get();
        // dd($categories);

        return $view->with(compact('categories'));
    }

}