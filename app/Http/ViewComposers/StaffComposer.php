<?php

namespace App\Http\ViewComposers;

use App\User;

use Illuminate\View\View;

class StaffComposer
{
	public function compose(View $view)
	{

        // Список пользователей
        $answer = operator_right('users', true, 'index');

        // dd($view->mode);

        if (empty($view->mode)) {

            $staff = User::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->whereNull('god')
            ->has('staff')
            ->orderBy('second_name')
            ->get();
        } else {
            switch ($view->mode) {

                case 'vacancies':
                $staff = User::moderatorLimit($answer)
                ->companiesLimit($answer)
                ->filials($answer)
                ->authors($answer)
                ->systemItem($answer)
                ->whereNull('god')
                ->doesntHave('staff')
                ->orderBy('second_name')
                ->get();
                break;

                default:

                break;
            }
        }
        return $view->with('staff', $staff);
    }
}