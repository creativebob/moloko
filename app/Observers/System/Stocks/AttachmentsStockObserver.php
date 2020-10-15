<?php

namespace App\Observers\System\Stocks;

use App\Models\System\Stocks\AttachmentsStock;

class AttachmentsStockObserver extends CmvStockObserver
{

    public function creating(AttachmentsStock $attachmentsStock)
    {
        $this->store($attachmentsStock);
    }

    public function updating(AttachmentsStock $attachmentsStock)
    {
        $this->update($attachmentsStock);
        $this->setFree($attachmentsStock);
    }

    public function deleting(AttachmentsStock $attachmentsStock)
    {
        $this->destroy($attachmentsStock);
    }
}
