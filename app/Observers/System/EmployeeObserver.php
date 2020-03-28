<?php

namespace App\Observers\System;

use App\Employee;

use App\Observers\System\Traits\Commonable;

class EmployeeObserver
{

    use Commonable;

    public function creating(Employee $employee)
    {
        $this->store($employee);
    }

    public function updating(Employee $employee)
    {
        $this->update($employee);
    }
}
