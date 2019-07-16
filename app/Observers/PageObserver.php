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
        $this->setSlug($page);
    }

    public function updating(Page $page)
    {
        $this->update($page);
        $this->setSlug($page);
    }

    public function deleting(Page $page)
    {
        $this->destroy($page);
    }

    protected function setSlug(Page $page)
    {
        $page->slug = \Str::slug($page->title);
    }
}
