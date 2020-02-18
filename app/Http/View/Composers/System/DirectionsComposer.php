<?php

namespace App\Http\View\Composers\System;

use App\Direction;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DirectionsComposer
{
	public function compose(View $view)
	{
        $answer = operator_right('directions', true, 'index');

        // Главный запрос
        $directions = Direction::with('category')
        ->companiesLimit($answer)
        // ->where('archive', false)
        ->get();
        // dd($directions);

        return $view->with('directions', $directions);
    }

}
