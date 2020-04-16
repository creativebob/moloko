<?php

namespace App\Http\View\Composers\System;

use App\Client;
use Illuminate\View\View;

class ClientsCountComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('clients', false, 'index');

        $clients_count = Client::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->count();
//        dd($employees_dismissal_count);

        return $view->with(compact('clients_count'));
    }

}
