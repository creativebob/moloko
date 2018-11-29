<?php

namespace App\Http\ViewComposers;

use App\Company;
use App\Manufacturer;
use Illuminate\Support\Facades\Auth;

use Illuminate\View\View;

class ManufacturersComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('manufacturers', false, 'index');

        $manufacturers_array = Manufacturer::moderatorLimit($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->where('company_id', Auth::user()->company_id)
        ->orderBy('sort', 'asc')
        ->get(['manufacturer_id'])
        ->keyBy('manufacturer_id')
        ->toArray();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_company = operator_right('companies', false, 'index');

        // Главный запрос
        $manufacturers = Company::moderatorLimit($answer_company)
        ->orderBy('sort', 'asc')
        ->findOrFail($manufacturers_array);
        // dd($manufacturers);

        // dd($view);

        return $view->with('manufacturers', $manufacturers);
    }

}