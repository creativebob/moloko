<?php

namespace App\Observers\System;

use App\Employee;

class EmployeeObserver extends BaseObserver
{

    public function creating(Employee $employee)
    {
        $this->store($employee);
    }

    public function updating(Employee $employee)
    {
        $this->update($employee);
    }
}
