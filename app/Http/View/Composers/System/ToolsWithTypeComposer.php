<?php

namespace App\Http\View\Composers\System;

use App\Tool;
use Illuminate\View\View;

class ToolsWithTypeComposer
{
	public function compose(View $view)
	{
        $type = $view->type;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('tools', false, getmethod('index'));

        $tools = Tool::with([
            'article'
        ])
        ->whereHas('type', function ($q) use ($type) {
            $q->where('alias', $type);
        })
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->where('archive', false)
        ->get();
//        dd($tools);

        return $view->with(compact('tools'));
    }

}
