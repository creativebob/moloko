<?php

namespace App\Http\ViewComposers\System;

use App\ServicesCategory;

use Illuminate\View\View;

class ServicesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('services_categories', false, 'index');

        // $columns = [
        //     'id',
        //     'name',
        //     'parent_id'
        // ];

        $services_categories = ServicesCategory::
        with([
            'services' => function ($q) {
                $q->with([
                    'process' => function ($q) {
                        $q->where([
                            'draft' => false,
                            'set' => false
                        ]);
                    }
                ])
                    ->where('archive', false);
            }
        ])
        ->
        whereHas('services', function ($q) {
            $q->whereHas('process', function ($q) {
                $q->where([
                    'draft' => false,
                    'set' => false
                ]);
            })
                ->where('archive', false);
        })
        ->moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->orderBy('sort', 'asc')
        ->get();
//         dd($services_categories->first()->services);

        return $view->with(compact('services_categories'));
    }
}
