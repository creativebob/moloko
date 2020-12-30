<?php

namespace App\Http\View\Composers\System;

use App\WorkflowsCategory;
use Illuminate\View\View;

class WorkflowsCategoriesWithWorkflowsComposer
{
	public function compose(View $view)
	{
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('workflows_categories', false, 'index');

        $workflowsCategories = WorkflowsCategory::with([
            'workflows' => function ($q) {
                $q->where('archive', false)
                    ->whereHas('process', function ($q) {
                        $q->where([
                            'draft' => false,
                            'kit' => false
                        ]);
                    })
                    ->with([
                        'process' => function ($q) {
                            $q->with([
                                'unit'
                            ])
                                ->where([
                                    'draft' => false,
                                    'kit' => false
                                ]);
                        },
                        'category',
//                        'unit_for_composition',
//                        'costs',
                    ])
                    ->orderBy('sort');
            }
        ])
            ->whereHas('workflows', function ($q) {
                $q->where('archive', false)
                    ->whereHas('process', function ($q) {
                        $q->where([
                            'draft' => false,
                            'kit' => false
                        ]);
                    });
            })
            ->moderatorLimit($answer)
            ->systemItem($answer)
            ->companiesLimit($answer)
            ->orderBy('sort', 'asc')
            ->get();
//        dd($workflowsCategories);

        $workflows = [];
        foreach($workflowsCategories as $workflowsCategory) {
            foreach ($workflowsCategory->workflows as $item) {
//                $item->category = $relatedCategory;
                $workflows[] = $item;
            }
        };
        $workflows = collect($workflows);

        return $view->with(compact('workflowsCategories', 'workflows'));
    }
}
