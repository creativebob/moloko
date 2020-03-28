<?php

namespace App\Observers\System;

use App\Room;

use App\Observers\System\Traits\Commonable;

class RoomObserver
{

    use Commonable;

    public function creating(Room $room)
    {
        $this->store($room);
    }

    public function updating(Room $room)
    {
        $this->update($room);
    }

    public function deleting(Room $room)
    {
        $this->destroy($room);
    }
}
