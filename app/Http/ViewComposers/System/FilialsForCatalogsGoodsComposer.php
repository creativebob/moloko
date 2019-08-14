<?php

namespace App\Http\ViewComposers\System;

use App\Department;
use App\CatalogsGoods;

use Illuminate\View\View;

class FilialsForCatalogsGoodsComposer
{
	public function compose(View $view)
	{

        $catalog = CatalogsGoods::findOrFail($view->catalog_id);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('departments', true, 'index');

        // Главный запрос
        $filials = Department::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->whereNull('parent_id')
        ->where('company_id', $catalog->company_id)
        ->get();
        // dd($filials);

        return $view->with(compact('filials'));
    }

}
