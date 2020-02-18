<?php

namespace App\Http\View\Composers\System;

use App\WorkflowsCategory;

use Illuminate\View\View;

class WorkflowsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('workflows_categories', false, 'index');

        // $columns = [
        //     'id',
        //     'name',
        //     'parent_id'
        // ];

        $workflows_categories = WorkflowsCategory::with([
            'workflows' => function ($q) {
                $q->with([
                    'process' => function ($q) {
                        $q->where('draft', false);
                    }
                ])
                    ->where('archive', false);
            }
        ])
        ->whereHas('workflows', function ($q) {
            $q->where('archive', false)
            ->whereHas('process', function ($q) {
                $q->where('draft', false);
            });
        })
        ->moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->orderBy('sort', 'asc')
        ->get();
        // dd($workflows_categories);

        return $view->with(compact('workflows_categories'));
    }
}
