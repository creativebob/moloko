<?php

namespace App\Http\ViewComposers\System;

use App\Account;

use Illuminate\View\View;

class AccountsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('accounts', false, 'index');

        // Главный запрос
        $accounts = Account::with([
            'source_service' => function ($q) {
                $q->with([
                    'source:id,name'
                ])
                ->select([
                    'id',
                    'name',
                    'source_id'
                ]);
            }
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get();

        return $view->with(compact('accounts'));
    }

}
