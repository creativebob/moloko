<?php

namespace App\Http\ViewComposers\System;

use App\LeftoverOperation;

use Illuminate\View\View;

class LeftoverOperationsComposer
{
	public function compose(View $view)
	{

        // Главный запрос
        $leftover_operations = LeftoverOperation::get();

        return $view->with(compact('leftover_operations'));
    }

}