<?php

namespace App\Http\View\Composers\System;

use App\Stock;

use Illuminate\View\View;

class StocksComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('stocks', true, 'index');

        // Главный запрос
        $stocks = Stock::with('company')
        ->companiesLimit($answer)
        ->moderatorLimit($answer)
        ->filials($answer)
        // ->authors($answer)
        ->systemItem($answer)
        // ->template($answer)
        ->oldest('sort')
        ->get();
        // dd($stocks);

        return $view->with(compact('stocks'));

    }
}
