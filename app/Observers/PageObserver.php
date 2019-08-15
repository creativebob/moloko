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
        $this->setPageSlug($page);
    }

    public function updating(Page $page)
    {
        $this->update($page);
        $this->setPageSlug($page);
    }

    public function deleting(Page $page)
    {
        $this->destroy($page);
    }

    protected function setPageSlug(Page $page)
    {
        $page->slug = \Str::slug($page->title);
    }
}
