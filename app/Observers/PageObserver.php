<?php

namespace App\Observers;

use App\Page;

use App\Observers\Traits\CommonTrait;

class PageObserver
{
    use CommonTrait;

    public function creating(Page $page)
    {
        $this->store($page);
    }

    public function updating(Page $page)
    {
        $this->update($page);
    }

    public function deleting(Page $page)
    {
        $this->destroy($page);
    }
}
