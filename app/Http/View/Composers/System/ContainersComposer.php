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

        $containers_categories = ContainersCategory::with([
            'containers' => function ($q) {
                $q->with([
                    'article' => function ($q) {
                        $q->where('draft', false);
                    }
                ])
                    ->where('archive', false);
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

        return $view->with(compact('containers_categories'));
    }

}
