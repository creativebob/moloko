<?php

namespace App\Http\ViewComposers\System;

use App\AttachmentsCategory;

use Illuminate\View\View;

class AttachmentsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('attachments_categories', false, 'index');

        $attachments_categories = AttachmentsCategory::with([
            'attachments' => function ($q) {
                $q->with([
                    'article' => function ($q) {
                        $q->where('draft', false);
                    }
                ])
                    ->where('archive', false);
            }
        ])
        ->whereHas('attachments', function ($q) {
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
//        dd($attachments_categories);

        return $view->with(compact('attachments_categories'));
    }

}
