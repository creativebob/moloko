<?php

namespace App\Observers;

use App\Room;

use App\Observers\Traits\CommonTrait;

class RoomObserver
{

    use CommonTrait;

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
