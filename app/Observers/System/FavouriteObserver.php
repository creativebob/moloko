<?php

namespace App\Observers\System;

use App\Favourite;

use App\Observers\System\Traits\Commonable;

class FavouriteObserver
{

    use Commonable;

    public function creating(Favourite $favourite)
    {
        $this->store($favourite);
    }

    public function updating(Favourite $favourite)
    {
        $this->update($favourite);
    }

    public function deleting(Favourite $favourite)
    {
        $this->destroy($favourite);
    }
}
