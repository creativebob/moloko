<?php

namespace App\Http\View\Composers\System;

use App\Entity;
use App\GoodsCategory;

use Illuminate\View\View;

class PartsComposer
{
	public function compose(View $view)
	{
	    $alias = $view->item->getTable();
	    $categoryAlias = "{$alias}_categories";

	    $model = Entity::where('alias', $categoryAlias)
            ->value('model');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($categoryAlias, false, 'index');

        $id = $view->id ?? null;

        $partsCategories = $model::with([
            $alias => function ($q) use ($id) {
                $q->where('archive', false)

                    ->when($id, function ($q) use ($id) {
                        $q->where('id', '!=', $id);
                    })

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
        ->whereHas($alias, function ($q) {
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
//         dd($relatedCategories);

        $parts = [];
        foreach($partsCategories as $relatedCategory) {
            foreach ($relatedCategory->$alias as $item) {
//                $item->category = $relatedCategory;
                $parts[] = $item;
            }
        };

        return $view->with(compact('partsCategories', 'parts'));
    }
}
