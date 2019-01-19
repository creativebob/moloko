<?php

namespace App\Http\ViewComposers;

use App\Company;
use Illuminate\Support\Facades\Auth;

use Illuminate\View\View;

class ContragentsComposer
{
	public function compose(View $view)
	{

        $name = $view->name;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($name, false, 'index');

        if (isset(Auth::user()->company_id)) {

            // Главный запрос
            $company = Company::with([
                $name => function ($q) {
                    $q->orderBy('sort', 'asc');
                }
            ])
            ->moderatorLimit($answer)
            ->findOrFail(Auth::user()->company_id);

            // dd($company->$name);

            // $manufacturers = Company::whereHas('manufacturers', function ($q) {
            //     $q->pivot('company_id', Auth::user()->company_id)->orderBy('sort', 'asc');
            // })
            // ->moderatorLimit($answer)
            // ->get();

            // dd($view);

            return $view->with('contragents', $company->$name);
        } else {

            $companies = Company::where('company_id', null)->get();
            // dd($companies);

            return $view->with('contragents', $companies);
        }


    }

}