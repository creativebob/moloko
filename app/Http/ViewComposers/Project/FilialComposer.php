<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class FilialComposer
{

    public function compose(View $view)
    {
        $filial = $view->site->filial;
        return $view->with(compact('filial'));
    }

}
