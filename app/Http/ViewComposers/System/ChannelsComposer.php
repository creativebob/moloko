<?php

namespace App\Http\ViewComposers\System;

use App\Channel;

use Illuminate\View\View;

class ChannelsComposer
{
	public function compose(View $view)
	{

        // Главный запрос
        $channels = Channel::all();

        return $view->with(compact('channels'));
    }

}
