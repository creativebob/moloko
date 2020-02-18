<?php

namespace App\Http\View\Composers\System;

use App\RawsCategory;

use Illuminate\View\View;

class CompositionsCategoriesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('raws_categories', false, 'index');

        // $columns = [
        //     'id',
        //     'name',
        //     'parent_id'
        // ];

        $raws_categories = RawsCategory::whereHas('raws', function ($q) {
            $q->where('archive', false)
            ->whereHas('article', function ($q) {
                $q->where('draft', false);
            });
        })
        ->moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->get();
        // dd($raws_categories);

        return $view->with(compact('raws_categories'));
    }
}
