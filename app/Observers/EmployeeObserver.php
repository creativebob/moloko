<?php

namespace App\Observers;

use App\Employee;

use App\Observers\Traits\Commonable;

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

    public function deleting(Employee $employee)
    {
        $this->destroy($employee);
    }
}
