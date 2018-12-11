<?php

namespace App\Http\ViewComposers;

use App\Position;

use Illuminate\View\View;

class positionsComposer
{
	public function compose(View $view)
	{

        // Список пользователей
        $answer = operator_right('positions', false, 'index');

        $positions = Position::moderatorLimit($answer)
        ->get();

        return $view->with('positions', $positions);
    }

}