<?php

namespace App\Http\ViewComposers\System;

use App\ArticlesGroup;

use Illuminate\View\View;

class ArticlesGroupsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('articles_groups', false, 'index');

        $relation = $view->entity;
        $category_id = $view->category_id;
        // dd($relation, $category_id);

        // Главный запрос
        $articles_groups = ArticlesGroup::moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->whereHas($relation, function ($q) use ($relation, $category_id) {
            $q->where($relation.'.id', $category_id);
        })
        ->orderBy('sort', 'asc')
        ->toBase()
        ->get(['id','name']);
        // dd($articles_groups);

        return $view->with('articles_groups', $articles_groups);
    }

}