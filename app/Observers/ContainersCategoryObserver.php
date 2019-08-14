<?php

namespace App\Observers;

use App\ContainersCategory;

use App\Observers\Traits\CommonTrait;

class ContainersCategoryObserver
{

    use CommonTrait;

    public function creating(ContainersCategory $containers_category)
    {
        $this->store($containers_category);
    }

    public function updating(ContainersCategory $containers_category)
    {
        $this->update($containers_category);
    }

    public function deleting(ContainersCategory $containers_category)
    {
        $this->destroy($containers_category);
    }
}
