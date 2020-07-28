<?php

namespace App\Observers\System;

use App\Department;
use App\Observers\System\Traits\Commonable;

class DepartmentObserver
{

    use Commonable;

    public function creating(Department $department)
    {
        $this->store($department);
    }

    public function updating(Department $department)
    {
        $this->update($department);
    }

    public function deleting(Department $department)
    {
        $this->destroy($department);
    }
}
