<?php

namespace App\Http\ViewComposers\System;

use App\ProcessesGroup;

use Illuminate\View\View;

class ProcessesGroupsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('processes_groups', false, 'index');

        $relation = $view->entity;
        $category_id = $view->category_id;
        // dd($relation, $category_id);

        // Главный запрос
        $processes_groups = ProcessesGroup::moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->template($answer)
        ->whereHas($relation, function ($q) use ($relation, $category_id) {
            $q->where($relation.'.id', $category_id);
        })
        ->orderBy('sort', 'asc')
        ->toBase()
        ->get(['id','name']);
        // dd($processes_groups);

        return $view->with('processes_groups', $processes_groups);
    }

}
