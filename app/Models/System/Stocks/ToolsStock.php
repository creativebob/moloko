<?php

namespace App\Models\System\Stocks;

use App\Tool;

class ToolsStock extends CmvStock
{
    public function cmv()
    {
        return $this->belongsTo(Tool::class);
    }
}
