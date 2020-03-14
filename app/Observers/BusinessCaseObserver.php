<?php

namespace App\Observers;

use App\Observers\Traits\Commonable;
use App\BusinessCase;

class BusinessCaseObserver
{

    use Commonable;

    public function creating(BusinessCase $business_case)
    {
        $this->store($business_case);
    }

    public function updating(BusinessCase $business_case)
    {
        $this->update($business_case);
    }

    public function deleting(BusinessCase $business_case)
    {
        $this->destroy($business_case);
    }
}
