<?php

namespace App\Http\View\Composers\System;

use App\RawsMode;

use Illuminate\View\View;

class RawsModesComposer
{
	public function compose(View $view)
	{
        return $view->with('raws_modes', RawsMode::orderBy('sort', 'asc')->get(['id', 'name']));
    }
}
