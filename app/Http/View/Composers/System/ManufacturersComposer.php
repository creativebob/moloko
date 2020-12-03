<?php

namespace App\Http\View\Composers\System;

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
        $manufacturers = Manufacturer::with([
            'company:id,name'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get([
            'id',
            'company_id',
            'manufacturer_id'
        ]);

        // TODO Медленное решение получения имени компании в список 01.12.2020
        foreach($manufacturers as $manufacturer){
            $manufacturer->name = $manufacturer->company->name;
        }

        // dd($manufacturers);
        // dd($company->$name);

        return $view->with(compact('manufacturers'));

    }
}
