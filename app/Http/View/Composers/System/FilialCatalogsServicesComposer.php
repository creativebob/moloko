<?php

namespace App\Http\View\Composers\System;

use App\CatalogsService;

use Illuminate\View\View;

class FilialCatalogsServicesComposer
{
	public function compose(View $view)
	{
        $filialId = $view->filialId;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_services', false, 'index');

        // TODO - 25.12.19 - Нужна разбивка каталогово по филиалау пользователя

        // Главный запрос
        $catalogsServices = CatalogsService::moderatorLimit($answer)
        ->companiesLimit($answer)
            ->whereHas('filials', function ($q) use ($filialId) {
                $q->where('id', $filialId);
            })
        ->toBase()
        ->get();
        // dd($catalogsServices);

        return $view->with(compact('catalogsServices'));
    }

}
