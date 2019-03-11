<?php

namespace App\Http\ViewComposers;

use App\Manufacturer;
use Illuminate\Support\Facades\Auth;

use Illuminate\View\View;

class ContragentsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($name, false, 'index');

        if (isset(Auth::user()->company_id)) {

            // Главный запрос
            $manufacturers = Manufacturer::with('company')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->get(['id', 'company.name']));

            // dd($company->$name);

            return $view->with(compact('manufacturers'));

        }
    }
}