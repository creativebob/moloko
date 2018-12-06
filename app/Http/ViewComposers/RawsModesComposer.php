<?php

namespace App\Http\ViewComposers;

use App\RawsMode;

use Illuminate\View\View;

class RawsModesComposer
{
	public function compose(View $view)
	{

        $raws_modes = RawsMode::orderBy('sort', 'asc')
        ->get(['id', 'name']);

        return $view->with('raws_modes', $raws_modes);
    }

}