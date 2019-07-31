<?php

namespace App\Http\ViewComposers;

use App\Supplier;

use Illuminate\View\View;

class SupplierSelectComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('suppliers', false, 'index');

        // Главный запрос
        $suppliers = Supplier::with('company')
        ->companiesLimit($answer)
        ->moderatorLimit($answer)
        // ->authors($answer)
        ->systemItem($answer)
        // ->template($answer)
        ->orderBy('sort', 'asc')
        ->get();

        // dd($suppliers);

        return $view->with('suppliers', $suppliers);

    }
}