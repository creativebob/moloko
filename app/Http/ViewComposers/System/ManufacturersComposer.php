<?php

namespace App\Http\ViewComposers\System;

use App\Manufacturer;
use Illuminate\Support\Facades\Auth;

use Illuminate\View\View;

class ManufacturersComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('manufacturers', false, 'index');

        // Главный запрос
        $manufacturers = Manufacturer::with('company')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        // ->toBase()
        ->get();
        // dd($manufacturers);
        // dd($company->$name);

        return $view->with(compact('manufacturers'));

    }
}