<?php

namespace App\Observers\System;

use App\Attachment;

use App\Observers\System\Traits\Commonable;

class AttachmentObserver
{

    use Commonable;

    public function creating(Attachment $attachment)
    {
        $this->store($attachment);
    }

    public function updating(Attachment $attachment)
    {
        $this->update($attachment);
    }

    public function deleting(Attachment $attachment)
    {
        $this->destroy($attachment);
    }
}
