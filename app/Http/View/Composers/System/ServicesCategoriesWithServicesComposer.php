<?php

namespace App\Http\View\Composers\System;

use App\ServicesCategory;
use Illuminate\View\View;

class ServicesCategoriesWithServicesComposer
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

        $servicesCategories = ServicesCategory::with([
            'services' => function ($q) {
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
//                        'unit_for_composition'
                    ])
                    ->orderBy('sort');
            }
        ])
            ->whereHas('services', function ($q) {
                $q->whereHas('process', function ($q) {
                    $q->where([
                        'draft' => false,
                        'kit' => false
                    ]);
                })
                    ->where('archive', false);
            })
            ->moderatorLimit($answer)
            ->systemItem($answer)
            ->companiesLimit($answer)
            ->orderBy('sort')
            ->get();
//         dd($servicesCategories);

        $services = [];
        foreach ($servicesCategories as $servicesCategory) {
            foreach ($servicesCategory->services as $item) {
//                $item->category = $relatedCategory;
                $services[] = $item;
            }
        };
        $services = collect($services);

        return $view->with(compact('servicesCategories', 'services'));
    }
}
