<?php

namespace App\Http\View\Composers\System;

use App\RawsCategory;

use Illuminate\View\View;

class RawsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('raws_categories', false, 'index');

        $rawsCategories = RawsCategory::with([
            'raws' => function ($q) {
                $q->where('archive', false)
                    ->whereHas('article', function ($q) {
                        $q->where([
                            'draft' => false,
                        ]);
                    })
                    ->with([
                        'article' => function ($q) {
                            $q->with([
                                'unit',
                            ])
                                ->where([
                                    'draft' => false,
                                ]);
                        },
                        'category',
                        'unit_for_composition',
                        'unit_portion',
                        'costs',
                    ])
                    ->orderBy('sort');
            }
        ])
        ->whereHas('raws', function ($q) {
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
//        dd($raws_categories);

        $raws = [];
        foreach($rawsCategories as $rawsCategory) {
            foreach ($rawsCategory->raws as $item) {
//                $item->category = $relatedCategory;
                $raws[] = $item;
            }
        };
        $raws = collect($raws);

        return $view->with(compact('rawsCategories', 'raws'));
    }

}
