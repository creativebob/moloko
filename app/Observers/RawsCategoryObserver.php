<?php

namespace App\Observers;

use App\RawsCategory;

use App\Observers\Traits\CommonTrait;

class RawsCategoryObserver
{

    use CommonTrait;

    public function creating(RawsCategory $raws_category)
    {
        $this->store($raws_category);
    }

    public function updating(RawsCategory $raws_category)
    {
        $this->update($raws_category);
    }

    public function deleting(RawsCategory $raws_category)
    {
        $this->destroy($raws_category);
    }
}
