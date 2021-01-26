<?php

namespace App\Observers\System;

use App\File;

class FileObserver extends BaseObserver
{
    /**
     * Handle the file "creating" event.
     *
     * @param File $file
     */
    public function creating(File $file)
    {
        $this->store($file);
    }

    /**
     * Handle the file "deleting" event.
     *
     * @param File $file
     */
    public function deleting(File $file)
    {
        $this->destroy($file);
    }
}
