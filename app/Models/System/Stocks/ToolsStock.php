<?php

namespace App\Models\System\Stocks;

use App\Tool;

class ToolsStock extends CmvStock
{
	const ALIAS = 'tools_stocks';
    const DEPENDENCE = true;

    public function cmv()
    {
        return $this->belongsTo(Tool::class);
    }
}
