<?php

namespace App\Http\View\Composers\System;

use App\EventsCategory;
use Illuminate\View\View;

class EventsCategoriesWithEventsComposer
{
	public function compose(View $view)
	{
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('events_categories', false, 'index');

        $eventsCategories = EventsCategory::with([
            'events' => function ($q) {
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
                                'unit',
                                'unit_length'
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
            ->whereHas('events', function ($q) {
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
//        dd($eventsCategories);

        $events = [];
        foreach($eventsCategories as $eventsCategory) {
            foreach ($eventsCategory->events as $item) {
//                $item->category = $relatedCategory;
                $events[] = $item;
            }
        };
        $events = collect($events);

        return $view->with(compact('eventsCategories', 'events'));
    }
}
