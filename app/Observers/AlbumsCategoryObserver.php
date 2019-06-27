<?php

namespace App\Observers;

use App\AlbumsCategory;

use App\Observers\Traits\CommonTrait;

class AlbumsCategoryObserver
{

    use CommonTrait;

    public function creating(AlbumsCategory $albums_category)
    {
        $this->store($albums_category);
    }

    public function updating(AlbumsCategory $albums_category)
    {
        $this->update($albums_category);
    }

    public function deleting(AlbumsCategory $albums_category)
    {
        $this->destroy($albums_category);
    }
}
