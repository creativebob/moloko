<?php

namespace App\Http\ViewComposers;

use App\RawsCategory;

use Illuminate\View\View;

class RawsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('raws_categories', false, 'index');

        $raws_categories = RawsCategory::with([
            'raws' => function ($q) {
                $q->with([
                    'article' => function ($q) {
                        $q->where('draft', false);
                    }
                ])
                    ->where('archive', false);
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

        return $view->with(compact('raws_categories'));
    }

}
