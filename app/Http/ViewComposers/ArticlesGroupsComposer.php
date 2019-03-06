<?php

namespace App\Http\ViewComposers;

use App\ArticlesGroup;

use Illuminate\View\View;

class ArticlesGroupsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('articles_groups', false, 'index');

        // Главный запрос
        $articles_groups = ArticlesGroup::moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        // ->whereDisplay(1)
        // ->has('albums')
        ->orderBy('sort', 'asc')
        ->get(['id','name']);
        // dd($articles_groups);

        return $view->with('articles_groups', $articles_groups);
    }

}