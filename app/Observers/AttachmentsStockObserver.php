<?php

namespace App\Observers;

use App\AttachmentsStock;
use App\Observers\Traits\Commonable;

class AttachmentsStockObserver
{

    use Commonable;

    public function creating(AttachmentsStock $attachments_stock)
    {
        $this->store($attachments_stock);
    }

    public function updating(AttachmentsStock $attachments_stock)
    {
        $this->update($attachments_stock);
    }

    public function deleting(AttachmentsStock $attachments_stock)
    {
        $this->destroy($attachments_stock);
    }

}
