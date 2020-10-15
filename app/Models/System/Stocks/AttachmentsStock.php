<?php

namespace App\Models\System\Stocks;

use App\Attachment;

class AttachmentsStock extends CmvStock
{
    public function cmv()
    {
        return $this->belongsTo(Attachment::class);
    }
}
