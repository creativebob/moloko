<?php

namespace App\Http\View\Composers\System;

use App\ContainersCategory;

use Illuminate\View\View;

class ContainersComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('containers_categories', false, 'index');

        $containersCategories = ContainersCategory::with([
            'containers' => function ($q) {
                $q->where('archive', false)
                    ->whereHas('article', function ($q) {
                        $q->where([
                            'draft' => false,
                        ]);
                    })
                    ->with([
                        'article' => function ($q) {
                            $q->with([
                                'unit'
                            ])
                                ->where([
                                    'draft' => false,
                                ]);
                        },
                        'category',
                        'unit_for_composition',
                        'costs',
                    ])
                    ->orderBy('sort');
            }
        ])
        ->whereHas('containers', function ($q) {
            $q->where('archive', false)
            ->whereHas('article', function ($q) {
                $q->where('draft', false);
            });
        })
        ->moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->orderBy('sort', 'asc')
        ->get();
//        dd($containers_categories);

        $containers = [];
        foreach($containersCategories as $containersCategory) {
            foreach ($containersCategory->containers as $item) {
//                $item->category = $relatedCategory;
                $containers[] = $item;
            }
        };
        $containers = collect($containers);

        return $view->with(compact('containersCategories', 'containers'));
    }

}
