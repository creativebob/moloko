<?php

namespace App\Observers\System;

use App\Rubricator;

use App\Observers\System\Traits\Commonable;

class RubricatorObserver
{
    use Commonable;

    public function creating(Rubricator $rubricator)
    {
        $this->setSlug($rubricator);
        $this->store($rubricator);
    }

    public function updating(Rubricator $rubricator)
    {
        $this->setSlug($rubricator);
        $this->update($rubricator);
    }

    public function deleting(Rubricator $rubricator)
    {
        $this->destroy($rubricator);
    }
}
