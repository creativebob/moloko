<?php

namespace App\Observers;

use App\Direction;

class DirectionObserver
{

    public function creating(Direction $direction)
    {
        $request = request();
        $user = $request->user();
        $direction->company_id = $user->company_id;
        $direction->author_id = hideGod($user);
    }

    public function updating(Direction $direction)
    {
        $request = request();
        $direction->editor_id = hideGod($request->user());
    }
}
