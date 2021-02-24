<?php

namespace App\Http\View\Composers\System;

use App\Client;
use App\User;
use Illuminate\View\View;

class ClientsForSearchComposer
{
    /**
     * Пользователи
     */
    protected $users;

    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        $data = [];

        $answer = operator_right('clients', true, 'index');

        $clientsCount = Client::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->count();

        if ($clientsCount > 1000) {
            $data = [
                'mode' => 2
            ];
        } else {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right('users', true, 'index');

            $users = User::with([
                'client.clientable' => with([
                    'location',
                    'main_phones'
                ]),
                'organizations.client',
            ])
                ->where('site_id', '!=', 1)
                ->moderatorLimit($answer)
                ->companiesLimit($answer)
                ->authors($answer)
                ->systemItem($answer)
                ->get();

            $company = auth()->user()->company;

            $company->load([
                'organizations' => function ($q) {
                    $q->with([
                        'client.clientable',
                        'representatives' => function ($q) {
                            $q->with([
                                'client'
                            ])
                                ->latest();
                        }
                    ]);
                }
            ]);

            $data = [
                'mode' => 1,
                'users' => $users,
                'companies' => $company->organizations
            ];
        }

        return $view->with('clientsData', $data);
    }
}
