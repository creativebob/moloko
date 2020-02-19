<?php

namespace App\Http\View\Composers\Project;

use App\DisplayMode;
use Illuminate\View\View;

class DisplayModesComposer
{

    public function compose(View $view)
    {

        $display_modes = DisplayMode::get();

        return $view->with(compact('display_modes'));
    }

}
