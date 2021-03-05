<?php

namespace App\Http\View\Composers\System;

use App\Entity;
use App\Manufacturer;
use Illuminate\View\View;

class CmvManufacturersComposer
{
    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
	    $alias = $view->entity;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($view->entity, false, 'index');

        $model = Entity::where('alias', $alias)
            ->value('model');

        $itemsIds = $model::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
//            ->filter()
            ->where('archive', false)
            ->pluck('id');
//        dd($itemsIds);

        $answerManufacturers = operator_right('manufacturers', false, 'index');

        $manufacturers = Manufacturer::with([
            'company:id,name'
        ])
        ->moderatorLimit($answerManufacturers)
            ->companiesLimit($answerManufacturers)
            ->authors($answerManufacturers)
            ->systemItem($answerManufacturers)
            ->whereHas($alias, function ($q) use ($alias, $itemsIds) {
                $q->whereIn("{$alias}.id", $itemsIds);
            })
            ->where('archive', false)
            ->get();

        return $view->with(compact('manufacturers'));
    }
}
