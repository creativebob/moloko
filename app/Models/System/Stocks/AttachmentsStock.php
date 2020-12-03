<?php

namespace App\Models\System\Stocks;

use App\Attachment;

class AttachmentsStock extends CmvStock
{
	const ALIAS = 'attachments_stocks';
    const DEPENDENCE = true;

    public function cmv()
    {
        return $this->belongsTo(Attachment::class);
    }
}
