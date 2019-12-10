<?php

namespace App\Http\ViewComposers\System;

use App\Notification;
use Illuminate\View\View;

class NotificationsComposer
{
	public function compose(View $view)
	{
        $notifications = Notification::whereHas('sites', function($q) {
            $q->where('sites.id', 1);
        })
            ->get();
        return $view->with(compact('notifications'));
    }

}
