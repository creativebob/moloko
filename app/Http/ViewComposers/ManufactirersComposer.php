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
        $answer = operator_right('companies', false, 'index');

        // Главный запрос
        $company = Company::with([
            'manufacturers' => function ($q) {
                $q->orderBy('sort', 'asc');
            }
        ])
        ->moderatorLimit($answer)
        ->findOrFail(Auth::user()->company_id);
        // dd($company);

        // $manufacturers = Company::whereHas('manufacturers', function ($q) {
        //     $q->pivot('company_id', Auth::user()->company_id)->orderBy('sort', 'asc');
        // })
        // ->moderatorLimit($answer)
        // ->get();

        // dd($view);

        return $view->with('manufacturers', $company->manufacturers);
    }

}