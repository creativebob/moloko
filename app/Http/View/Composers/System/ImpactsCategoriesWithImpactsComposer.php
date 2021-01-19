<?php

namespace App\Http\View\Composers\System;

use App\ImpactsCategory;

use Illuminate\View\View;

class ImpactsCategoriesWithImpactsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('impacts_categories', false, 'index');

        $impactsCategories = ImpactsCategory::with([
            'impacts' => function ($q) {
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
                        'category'
                ])
                    ->orderBy('sort');
            }
        ])
        ->whereHas('impacts', function ($q) {
            $q->where('archive', false)
                ->whereHas('article', function ($q) {
                    $q->where([
                        'draft' => false,
                    ]);
            });
        })
        ->moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->orderBy('sort')
        ->get();
//         dd($impactsCategories);

        $impacts = [];
        foreach($impactsCategories as $impactsCategory) {
            foreach ($impactsCategory->impacts as $item) {
//                $item->category = $impactsCategory;
                $impacts[] = $item;
            }
        };

        return $view->with(compact('impactsCategories', 'impacts'));
    }
}
