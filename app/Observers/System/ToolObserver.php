<?php

namespace App\Observers\System;

use App\Tool;

use App\Observers\System\Traits\Commonable;

class ToolObserver
{
    use Commonable;

    public function creating(Tool $tool)
    {
        $this->store($tool);
    }

    public function updating(Tool $tool)
    {
        $this->update($tool);
    }

    public function deleting(Tool $tool)
    {
        $this->destroy($tool);
    }
}
