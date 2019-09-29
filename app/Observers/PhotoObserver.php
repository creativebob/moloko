<?php

namespace App\Observers;

use App\Photo;
use Illuminate\Support\Facades\Storage;

use App\Observers\Traits\Commonable;

class PhotoObserver
{

    use Commonable;

    public function creating(Photo $photo)
    {
        $this->store($photo);
    }

    public function updating(Photo $photo)
    {
        $this->update($photo);
    }

    public function deleting(Photo $photo)
    {
        $this->destroy($photo);
    }


}
