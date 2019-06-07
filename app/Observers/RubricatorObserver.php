<?php

namespace App\Observers;

use App\Rubricator;

use App\Observers\Traits\CommonTrait;

class RubricatorObserver
{
    use CommonTrait;

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
