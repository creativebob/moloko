<?php

namespace App\Http\View\Composers\System;

use App\Client;
use App\User;
use Illuminate\View\View;

class UsersWithClientComposer
{
    /**
     * Пользователи
     */
    protected $users;

    /**
     * ClientsUsersComposer constructor.
     */
    public function __construct()
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('users', true, 'index');

        $this->users = User::with([
            'client.clientable',
            'organizations.client',
        ])
            ->where('site_id', '!=', 1)
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->get();
    }

    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        return $view->with('users', $this->users);
    }
}
