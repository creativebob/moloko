<?php

namespace App\Http\ViewComposers;

use App\Property;

use Illuminate\View\View;

class PropertiesComposer
{
	public function compose(View $view)
	{
		// Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_properties = operator_right('properties', false, 'index');
        $answer_metrics = operator_right('metrics', false, 'index');

        $properties = Property::moderatorLimit($answer_properties)
        ->companiesLimit($answer_properties)
        ->authors($answer_properties)
        ->systemItem($answer_properties)
        ->template($answer_properties)
        ->with(['metrics' => function ($query) use ($answer_metrics) {
            $query->with('values')
            ->moderatorLimit($answer_metrics)
            ->companiesLimit($answer_metrics)
            ->authors($answer_metrics)
            ->systemItem($answer_metrics);
        }])
        ->withCount('metrics')
        ->orderBy('sort', 'asc')
        ->get();

        return $view->with('properties', $properties);
    }
}