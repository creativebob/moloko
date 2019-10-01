<?php

namespace App\Http\ViewComposers\System;

use App\Stock;

use Illuminate\View\View;

class StocksComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('stocks', false, 'index');

        // Главный запрос
        $stocks = Stock::with('company')
        ->companiesLimit($answer)
        ->moderatorLimit($answer)
        // ->authors($answer)
        ->systemItem($answer)
        // ->template($answer)
        ->orderBy('sort', 'asc')
        ->get();
        // dd($stocks);

        return $view->with(compact('stocks'));

    }
}