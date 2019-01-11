<?php

namespace App\Http\ViewComposers;

use App\Direction;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DirectionsComposer
{
	public function compose(View $view)
	{

        // Главный запрос
        $directions = Direction::with('category')
        ->where('company_id', Auth::user()->company_id)
        // ->where('archive', false)
        ->get();
        // dd($directions);

        return $view->with('directions', $directions);
    }

}