<?php

namespace App\Http\ViewComposers;

use App\Entity;

use Illuminate\View\View;

class CompositionsCategoriesComposer
{
	public function compose(View $view)
	{

        $item = $view->item;
        $article = $view->article;

        if ($item->set_status == 1) {
            // Если набор, то состоит из себя
            $alias = $item->category->getTable();
        } else {
            $entity = Entity::with('consist')
            ->whereAlias($item->getTable())
            ->first();

            if (isset($entity->consist)) {
                $alias = $entity->consist->ancestor->alias;
            } else {
                $alias = null;
            }
        }

        if (isset($alias)) {
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
        } else {
            $categories = collect();
            $alias = null;
        }


        return $view->with(compact('categories', 'article'));
    }

}