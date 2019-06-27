<?php

namespace App\Http\ViewComposers;

use App\Account;

use Illuminate\View\View;

class AccountsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('accounts', false, 'index');

        // Главный запрос
        $accounts = Account::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get();

        return $view->with('accounts', $accounts);
    }

}
